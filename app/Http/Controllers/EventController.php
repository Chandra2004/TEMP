<?php

namespace TheFramework\Http\Controllers;

use TheFramework\App\View;
use TheFramework\Http\Requests\EventRequest;
use TheFramework\Helpers\Helper;
use TheFramework\Config\UploadHandler;
use TheFramework\Models\Event;
use TheFramework\Models\EventCategory;
use TheFramework\Models\PaymentMethod;
use TheFramework\Models\User;
use TheFramework\Http\Controllers\Services\ErrorController;

class EventController extends Controller
{
    private $event;

    public function __construct()
    {
        parent::__construct();
        $this->event = new Event();
    }

    public function event($role, $page = 1)
    {
        $userSession = Helper::session_get('user');
        $uidUser = $userSession['uid'];

        if (User::authorizeAction($role, $uidUser) === false) {
            ErrorController::error403();
        }

        $events = Event::with(['eventCategories', 'eventCategories.requirements'])->paginate(10, $page);
        $categories = \TheFramework\Models\Category::query()->all();
        $authors = User::query()
            ->select(['users.*', 'roles.name as nama_role', 'data_users.nama_lengkap'])
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
            ->where('roles.name', '=', 'admin')
            ->all();
        $paymentMethods = PaymentMethod::query()->all();
        $totalUnreadNotification = \TheFramework\Models\Notification::query()
            ->where('is_read', '=', 0)
            ->where('uid_user', '=', $uidUser)
            ->count();
        $unReadNotification = \TheFramework\Models\Notification::query()
            ->where('is_read', '=', 0)
            ->where('uid_user', '=', $uidUser)
            ->all();

        $user = User::query()
            ->select(['users.*', 'roles.name as nama_role', 'data_users.nama_lengkap', 'data_users.foto_profil'])
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
            ->where('users.uid', '=', $uidUser)
            ->first();

        if (!$user) {
            return Helper::redirect('/logout', 'error', 'Sesi tidak valid, silakan login kembali.', 3);
        }

        return View::render('dashboard.general.event', [
            'user' => $user,
            'events' => $events,
            'categories' => $categories,
            'authors' => $authors,
            'payment_methods' => $paymentMethods,
            'totalUnreadNotification' => $totalUnreadNotification,
            'unReadNotification' => $unReadNotification,
            'notification' => Helper::get_flash('notification'),
            'title' => 'Manajemen Event | Khafid Swimming Club (KSC) - Official Website',
        ]);
    }

    public function eventCreateProcess($role, $uidUser, EventRequest $request)
    {
        $newPhoto = null;
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            if ($request->hasFile('banner_event')) {
                $newPhoto = UploadHandler::handleUploadToWebP($request->file('banner_event'), '/banner-event', 'event_');
                if (UploadHandler::isError($newPhoto)) {
                    throw new \Exception(UploadHandler::getErrorMessage($newPhoto));
                }
            }

            $data = $request->validated();
            $data['uid'] = Helper::uuid();
            $data['slug'] = Helper::slugify($data['nama_event']);
            $data['banner_event'] = $newPhoto;

            $matches = $data['matches'] ?? [];
            unset($data['matches']);

            // Manual Validation for Matches
            foreach ($matches as $match) {
                if (empty($match['uid_category'])) {
                    throw new \Exception("Gagal Validasi: kategori lomba wajib diisi.");
                }
                if (empty($match['waktu_mulai'])) {
                    throw new \Exception("Gagal Validasi: waktu mulai lomba wajib diisi.");
                }
            }

            $eventExists = Event::query()->where('slug', '=', $data['slug'])->first();
            if ($eventExists) {
                throw new \Exception("Event dengan nama tersebut sudah ada.");
            }

            $eventCreated = Event::query()->insert($data);
            if ($eventCreated) {
                foreach ($matches as $match) {
                    $matchUid = Helper::uuid();
                    EventCategory::query()->insert([
                        'uid' => $matchUid,
                        'uid_event' => $data['uid'],
                        'uid_category' => $match['uid_category'],
                        'nama_acara' => $match['nama_acara'] ?? '',
                        'tipe_biaya' => $match['tipe_biaya'] ?? 'gratis',
                        'biaya_pendaftaran' => ($match['tipe_biaya'] ?? '') == 'berbayar' ? ($match['biaya_pendaftaran'] ?? 0) : 0,
                        'waktu_mulai' => $match['waktu_mulai'] ?? '08:00',
                        'jumlah_seri' => $match['jumlah_seri'] ?? 1,
                    ]);

                    if (isset($match['requirements']) && is_array($match['requirements'])) {
                        foreach ($match['requirements'] as $req) {
                            if (!empty($req['parameter_name'])) {
                                \TheFramework\Models\CategoryRequirement::query()->insert([
                                    'uid' => Helper::uuid(),
                                    'uid_event_category' => $matchUid,
                                    'parameter_name' => $req['parameter_name'],
                                    'parameter_value' => $req['parameter_value'] ?? '',
                                    'operator' => $req['operator'] ?? '=',
                                    'parameter_type' => 'string',
                                    'is_required' => 1
                                ]);
                            }
                        }
                    }
                }
            }

            return Helper::redirect("/{$role}/dashboard/management-event", 'success', "Event {$data['nama_event']} berhasil ditambahkan", 10);
        } catch (\Exception $e) {
            if ($newPhoto) {
                UploadHandler::delete($newPhoto, '/banner-event');
            }
            return Helper::redirect("/{$role}/dashboard/management-event", 'error', 'Terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }

    public function eventEditProcess($role, $uidUser, $uidEvent, EventRequest $request)
    {
        $newPhoto = null;
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $eventRecord = Event::where('uid', $uidEvent)->first();
            if (!$eventRecord) {
                ErrorController::error403();
            }

            $data = $request->validated();
            $data['slug'] = Helper::slugify($data['nama_event'] ?? '');

            if ($request->hasFile('banner_event')) {
                $newPhoto = UploadHandler::handleUploadToWebP($request->file('banner_event'), '/banner-event', 'event_');
                if (UploadHandler::isError($newPhoto)) {
                    throw new \Exception(UploadHandler::getErrorMessage($newPhoto));
                }
                if ($eventRecord['banner_event']) {
                    UploadHandler::delete($eventRecord['banner_event'], '/banner-event');
                }
                $data['banner_event'] = $newPhoto;
            } else {
                $data['banner_event'] = $eventRecord['banner_event'];
            }

            $matches = $data['matches'] ?? [];
            unset($data['matches']);

            // Manual Validation for Matches
            foreach ($matches as $match) {
                if (empty($match['uid_category'])) {
                    throw new \Exception("Gagal Validasi: kategori lomba wajib diisi.");
                }
                if (empty($match['waktu_mulai'])) {
                    throw new \Exception("Gagal Validasi: waktu mulai lomba wajib diisi.");
                }
            }

            $duplicateSlug = Event::query()
                ->where('slug', '=', $data['slug'])
                ->where('uid', '!=', $uidEvent)
                ->first();

            if ($duplicateSlug) {
                throw new \Exception("Event dengan nama tersebut sudah terdaftar.");
            }

            Event::query()->where('uid', '=', $uidEvent)->update($data);

            // Sync matches
            $existingMatches = EventCategory::query()->where('uid_event', '=', $uidEvent)->all();
            foreach ($existingMatches as $exMatch) {
                \TheFramework\Models\CategoryRequirement::query()->where('uid_event_category', '=', $exMatch['uid'])->delete();
                EventCategory::query()->where('uid', '=', $exMatch['uid'])->delete();
            }

            foreach ($matches as $match) {
                $matchUid = Helper::uuid();
                EventCategory::query()->insert([
                    'uid' => $matchUid,
                    'uid_event' => $uidEvent,
                    'uid_category' => $match['uid_category'],
                    'nama_acara' => $match['nama_acara'] ?? '',
                    'tipe_biaya' => $match['tipe_biaya'] ?? 'gratis',
                    'biaya_pendaftaran' => ($match['tipe_biaya'] ?? '') == 'berbayar' ? ($match['biaya_pendaftaran'] ?? 0) : 0,
                    'waktu_mulai' => $match['waktu_mulai'] ?? '08:00',
                    'jumlah_seri' => $match['jumlah_seri'] ?? 1,
                ]);

                if (isset($match['requirements']) && is_array($match['requirements'])) {
                    foreach ($match['requirements'] as $req) {
                        if (!empty($req['parameter_name'])) {
                            \TheFramework\Models\CategoryRequirement::query()->insert([
                                'uid' => Helper::uuid(),
                                'uid_event_category' => $matchUid,
                                'parameter_name' => $req['parameter_name'],
                                'parameter_value' => $req['parameter_value'] ?? '',
                                'operator' => $req['operator'] ?? '=',
                                'parameter_type' => 'string',
                                'is_required' => 1
                            ]);
                        }
                    }
                }
            }

            return Helper::redirect("/{$role}/dashboard/management-event", 'success', "Event {$data['nama_event']} berhasil diperbarui", 10);
        } catch (\Exception $e) {
            if ($newPhoto) {
                UploadHandler::delete($newPhoto, '/banner-event');
            }
            return Helper::redirect("/{$role}/dashboard/management-event", 'error', 'Terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }

    public function eventDeleteProcess($role, $uidUser, $uidEvent)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $event = Event::where('uid', $uidEvent)->first();
            if (!$event) {
                ErrorController::error404();
            }

            if ($event['banner_event']) {
                UploadHandler::delete($event['banner_event'], '/banner-event');
            }

            $matches = EventCategory::query()->where('uid_event', '=', $uidEvent)->all();
            foreach ($matches as $match) {
                \TheFramework\Models\CategoryRequirement::query()->where('uid_event_category', '=', $match['uid'])->delete();
                EventCategory::query()->where('uid', '=', $match['uid'])->delete();
            }

            Event::query()->where('uid', '=', $uidEvent)->delete();

            return Helper::redirect("/{$role}/dashboard/management-event", 'success', "Event berhasil dihapus", 10);
        } catch (\Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-event", 'error', 'Gagal menghapus event: ' . $e->getMessage(), 10);
        }
    }

    public function exportBukuAcara($role, $uidUser, $uidEvent)
    {
        if ($uidEvent === 'all') {
            $events = Event::query()->orderBy('tanggal_mulai', 'DESC')->all();
        } else {
            $eventFound = Event::where('uid', $uidEvent)->first();
            if (!$eventFound) {
                return Helper::redirect(Helper::previous(), 'error', 'Event tidak ditemukan.');
            }
            $events = [$eventFound];
        }

        $globalData = [];

        foreach ($events as $e) {
            // Ambil kategori lomba yang ada di event ini (Acara 101, 102, dst)
            $eventCategories = \TheFramework\Models\EventCategory::query()
                ->with(['category'])
                ->where('uid_event', '=', $e['uid'])
                ->orderBy('nomor_acara', 'ASC')
                ->all();

            $dataAcara = [];
            foreach ($eventCategories as $ec) {
                $acaraItem = [
                    'nomor_acara' => $ec['nomor_acara'],
                    'nama_acara'  => $ec['nama_acara'],
                    'kode_ku'     => $ec['category']['kode_ku'] ?? 'UMUM',
                    'seri'        => []
                ];

                $registrationsRaw = \TheFramework\Models\Registration::query()
                    ->select([
                        'registrations.uid',
                        'registrations.uid_user',
                        'data_users.nama_lengkap',
                        'data_users.tanggal_lahir',
                        'data_users.klub_renang',
                        'registrations.seed_time',
                        'schedules.nomor_seri',
                        'schedules.nomor_lintasan'
                    ])
                    ->join('users', 'registrations.uid_user', '=', 'users.uid')
                    ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                    ->join('schedules', 'registrations.uid', '=', 'schedules.uid_registration')
                    ->where('registrations.uid_event_category', '=', $ec['uid'])
                    ->orderBy('schedules.nomor_seri', 'ASC')
                    ->orderBy('schedules.nomor_lintasan', 'ASC')
                    ->all();

                // Filter unik per atlet (1 user = 1 lintasan) di PHP biar bener-bener bersih
                $uniqueAthletes = [];
                $registrations = [];
                foreach ($registrationsRaw as $reg) {
                    // Kunci unik: gabungan UID User dan Nama (antisipasi data kembar)
                    $athleteKey = $reg['uid_user'] . '-' . strtolower(trim($reg['nama_lengkap']));
                    
                    if (!isset($uniqueAthletes[$athleteKey])) {
                        $uniqueAthletes[$athleteKey] = true;
                        $registrations[] = $reg;
                    }
                }

                foreach ($registrations as $reg) {
                    $seriNum = $reg['nomor_seri'];
                    if (!isset($acaraItem['seri'][$seriNum])) {
                        $acaraItem['seri'][$seriNum] = [];
                    }
                    $acaraItem['seri'][$seriNum][] = $reg;
                }
                $dataAcara[] = $acaraItem;
            }

            $globalData[] = [
                'event' => $e,
                'dataAcara' => $dataAcara
            ];
        }

        $html = View::renderToString('dashboard.general.reports.buku-acara', [
            'globalData' => $globalData,
            'title' => 'Buku Acara - ' . ($uidEvent === 'all' ? 'Semua Event' : $events[0]['nama_event'])
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $filename = 'Buku_Acara_' . date('Y-m-d_His') . '.pdf';
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }
}
