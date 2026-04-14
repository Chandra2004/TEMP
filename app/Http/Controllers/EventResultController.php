<?php

namespace TheFramework\Http\Controllers;

use Exception;
use TheFramework\App\View;
use TheFramework\Helpers\Helper;
use TheFramework\Models\Event;
use TheFramework\Models\EventCategory;
use TheFramework\Models\Registration;
use TheFramework\Models\Result;
use TheFramework\Models\User;
use TheFramework\Http\Controllers\Services\ErrorController;

class EventResultController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Tampilkan daftar event untuk dikelola hasilnya
     */
    public function index($role)
    {
        $userSession = Helper::session_get('user');
        if (User::authorizeAction($role, $userSession['uid']) === false) {
            ErrorController::error403();
        }

        $events = Event::query()->orderBy('tanggal_mulai', 'DESC')->all();

        return View::render('dashboard.general.result.index', array_merge($this->dataTetap, [
            'title' => 'Manajemen Hasil Lomba | Khafid Swimming Club (KSC)',
            'events' => $events
        ]));
    }

    /**
     * Tampilkan daftar nomor acara (kategori) dalam suatu event
     */
    public function categoryList($role, $uidEvent)
    {
        $userSession = Helper::session_get('user');
        if (User::authorizeAction($role, $userSession['uid']) === false) {
            ErrorController::error403();
        }

        $event = Event::where('uid', $uidEvent)->first();
        if (!$event) ErrorController::error404();

        $categories = EventCategory::query()
            ->with(['category'])
            ->where('uid_event', '=', $uidEvent)
            ->orderBy('nomor_acara', 'ASC')
            ->all();

        return View::render('dashboard.general.result.categories', array_merge($this->dataTetap, [
            'title' => 'Pilih Nomor Acara - ' . $event['nama_event'],
            'event' => $event,
            'categories' => $categories
        ]));
    }

    /**
     * Form input hasil untuk semua atlet di satu kategori
     */
    public function inputForm($role, $uidEvent, $uidEventCategory)
    {
        $userSession = Helper::session_get('user');
        if (User::authorizeAction($role, $userSession['uid']) === false) {
            ErrorController::error403();
        }

        $eventCategory = EventCategory::query()
            ->with(['event', 'category'])
            ->where('uid', '=', $uidEventCategory)
            ->first();

        if (!$eventCategory) ErrorController::error404();

        // Ambil atlet yang terdaftar dan diterima pembayarannya/pendaftarannya
        $athletes = Registration::query()
            ->select([
                'registrations.uid',
                'data_users.nama_lengkap',
                'data_users.klub_renang',
                'results.waktu_akhir',
                'results.status',
                'results.peringkat'
            ])
            ->join('users', 'registrations.uid_user', '=', 'users.uid')
            ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
            ->leftJoin('results', 'registrations.uid', '=', 'results.uid_registration')
            ->where('registrations.uid_event_category', '=', $uidEventCategory)
            ->where('registrations.status_pendaftaran', '=', 'diterima')
            ->orderBy('data_users.nama_lengkap', 'ASC')
            ->all();

        return View::render('dashboard.general.result.input', array_merge($this->dataTetap, [
            'title' => 'Input Hasil: ' . $eventCategory['nama_acara'],
            'eventCategory' => $eventCategory,
            'athletes' => $athletes
        ]));
    }

    /**
     * Simpan hasil lomba
     */
    public function store($role, $uidEvent, $uidEventCategory)
    {
        try {
            $userSession = Helper::session_get('user');
            if (User::authorizeAction($role, $userSession['uid']) === false) {
                ErrorController::error403();
            }

            $resultsData = $_POST['results'] ?? [];

            foreach ($resultsData as $uidReg => $data) {
                $status = $data['status'] ?? 'FINISH';
                $waktuStr = '00:00.00';
                $totalMs = 0;

                if ($status === 'FINISH') {
                    $min = str_pad($data['min'] ?? '0', 2, '0', STR_PAD_LEFT);
                    $sec = str_pad($data['sec'] ?? '0', 2, '0', STR_PAD_LEFT);
                    $ms = str_pad($data['ms'] ?? '0', 2, '0', STR_PAD_LEFT);
                    
                    $waktuStr = "{$min}:{$sec}.{$ms}";
                    $totalMs = ((int)$min * 60 * 1000) + ((int)$sec * 1000) + ((int)$ms * 10);
                }

                $existingResult = Result::where('uid_registration', $uidReg)->first();

                $dbData = [
                    'uid_registration' => $uidReg,
                    'waktu_akhir' => ($status === 'FINISH') ? $waktuStr : null,
                    'total_milliseconds' => ($status === 'FINISH') ? $totalMs : 99999999, // Beri angka besar agar tidak jadi Best Time
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($existingResult) {
                    Result::query()->where('uid', '=', $existingResult['uid'])->update($dbData);
                } else {
                    $dbData['uid'] = Helper::uuid();
                    $dbData['created_at'] = date('Y-m-d H:i:s');
                    Result::query()->insert($dbData);
                }
            }

            return Helper::redirect(Helper::previous(), 'success', 'Hasil lomba berhasil disimpan.', 10);
        } catch (Exception $e) {
            return Helper::redirect(Helper::previous(), 'error', 'Gagal menyimpan hasil: ' . $e->getMessage(), 10);
        }
    }
}
