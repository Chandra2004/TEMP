<?php

namespace TheFramework\Http\Controllers;

use Carbon\Carbon;
use Exception;
use TheFramework\App\Config;
use TheFramework\App\View;
use TheFramework\Config\UploadHandler;
use TheFramework\Helpers\Helper;
use TheFramework\Http\Controllers\Services\ErrorController;
use TheFramework\Http\Requests\RegistrationRequest;
use TheFramework\Models\Event;
use TheFramework\Models\Payment;
use TheFramework\Models\Registration;
use TheFramework\Models\Schedule;
use TheFramework\Models\User;

class RegistrationController extends DashboardController
{
    protected Registration $registration;
    protected Payment $payment;
    protected Schedule $schedule;

    public function __construct()
    {
        parent::__construct();
        $this->registration = new Registration();
        $this->payment = new Payment();
        $this->schedule = new Schedule();
    }

    public function registration()
    {
        $registrations = Registration::query()
            ->with([
                'user', 
                'payment', 
                'schedule',
                'eventCategory.category', 
                'eventCategory.event'
            ])
            ->orderBy('created_at', 'DESC')
            ->all();

        // Ambil data event untuk filter atau pendaftaran manual
        $events = Event::query()
            ->select(['id', 'uid', 'nama_event', 'tipe_event', 'biaya_event', 'status_event', 'kuota_peserta'])
            ->all();

        $users = User::query()
            ->select([
                'users.id', 
                'users.uid', 
                'data_users.nama_lengkap', 
                'users.email'
            ])
            ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
            ->all(); // Ambil semua user dengan nama lengkap untuk pendaftaran manual

        return View::render('dashboard.general.registration', array_merge($this->dataTetap, [
            'title' => 'Manajemen Pendaftaran ' . Helper::session_get("user")['nama_role'] . ' | Khafid Swimming Club (KSC) - Official Website',
            'registrations' => $registrations,
            'events' => $events,
            'users' => $users
        ]));
    }

    public function registrationHistory()
    {
        $userSession = Helper::session_get('user');

        // Ambil pendaftaran milik user ini
        $registrations = Registration::query()
            ->where('uid_user', '=', $userSession['uid'])
            ->with(['eventCategory.event', 'eventCategory.category', 'payment'])
            ->orderBy('created_at', 'DESC')
            ->all();

        return View::render('dashboard.member.registration-history', array_merge($this->dataTetap, [
            'title' => 'Riwayat Pendaftaran | Khafid Swimming Club (KSC) - Official Website',
            'registrations' => $registrations
        ]));
    }

    public function registrationCreateProcess(RegistrationRequest $request)
    {
        $photoBukti = null;
        $event = Event::where('uid', $request->input('uid_event'))->first();
        $user = User::where('uid', $request->input('uid_user'))->first();
        $uidCategories = $request->input('uid_event_category');

        $fallbackUrl = $event ? "/detail-event/{$event['slug']}/{$event['uid']}" : '/events';
        $redirectUrl = Helper::previous($fallbackUrl);

        if ($user == null || $event == null || empty($uidCategories)) {
            return Helper::redirect($redirectUrl, 'warning', 'Data pendaftaran tidak lengkap.', 10);
        }

        // Handle string input to array for consistency
        if (!is_array($uidCategories)) {
            $uidCategories = [$uidCategories];
        }

        try {
            // Handle File Upload Once
            if ($request->hasFile('bukti_pembayaran')) {
                $photoBukti = UploadHandler::handleUploadToWebP($request->file('bukti_pembayaran'), '/bukti-pembayaran', 'payment_proof_');
                if (UploadHandler::isError($photoBukti)) {
                    throw new Exception(UploadHandler::getErrorMessage($photoBukti));
                }
            }

            $totalBayar = (float) ($request->input('total_bayar') ?? 0);
            
            if ($totalBayar > 0 && $photoBukti == null) {
                throw new Exception('Bukti pembayaran wajib diunggah untuk pendaftaran berbayar.');
            }

            $countRegistration = 0;
            foreach ($uidCategories as $idx => $uidCategory) {
                // Cek pendaftaran ganda pada kategori yang sama
                $existingRegistration = Registration::query()
                    ->where('uid_user', '=', $user->uid)
                    ->where('uid_event_category', '=', $uidCategory)
                    ->first();

                if ($existingRegistration) {
                    continue; 
                }

                // Check Capacity (New Logic: Seri x Lintasan)
                $eventCategory = \TheFramework\Models\EventCategory::query()->where('uid', '=', $uidCategory)->first();
                $maxSeri = (int)($eventCategory['jumlah_seri'] ?? 1);
                $maxLanes = (int)($event['jumlah_lintasan'] ?? 8);
                $totalCapacity = $maxSeri * $maxLanes;

                $currentCount = Registration::query()
                    ->where('uid_event_category', '=', $uidCategory)
                    ->count();

                if ($currentCount >= $totalCapacity) {
                    throw new Exception("Lomba " . ($eventCategory['nama_acara'] ?: 'tersebut') . " sudah penuh. (Kapasitas: {$totalCapacity} atlet)");
                }

                $dataRegistration = [];
                $dataRegistration['uid'] = Helper::uuid();
                $dataRegistration['nomor_pendaftaran'] = Helper::generateSecureRegNumber('REG') . '-' . ($idx + 1);
                $dataRegistration['uid_user'] = $user['uid'];
                $dataRegistration['uid_event_category'] = $uidCategory;
                $dataRegistration['status_pendaftaran'] = 'pending';
                $dataRegistration['created_at'] = Carbon::now(Config::get('DB_TIMEZONE'))->toDateTimeString();
                
                $this->registration->addRegistration($dataRegistration);

                // logic slot otomatis (Seri & Lintasan)
                $slot = Schedule::findFirstAvailableSlot($uidCategory, $maxLanes, $maxSeri);

                $dataSchedule = [
                    'uid' => Helper::uuid(),
                    'uid_registration' => $dataRegistration['uid'],
                    'nomor_seri' => $slot['nomor_seri'],
                    'nomor_lintasan' => $slot['nomor_lintasan'],
                    'created_at' => $dataRegistration['created_at'],
                    'updated_at' => $dataRegistration['created_at']
                ];
                $this->schedule->addSchedule($dataSchedule);

                // Create Payment Record (Always created for auditing, status depends on amount)
                $dataPayment = [];
                $dataPayment['uid'] = Helper::uuid();
                $dataPayment['uid_registration'] = $dataRegistration['uid'];
                $dataPayment['status_pembayaran'] = ($totalBayar > 0) ? 'pending' : 'selesai';
                $dataPayment['tanggal_pembayaran'] = $dataRegistration['created_at'];
                $dataPayment['payment_method'] = ($totalBayar > 0) ? ($request->input('metode_pembayaran') ?? 'Transfer') : 'Gratis';
                $dataPayment['amount'] = ($totalBayar > 0) ? $totalBayar : 0;
                $dataPayment['bukti_pembayaran'] = $photoBukti;

                (new Payment())->addPayment($dataPayment);
                
                $countRegistration++;
            }

            if ($countRegistration === 0) {
                return Helper::redirect($redirectUrl, 'warning', 'Anda sudah terdaftar di semua kategori pilihan.', 10);
            }

            return Helper::redirect($redirectUrl, 'success', "Berhasil mendaftar di {$countRegistration} kategori lomba.", 10);
        } catch (Exception $e) {
            if ($photoBukti) {
                UploadHandler::delete($photoBukti, '/bukti-pembayaran');
            }

            return Helper::redirect($redirectUrl, 'error', 'Terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }

    public function registrationEditProcess($role, $uidUser, $uidRegistration, RegistrationRequest $request)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $validatedData = $request->validated();
            $registration = Registration::with(['eventCategory.event', 'payment'])->where('uid', '=', $uidRegistration)->first();
            
            if (!$registration) {
                return Helper::redirect(Helper::previous(), 'error', 'Pendaftaran tidak ditemukan.', 10);
            }

            $status = $request->input('status_pendaftaran');
            $catatan = $request->input('catatan');

            // Logic Sinkronisasi Otomatis
            $registrationUpdateData = [
                'status_pendaftaran' => $status,
                'catatan' => ($status === 'diterima') ? null : $catatan,
            ];

            $this->registration->updateRegistration($registrationUpdateData, $registration->uid);

            // Jika ada pembayaran, sinkronkan berdasarkan status pendaftaran
            if ($registration->payment) {
                $paymentUpdateData = [
                    'status_pembayaran' => ($status === 'diterima') ? 'selesai' : (($status === 'ditolak') ? 'ditolak' : 'pending'),
                ];
                
                // Jika diterima, catat waktu pembayaran jika belum ada
                if ($status === 'diterima' && empty($registration->payment->tanggal_pembayaran)) {
                    $paymentUpdateData['tanggal_pembayaran'] = Carbon::now();
                }

                $this->payment->updatePayment($paymentUpdateData, $registration->payment->uid);
            }

            return Helper::redirect(Helper::previous(), 'success', 'Pendaftaran atlet berhasil diverifikasi.', 10);
        } catch (Exception $e) {
            return Helper::redirect(Helper::previous(), 'error', 'Terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }

    public function registrationDeleteProcess($role, $uidUser, $uidRegistration)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $registration = Registration::with(['eventCategory', 'eventCategory.event'])->where('uid', '=', $uidRegistration)->first();
            if (!$registration) {
                return Helper::redirect(Helper::previous(), 'error', 'Pendaftaran tidak ditemukan.', 10);
            }

            $event = $registration->eventCategory->event ?? null;

            if ($event && $event['tipe_event'] === 'berbayar') {
                $paymentRecord = Payment::where('uid_registration', $uidRegistration)->first();

                if ($paymentRecord) {
                    if ($paymentRecord['bukti_pembayaran']) {
                        UploadHandler::delete($paymentRecord['bukti_pembayaran'], '/bukti-pembayaran');
                    }
                    $this->payment->deletePayment($paymentRecord->uid);
                }
            }

            $status = $this->registration->deleteRegistration($uidRegistration);

            if ($status === false) {
                return Helper::redirect(Helper::previous(), 'error', 'Pendaftaran gagal dihapus.', 10);
            }

            return Helper::redirect(Helper::previous(), 'success', 'Pendaftaran berhasil dihapus.', 10);
        } catch (Exception $e) {
            return Helper::redirect(Helper::previous(), 'error', 'Terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }
}
