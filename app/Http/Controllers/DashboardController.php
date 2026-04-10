<?php

namespace TheFramework\Http\Controllers;

use TheFramework\Http\Controllers\Controller;
use TheFramework\App\View;
use TheFramework\Helpers\Helper;
use TheFramework\Http\Controllers\Services\ErrorController;
use TheFramework\Models\Category;
use TheFramework\Models\Event;
use TheFramework\Models\Notification;
use TheFramework\Models\Registration;
use TheFramework\Models\Role;
use TheFramework\Models\User;

class DashboardController extends Controller
{
    protected $data = [];
    protected $dataTetap = [];
    protected $notification = [];
    protected $roleSpesificData = [];

    public function __construct()
    {
        $sessionUser = Helper::session_get("user");
        
        // Ambil data user lengkap dengan role dan profile
        $user = User::query()
            ->select(['users.*', 'roles.name as nama_role', 'data_users.*', 'users.id as id', 'users.uid as uid', 'users.email as email'])
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('data_users', 'users.uid', '=', 'data_users.uid_user')
            ->where('users.id', '=', $sessionUser['id'])
            ->first();

        if (!$user) {
            Helper::redirect('/logout', 'error', 'Sesi Anda telah kedaluwarsa atau database telah diperbarui. Silakan login kembali.');
        }

        $this->notification = Helper::get_flash('notification');
        
        // Pastikan nama_role tidak null sebelum di-ucfirst
        $roleName = $sessionUser['nama_role'] ?? 'User';

        $this->dataTetap = [
            'notification' => $this->notification,
            'title' => 'Dashboard ' . ucfirst($roleName) . ' | Khafid Swimming Club (KSC) - Official Website',
            'user' => $user, 
            'totalUnreadNotification' => Notification::query()->where('is_read', '=', 0)->where('uid_user', '=', $user->uid)->count(),
            'unReadNotification' => Notification::query()->where('is_read', '=', 0)->where('uid_user', '=', $user->uid)->all(),
        ];

        // DYNAMICS: Panggil method data berdasarkan role (Contoh: getAdminData)
        $methodName = 'get' . ucfirst($roleName) . 'Data';
        if (method_exists($this, $methodName)) {
            $this->roleSpesificData = $this->$methodName();
        } else {
            $this->roleSpesificData = $this->getAtletData();
        }

        $this->data = array_merge($this->dataTetap, $this->roleSpesificData);
    }

    public function dashboard($role = null)
    {

        return View::render('dashboard.' . $role . '.dashboard', $this->data);
    }

    protected function getAdminData()
    {
        return [
            'totalAnggota' => User::query()->count(),
            'eventAktif' => Event::query()->where('status_event', '=', 'berjalan')->count(),
            'antreanValidasi' => Registration::query()->where('status_pendaftaran', '=', 'pending')->count(),
            'members' => User::query()
                ->select([
                    'users.id',
                    'users.uid',
                    'data_users.nama_lengkap',
                    'data_users.tanggal_lahir',
                    'data_users.klub_renang',
                    'clubs.nama_klub',
                    'data_users.foto_profil'
                ])
                ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->leftJoin('clubs', 'data_users.uid_klub', '=', 'clubs.uid')
                ->where('roles.name', '=', 'atlet')
                ->orderBy('users.created_at', 'DESC')
                ->limit(5)
                ->all()
        ];
    }

    protected function getSuperadminData()
    {
        return $this->getAdminData();
    }

    protected function getPelatihData()
    {
        return [
            'totalAnggota' => User::query()
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', '=', 'atlet')
                ->count(),
            'eventAktif' => Event::query()->where('status_event', '=', 'berjalan')->count(),
            'antreanValidasi' => Registration::query()->where('status_pendaftaran', '=', 'pending')->count(),
            'upcomingEvents' => Event::query()
                ->where('status_event', '=', 'berjalan')
                ->where('tanggal_mulai', '>=', date('Y-m-d'))
                ->orderBy('tanggal_mulai', 'ASC')
                ->limit(5)
                ->all(),
            'registrasiTerbaru' => Registration::query()
                ->select([
                    'registrations.*', 
                    'data_users.nama_lengkap', 
                    'data_users.foto_profil', 
                    'events.nama_event'
                ])
                ->join('users', 'users.uid', '=', 'registrations.uid_user')
                ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->join('event_categories', 'event_categories.uid', '=', 'registrations.uid_event_category')
                ->join('events', 'events.uid', '=', 'event_categories.uid_event')
                ->orderBy('registrations.created_at', 'DESC')
                ->limit(5)
                ->all()
        ];
    }

    protected function getAtletData()
    {
        return [
            'events' => Event::query()->where('status_event', '=', 'berjalan')->orderBy('tanggal_mulai', 'DESC')->limit(2)->all(),
            'members' => User::query()
                ->select(['users.uid', 'data_users.nama_lengkap', 'data_users.foto_profil', 'data_users.klub_renang'])
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->leftJoin('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->where('roles.name', '=', 'atlet')
                ->orderBy('users.created_at', 'DESC')
                ->all(),
            'coaches' => User::query()
                ->select(['users.uid', 'data_users.nama_lengkap', 'data_users.foto_profil', 'data_users.klub_renang'])
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->leftJoin('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->where('roles.name', '=', 'pelatih')
                ->orderBy('users.created_at', 'DESC')
                ->all()

        ];
    }

    public function report()
    {
        $eventUid = Helper::request()->get('event_uid');

        $query = Registration::query()
            ->select([
                'registrations.*', 
                'data_users.nama_lengkap', 
                'events.nama_event'
            ])
            ->join('users', 'users.uid', '=', 'registrations.uid_user')
            ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
            ->join('event_categories', 'event_categories.uid', '=', 'registrations.uid_event_category')
            ->join('events', 'events.uid', '=', 'event_categories.uid_event');

        if ($eventUid && $eventUid !== 'all') {
            $query->where('events.uid', '=', $eventUid);
        }

        $previewData = $query->orderBy('registrations.entry_time', 'DESC')->limit(10)->all();

        return View::render('dashboard.general.report', array_merge($this->data, [
            'title' => 'Laporan & Ekspor Data ' . Helper::session_get("user")['nama_role'] . ' | Khafid Swimming Club (KSC) - Official Website',
            'categories' => Category::query()->all(),
            'events' => Event::query()->orderBy('tanggal_mulai', 'DESC')->all(),
            'eventSelesai' => Event::query()->where('status_event', '=', 'selesai')->count(),
            'totalAnggota' => User::query()
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', '=', 'atlet')
                ->count(),
            'previewData' => $previewData,
            'filters' => [
                'event_uid' => $eventUid,
            ]
        ]));
    }

    public function exportProcess()
    {
        $type = Helper::request()->get('type'); // pendaftaran, keanggotaan, etc
        $format = Helper::request()->get('format'); // pdf, excel
        $eventUid = Helper::request()->get('event_uid');

        if ($type === 'pendaftaran') {
            $query = Registration::query()
                ->select([
                    'registrations.*', 
                    'data_users.nama_lengkap', 
                    'data_users.tanggal_lahir', 
                    'data_users.jenis_kelamin', 
                    'data_users.klub_renang', 
                    'events.nama_event', 
                    'events.tanggal_mulai', 
                    'events.lokasi_event', 
                    'events.biaya_event'
                ])
                ->join('users', 'users.uid', '=', 'registrations.uid_user')
                ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->join('event_categories', 'event_categories.uid', '=', 'registrations.uid_event_category')
                ->join('events', 'events.uid', '=', 'event_categories.uid_event');

            if ($eventUid && $eventUid !== 'all') {
                $query->where('events.uid', '=', $eventUid);
            }

            $results = $query->orderBy('registrations.created_at', 'ASC')->all();

            if ($format === 'pdf') {
                return $this->exportToPdf($results, $eventUid);
            } elseif ($format === 'excel') {
                return $this->exportToExcel($results);
            }
        }

        return Helper::redirect(previous(), 'error', 'Format atau jenis laporan tidak valid.');
    }

    private function exportToPdf($data, $eventUid)
    {
        $event = null;
        if ($eventUid && $eventUid !== 'all') {
            $event = Event::where('uid', $eventUid)->first();
        }

        $html = View::renderToString('dashboard.general.reports.pdf-pendaftaran', [
            'user' => Helper::session_get('user'),
            'registrations' => $data,
            'event' => $event,
            'title' => 'Laporan Pendaftaran Event ' . ($event == null ? '' : $event['nama_event'])
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $filename = 'Laporan_Pendaftaran_' . date('Y-m-d_His') . '.pdf';
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }

    private function exportToExcel($data)
    {
        $filename = 'Laporan_Pendaftaran_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        $output = fopen('php://output', 'w');

        // Header CSV
        fputcsv($output, ['No', 'Nomor Pendaftaran', 'Nama Lengkap', 'Tanggal Lahir', 'Jenis Kelamin', 'Klub', 'Event', 'Status', 'Tanggal Registrasi']);

        $i = 1;
        foreach ($data as $row) {
            fputcsv($output, [
                $i++,
                $row['nomor_pendaftaran'],
                $row['nama_lengkap'],
                $row['tanggal_lahir'],
                $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan',
                $row['nama_klub'] ?? '-',
                $row['nama_event'],
                ucfirst($row['status_pendaftaran']),
                $row['created_at']
            ]);
        }

        fclose($output);
        exit;
    }
}

