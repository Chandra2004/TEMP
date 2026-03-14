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
use TheFramework\Models\Role;
use TheFramework\Models\User;

class RegistrationController extends DashboardController
{
    protected Registration $registration;
    protected Payment $payment;

    public function __construct()
    {
        parent::__construct();
        $this->registration = new Registration();
        $this->payment = new Payment();
    }

    public function registration()
    {
        $events = Event::query()
            ->select(['id', 'uid', 'nama_event', 'tipe_event', 'biaya_event', 'status_event', 'kuota_peserta'])
            ->with(['registrations.user', 'registrations.payment'])
            ->withCount(['registrations'])
            ->orderBy('tanggal_event', 'DESC')
            ->all();

        $users = User::query()->select(['id_user', 'uid', 'nama_lengkap', 'email'])->where('uid_role', '=', Role::where('nama_role', 'member')->first()->uid)->all();

        // return Helper::json([
        //     'events' => $events,
        //     'users' => $users,
        // ]);

        return View::render('dashboard.general.registration', array_merge($this->dataTetap, [
            'title' => 'Manajemen Pendaftaran ' . Helper::session_get("user")['nama_role'] . ' | Khafid Swimming Club (KSC) - Official Website',
            'events' => $events,
            'users' => $users,
        ]));
    }

    public function registrationHistory()
    {
        $userSession = Helper::session_get('user');

        // Ambil pendaftaran milik user ini
        $registrations = Registration::query()
            ->where('uid_user', '=', $userSession['uid'])
            ->with(['event.category', 'payment'])
            ->orderBy('tanggal_registrasi', 'DESC')
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

        $fallbackUrl = $event ? "/detail-event/{$event['slug']}/{$event['uid']}" : '/events';
        $redirectUrl = Helper::previous($fallbackUrl);

        if ($user == null || $event == null) {
            return Helper::redirect(Helper::previous('/events'), 'warning', 'Kredensial pengguna yang anda daftarkan tidak tersedia', 10);
        }

        $existingRegistration = Registration::query()->where('uid_user', '=', $user->uid)->where('uid_event', '=', $event->uid)->first();
        if ($existingRegistration) {
            return Helper::redirect($redirectUrl, 'warning', 'Anda sudah melakukan pendaftaran untuk event ini.', 10);
        }

        try {
            if ($event['kuota_peserta'] > 0) {
                if (Registration::query()->where('uid_event', '=', $event['uid'])->count() >= $event['kuota_peserta']) {
                    throw new Exception('Kuota pendaftaran untuk event ini sudah penuh.');
                }
            }

            if ($request->hasFile('bukti_pembayaran')) {
                $photoBukti = UploadHandler::handleUploadToWebP($request->file('bukti_pembayaran'), '/bukti-pembayaran', 'payment_proof_');
                if (UploadHandler::isError($photoBukti)) {
                    throw new Exception(UploadHandler::getErrorMessage($photoBukti));
                }
            }

            $dataRegistration = $request->validated();
            $dataRegistration['uid'] = Helper::uuid();
            $dataRegistration['nomor_pendaftaran'] = Helper::generateSecureRegNumber('REG');
            $dataRegistration['uid_user'] = $user['uid'];
            $dataRegistration['uid_event'] = $event['uid'];
            $dataRegistration['status'] = 'menunggu';
            $dataRegistration['tanggal_registrasi'] = Carbon::now(Config::get('DB_TIMEZONE'))->toDateTimeString();
            unset($dataRegistration['metode_pembayaran']);
            unset($dataRegistration['bukti_pembayaran']);

            $this->registration->addRegistration($dataRegistration);

            if ($event['tipe_event'] === 'berbayar') {
                if ($photoBukti == null) {
                    throw new Exception('Bukti pembayaran wajib diunggah untuk event berbayar.');
                }

                $dataPayment['uid'] = Helper::uuid();
                $dataPayment['uid_registration'] = $dataRegistration['uid'];
                $dataPayment['status_pembayaran'] = 'menunggu';
                $dataPayment['tanggal_pembayaran'] = $dataRegistration['tanggal_registrasi'];
                $dataPayment['metode_pembayaran'] = $request->input('metode_pembayaran');
                $dataPayment['bukti_pembayaran'] = $photoBukti;

                $this->payment->addPayment($dataPayment);
            }

            return Helper::redirect($redirectUrl, 'success', 'Pendaftaran berhasil, tunggu verifikasi.', 10);
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

            $registration = Registration::where('uid', $uidRegistration)->first();
            if (!$registration) {
                return Helper::redirect(Helper::previous(), 'error', 'Pendaftaran tidak ditemukan.', 10);
            }

            $event = Event::where('uid', $registration['uid_event'])->first();
            if (!$event) {
                return Helper::redirect(Helper::previous(), 'error', 'Event terkait tidak ditemukan.', 10);
            }

            $registrationUpdateData = [
                'status' => $validatedData['status'] ?? $registration['status'],
                'catatan_admin' => $validatedData['catatan_admin'] ?? $registration['catatan_admin'],
            ];

            // Jika status diubah menjadi 'diterima', hapus catatan admin
            if (isset($registrationUpdateData['status']) && $registrationUpdateData['status'] === 'diterima') {
                $registrationUpdateData['catatan_admin'] = null;
            }

            $this->registration->updateRegistration($registrationUpdateData, $registration->uid);

            if ($event['tipe_event'] === 'berbayar' && isset($validatedData['status_pembayaran'])) {
                $paymentRecord = Payment::where('uid_registration', $uidRegistration)->first();

                if (!$paymentRecord) {
                    throw new Exception('Record pembayaran tidak ditemukan untuk pendaftaran ini.');
                }

                $paymentUpdateData = [
                    'status_pembayaran' => $validatedData['status_pembayaran'] ?? $paymentRecord['status_pembayaran'],
                ];

                $this->payment->updatePayment($paymentUpdateData, $paymentRecord->uid);
            }

            return Helper::redirect(Helper::previous(), 'success', 'Pendaftaran berhasil diperbarui.', 10);
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

            $registration = Registration::where('uid', $uidRegistration)->first();
            if (!$registration) {
                return Helper::redirect(Helper::previous(), 'error', 'Pendaftaran tidak ditemukan.', 10);
            }

            $event = Event::where('uid', $registration['uid_event'])->first();
            if (!$event) {
                return Helper::redirect(Helper::previous(), 'error', 'Event terkait tidak ditemukan.', 10);
            }

            if ($event['tipe_event'] === 'berbayar') {
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
