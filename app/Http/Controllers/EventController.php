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
use TheFramework\Models\Result;
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
        $requirementParameters = \TheFramework\Models\RequirementParameter::query()->all();

        // Transform data untuk memudahkan View (Modal Edit)
        $events['data'] = array_map(function ($event) use ($requirementParameters) {
            $event['matches_data'] = array_map(function ($cat) use ($requirementParameters) {
                $formattedRequirements = array_map(function ($req) use ($requirementParameters) {
                    $paramMetadata = null;
                    foreach ($requirementParameters as $p) {
                        if ($p['parameter_key'] === $req['parameter_name']) {
                            $paramMetadata = $p;
                            break;
                        }
                    }
                    return [
                        'parameter_name' => $req['parameter_name'],
                        'operator' => $req['operator'],
                        'parameter_value' => $req['parameter_value'],
                        'input_type' => $paramMetadata['input_type'] ?? 'text',
                        'options' => json_decode($paramMetadata['input_options'] ?? '[]', true),
                        'allowed_operators' => json_decode($paramMetadata['allowed_operators'] ?? '[]', true)
                    ];
                }, $cat['requirements'] ?? []);

                return [
                    'uid_category' => $cat['uid_category'],
                    'nomor_acara' => $cat['nomor_acara'] ?? '',
                    'nama_acara' => $cat['nama_acara'],
                    'tipe_biaya' => $cat['tipe_biaya'],
                    'biaya_pendaftaran' => $cat['biaya_pendaftaran'],
                    'jumlah_seri' => $cat['jumlah_seri'],
                    'waktu_mulai' => $cat['waktu_mulai'] ?? '08:00',
                    'requirements' => $formattedRequirements
                ];
            }, $event['eventCategories'] ?? []);

            return $event;
        }, $events['data']);
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

        $requirementParameters = \TheFramework\Models\RequirementParameter::query()
            ->where('is_active', '=', 1)
            ->all();

        return View::render('dashboard.general.event', [
            'user' => $user,
            'events' => $events,
            'categories' => $categories,
            'authors' => $authors,
            'payment_methods' => $paymentMethods,
            'requirement_parameters' => $requirementParameters,
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

            $logoKiri = null;
            if ($request->hasFile('logo_kiri')) {
                $logoKiri = UploadHandler::handleUploadToWebP($request->file('logo_kiri'), '/logos', 'logo_kiri_');
                if (UploadHandler::isError($logoKiri)) {
                    throw new \Exception(UploadHandler::getErrorMessage($logoKiri));
                }
            }

            $logoKanan = null;
            if ($request->hasFile('logo_kanan')) {
                $logoKanan = UploadHandler::handleUploadToWebP($request->file('logo_kanan'), '/logos', 'logo_kanan_');
                if (UploadHandler::isError($logoKanan)) {
                    throw new \Exception(UploadHandler::getErrorMessage($logoKanan));
                }
            }

            $data = $request->validated();
            $data['uid'] = Helper::uuid();
            $data['slug'] = Helper::slugify($data['nama_event']);
            $data['banner_event'] = $newPhoto;
            $data['logo_kiri'] = $logoKiri;
            $data['logo_kanan'] = $logoKanan;

            $matches = $request->all()['matches'] ?? [];
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
                foreach ($matches as $idx => $match) {
                    $matchUid = Helper::uuid();
                    EventCategory::query()->insert([
                        'uid' => $matchUid,
                        'uid_event' => $data['uid'],
                        'uid_category' => $match['uid_category'],
                        'nomor_acara' => $idx + 1,
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
                                    'parameter_value' => json_encode($req['parameter_value'] ?? ''),
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

            if ($request->hasFile('logo_kiri')) {
                $logoKiriNew = UploadHandler::handleUploadToWebP($request->file('logo_kiri'), '/logos', 'logo_kiri_');
                if (UploadHandler::isError($logoKiriNew)) {
                    throw new \Exception(UploadHandler::getErrorMessage($logoKiriNew));
                }
                if ($eventRecord['logo_kiri']) {
                    UploadHandler::delete($eventRecord['logo_kiri'], '/logos');
                }
                $data['logo_kiri'] = $logoKiriNew;
            } else {
                $data['logo_kiri'] = $eventRecord['logo_kiri'];
            }

            if ($request->hasFile('logo_kanan')) {
                $logoKananNew = UploadHandler::handleUploadToWebP($request->file('logo_kanan'), '/logos', 'logo_kanan_');
                if (UploadHandler::isError($logoKananNew)) {
                    throw new \Exception(UploadHandler::getErrorMessage($logoKananNew));
                }
                if ($eventRecord['logo_kanan']) {
                    UploadHandler::delete($eventRecord['logo_kanan'], '/logos');
                }
                $data['logo_kanan'] = $logoKananNew;
            } else {
                $data['logo_kanan'] = $eventRecord['logo_kanan'];
            }

            $matches = $request->all()['matches'] ?? [];
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

            foreach ($matches as $idx => $match) {
                $matchUid = Helper::uuid();
                EventCategory::query()->insert([
                    'uid' => $matchUid,
                    'uid_event' => $uidEvent,
                    'uid_category' => $match['uid_category'],
                    'nomor_acara' => $idx + 1,
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
                                'parameter_value' => json_encode($req['parameter_value'] ?? ''),
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
                    'nama_acara' => $ec['nama_acara'],
                    'kode_ku' => $ec['category']['kode_ku'] ?? 'UMUM',
                    'seri' => []
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

                    // Tambahkan komentar penjelas
                    // Ambil Waktu Terbaik (Prestasi) dari database (Hanya dari lomba yang TANGGALNYA SEBELUM event ini)
                    $bestTime = Result::getBestTime($reg['uid_user'], $ec['uid_category'], $e['tanggal_mulai']);
                    
                    // Prioritas:
                    // 1. Waktu terbaik dari lomba sebelumnya di database
                    // 2. Jika tidak ada, gunakan seed_time pendaftaran (jika berupa waktu valid)
                    // 3. Jika tidak ada semua, tampilkan "NT"
                    if ($bestTime !== 'NT') {
                        $reg['prestasi'] = $bestTime;
                    } else {
                        // Cek apakah seed_time berisi format waktu (ada tanda : atau .)
                        // Jika isinya teks lain seperti nama kategori, kita paksa jadi NT
                        $isTime = preg_match('/[0-9]/', $reg['seed_time'] ?? '') && (str_contains($reg['seed_time'], ':') || str_contains($reg['seed_time'], '.'));
                        $reg['prestasi'] = $isTime ? $reg['seed_time'] : 'NT';
                    }
                    
                    // Ambil Hasil lomba saat ini (jika sudah diinput admin)
                    $currentResult = Result::query()
                        ->where('uid_registration', '=', $reg['uid'])
                        ->first();
                    
                    if ($currentResult) {
                        if ($currentResult['status'] === 'FINISH') {
                            $reg['hasil'] = $currentResult['waktu_akhir'];
                        } else {
                            $reg['hasil'] = $currentResult['status']; // NS atau DSQ
                        }
                    } else {
                        $reg['hasil'] = ''; // Belum ada hasil
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

    public function exportBukuHasil($role, $uidUser, $uidEvent)
    {
        $eventFound = Event::where('uid', $uidEvent)->first();
        if (!$eventFound) {
            return Helper::redirect(Helper::previous(), 'error', 'Event tidak ditemukan.');
        }

        $eventCategories = \TheFramework\Models\EventCategory::query()
            ->with(['category'])
            ->where('uid_event', '=', $uidEvent)
            ->orderBy('nomor_acara', 'ASC')
            ->all();

        $globalData = [];
        foreach ($eventCategories as $ec) {
            $results = Result::query()
                ->select([
                    'results.*',
                    'data_users.nama_lengkap',
                    'data_users.klub_renang',
                    'data_users.sekolah',
                    'registrations.nomor_pendaftaran'
                ])
                ->join('registrations', 'results.uid_registration', '=', 'registrations.uid')
                ->join('users', 'registrations.uid_user', '=', 'users.uid')
                ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->where('registrations.uid_event_category', '=', $ec['uid'])
                ->where('results.status', '=', 'FINISH')
                ->where('results.total_milliseconds', '>', 0)
                ->orderBy('results.total_milliseconds', 'ASC')
                ->all();

            // Tambahkan juga yang NS/DSQ di akhir (opsional)
            $nonFinishers = Result::query()
                ->select([
                    'results.*',
                    'data_users.nama_lengkap',
                    'data_users.klub_renang',
                    'data_users.sekolah',
                    'registrations.nomor_pendaftaran'
                ])
                ->join('registrations', 'results.uid_registration', '=', 'registrations.uid')
                ->join('users', 'registrations.uid_user', '=', 'users.uid')
                ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->where('registrations.uid_event_category', '=', $ec['uid'])
                ->where('results.status', '!=', 'FINISH')
                ->all();

            $globalData[] = [
                'acara' => $ec,
                'results' => array_merge($results, $nonFinishers)
            ];
        }

        $html = View::renderToString('dashboard.general.reports.buku-hasil', [
            'event' => $eventFound,
            'globalData' => $globalData,
            'title' => 'Buku Hasil - ' . $eventFound['nama_event']
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->loadHtml($html);
        $dompdf->render();

        $filename = 'Buku_Hasil_' . str_replace(' ', '_', $eventFound['nama_event']) . '_' . date('His') . '.pdf';
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }
}
