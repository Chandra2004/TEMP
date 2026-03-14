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
        $this->notification = Helper::get_flash('notification');
        $this->dataTetap = [
            'notification' => $this->notification,
            'title' => 'Dashboard ' . Helper::session_get("user")['nama_role'] . ' | Khafid Swimming Club (KSC) - Official Website',
            'user' => Helper::session_get("user"),
            'totalUnreadNotification' => Notification::query()->where('is_read', '=', 0)->where('uid_user', '=', Helper::session_get("user")['uid'])->count(),
            'unReadNotification' => Notification::query()->where('is_read', '=', 0)->where('uid_user', '=', Helper::session_get("user")['uid'])->all(),
        ];

        switch (Helper::session_get("user")['nama_role']) {
            case 'admin':
                $this->roleSpesificData = $this->getAdminData();
                break;
            case 'coach':
                $this->roleSpesificData = $this->getCoachData();
                break;
            case 'member':
                $this->roleSpesificData = $this->getMemberData();
                break;
            default:
                ErrorController::error403();
                break;
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
            'antreanValidasi' => Registration::query()->where('status', '=', 'menunggu')->count(),
            'members' => User::query()
                ->select([
                    'id_user',
                    'uid',
                    'nama_lengkap',
                    'tanggal_lahir',
                    'nama_klub',
                    'foto_profil'
                ])
                ->where('uid_role', Role::where('nama_role', 'member')->first()->uid)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->all()
        ];
    }

    protected function getCoachData()
    {
        $memberRoleUid = Role::where('nama_role', 'member')->first()->uid;
        return [
            'totalAnggota' => User::query()->where('uid_role', $memberRoleUid)->count(),
            'eventAktif' => Event::query()->where('status_event', '=', 'berjalan')->count(),
            'antreanValidasi' => Registration::query()->where('status', '=', 'menunggu')->count(),
            'upcomingEvents' => Event::query()
                ->where('status_event', '=', 'berjalan')
                ->where('tanggal_event', '>=', date('Y-m-d'))
                ->orderBy('tanggal_event', 'ASC')
                ->limit(5)
                ->all(),
            'registrasiTerbaru' => Registration::query()
                ->select(['registrations.*', 'users.nama_lengkap', 'users.foto_profil', 'events.nama_event'])
                ->join('users', 'users.uid', '=', 'registrations.uid_user')
                ->join('events', 'events.uid', '=', 'registrations.uid_event')
                ->orderBy('registrations.created_at', 'DESC')
                ->limit(5)
                ->all()
        ];
    }

    protected function getMemberData()
    {
        return [
            'events' => Event::query()->where('status_event', '=', 'berjalan')->orderBy('tanggal_event', 'DESC')->limit(2)->all(),
            'members' => User::query()
                ->where('uid_role', Role::where('nama_role', 'member')->first()->uid)
                ->orderBy('created_at', 'DESC')
                ->all(),
            'coaches' => User::query()
                ->where('uid_role', Role::where('nama_role', 'coach')->first()->uid)
                ->orderBy('created_at', 'DESC')
                ->all()

        ];
    }

    public function report()
    {
        $eventUid = Helper::request()->get('event_uid');

        $query = Registration::query()
            ->select(['registrations.*', 'users.nama_lengkap', 'events.nama_event'])
            ->join('users', 'users.uid', '=', 'registrations.uid_user')
            ->join('events', 'events.uid', '=', 'registrations.uid_event');

        if ($eventUid && $eventUid !== 'all') {
            $query->where('registrations.uid_event', '=', $eventUid);
        }

        $previewData = $query->orderBy('registrations.tanggal_registrasi', 'DESC')->limit(10)->all();

        return View::render('dashboard.general.report', array_merge($this->data, [
            'title' => 'Laporan & Ekspor Data ' . Helper::session_get("user")['nama_role'] . ' | Khafid Swimming Club (KSC) - Official Website',
            'categories' => Category::query()->all(),
            'events' => Event::query()->orderBy('tanggal_event', 'DESC')->all(),
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
                ->select(['registrations.*', 'users.nama_lengkap', 'users.tanggal_lahir', 'users.jenis_kelamin', 'users.nama_klub', 'events.nama_event', 'events.tanggal_event', 'events.lokasi_event', 'events.biaya_event'])
                ->join('users', 'users.uid', '=', 'registrations.uid_user')
                ->join('events', 'events.uid', '=', 'registrations.uid_event');

            if ($eventUid && $eventUid !== 'all') {
                $query->where('registrations.uid_event', '=', $eventUid);
            }

            $results = $query->orderBy('registrations.tanggal_registrasi', 'ASC')->all();

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

        // return View::render('dashboard.general.reports.pdf-pendaftaran', [
        //     'user' => Helper::session_get('user'),
        //     'registrations' => $data,
        //     'event' => $event,
        //     'title' => 'Laporan Pendaftaran Event ' . ($event == null ? '' : $event['nama_event'])
        // ]);
        
        $html = View::renderToString('dashboard.general.reports.pdf-pendaftaran', [
            'user' => Helper::session_get('user'),
            'registrations' => $data,
            'event' => $event,
            'title' => 'Laporan Pendaftaran Event'
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
                ucfirst($row['status']),
                $row['tanggal_registrasi']
            ]);
        }

        fclose($output);
        exit;
    }
}

