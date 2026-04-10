<?php

namespace TheFramework\Http\Controllers;

use Exception;
use TheFramework\App\Config;
use TheFramework\Http\Controllers\Controller;
use TheFramework\App\View;
use TheFramework\Helpers\Helper;
use TheFramework\Config\EmailHandler;
use TheFramework\Http\Requests\KontakRequest;
use TheFramework\Models\Category;
use TheFramework\Models\Event;
use TheFramework\Models\Gallery;
use TheFramework\Models\Role;
use TheFramework\Models\User;

class HomepageController extends Controller
{
    public $sessionLogin;

    public function __construct()
    {
        $this->sessionLogin = Helper::session_get('user') ?? null;
    }

    public function homepage()
    {
        return View::render('homepage.beranda', [
            'user' => $this->sessionLogin,
            'notification' => Helper::get_flash('notification'),
            "title" => "Khafid Swimming Club (KSC) - Official Website | Beranda",
            'events' => (function() {
                $events = Event::query()
                    ->select([
                        'events.*',
                        'data_users.nama_lengkap AS author'
                    ])
                    ->with(['eventCategories.category', 'eventCategories.registrations'])
                    ->join('users', 'users.uid', '=', 'events.uid_author')
                    ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                    ->limit(2)
                    ->orderBy('events.tanggal_mulai', 'desc')
                    ->all();
                
                foreach ($events as &$event) {
                    $count = 0;
                    if (isset($event['eventCategories'])) {
                        foreach ($event['eventCategories'] as $cat) {
                            $count += count($cat['registrations'] ?? []);
                        }
                    }
                    $event['registrations_count'] = $count;
                }
                return $events;
            })(),
            'galleries' => Gallery::query()
                ->select([
                    'galleries.*',
                    'events.nama_event AS nama_foto'
                ])
                ->join('events', 'events.uid', '=', 'galleries.uid_event')
                ->limit(10)
                ->orderBy('created_at', 'desc')
                ->all()
        ]);
    }

    public function aboutUs()
    {
        return View::render('homepage.tentang-kami', [
            'user' => $this->sessionLogin,
            'notification' => Helper::get_flash('notification'),
            'title' => 'Khafid Swimming Club (KSC) - Official Website | Tentang Kami',
            'mentors' => User::query()
                ->select(['users.*', 'data_users.*'])
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->where('roles.name', '=', 'pelatih')
                ->limit(3)
                ->all()
        ]);
    }

    public function facilities()
    {
        return View::render('homepage.fasilitas', [
            'user' => $this->sessionLogin,
            'notification' => Helper::get_flash('notification'),
            'title' => 'Khafid Swimming Club (KSC) - Official Website | Fasilitas',
        ]);
    }

    public function coaches()
    {
        return View::render('homepage.pelatih', [
            'user' => $this->sessionLogin,
            'notification' => Helper::get_flash('notification'),
            'title' => 'Khafid Swimming Club (KSC) - Official Website | Pelatih',
            'mentors' => User::query()
                ->select(['users.*', 'data_users.*'])
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->where('roles.name', '=', 'pelatih')
                ->all()
        ]);
    }

    public function events($keyword = null, $page = 1)
    {
        $query = Event::query();

        if ($keyword && $keyword != 'page') {
            $query->where('nama_event', 'like', "%{$keyword}%")
                ->orWhere('lokasi_event', 'like', "%{$keyword}%");
        }

        $pagination = $query->with(['eventCategories.category', 'eventCategories.registrations', 'author'])->orderBy("tanggal_mulai", "DESC")->paginate(9, $page);
        
        foreach ($pagination['data'] as &$event) {
            $count = 0;
            if (isset($event['eventCategories'])) {
                foreach ($event['eventCategories'] as $cat) {
                    $count += count($cat['registrations'] ?? []);
                }
            }
            $event['registrations_count'] = $count;
        }

        $events = $pagination;

        return View::render('homepage.event', [
            'user' => $this->sessionLogin,
            'notification' => Helper::get_flash('notification'),
            "title" => "Khafid Swimming Club (KSC) - Official Website | Event",
            'categories' => Category::query()->all(),
            'events' => $events,
        ]);
    }

    public function eventDetail($slug, $uid)
    {
        $event = Event::query()
            ->select([
                'events.*',
                'data_users.nama_lengkap AS author',
                'payment_method.bank',
                'payment_method.rekening',
                'payment_method.atas_nama',
                'payment_method.photo',
            ])
            ->with(['eventCategories.category', 'eventCategories.registrations'])
            ->join('users', 'events.uid_author', '=', 'users.uid')
            ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
            ->join('payment_method', 'events.uid_payment_method', '=', 'payment_method.uid', 'LEFT')
            ->where('events.slug', '=', $slug)->where('events.uid', '=', $uid)->first();

        $profileCompletion = ['complete' => false, 'percentage' => 0];
        $registeredCategoryUids = [];

        if ($this->sessionLogin) {
            $userModel = new User();
            $profileCompletion = $userModel->getProfileCompletion($this->sessionLogin['uid']);

            // Dapatkan list UID kategori yang sudah didaftarkan user di event ini
            $registeredCats = (new \TheFramework\App\QueryBuilder(\TheFramework\App\Database::getInstance()))
                ->table('registrations')
                ->select(['registrations.uid_event_category'])
                ->join('event_categories', 'registrations.uid_event_category', '=', 'event_categories.uid')
                ->where('registrations.uid_user', '=', $this->sessionLogin['uid'])
                ->where('event_categories.uid_event', '=', $uid)
                ->get();
            
            if ($registeredCats) {
                foreach ($registeredCats as $reg) {
                    $registeredCategoryUids[] = $reg['uid_event_category'];
                }
            }
        }

        return View::render('homepage.event-detail', [
            'user' => $this->sessionLogin,
            'notification' => Helper::get_flash('notification'),
            'title' => "Khafid Swimming Club (KSC) - Official Website | " . $event['nama_event'],
            'event' => (function($event) {
                $count = 0;
                if (isset($event['eventCategories'])) {
                    foreach ($event['eventCategories'] as $cat) {
                        $count += count($cat['registrations'] ?? []);
                    }
                }
                $event['registrations_count'] = $count;
                return $event;
            })($event),
            'profileCompletion' => $profileCompletion,
            'registeredCategoryUids' => $registeredCategoryUids
        ]);
    }

    public function galleries()
    {
        $eventUid = Helper::request()->get('event');
        $page = (int)Helper::request()->get('page', 1);

        $events = Event::query()->select(['uid', 'nama_event'])->all();

        $query = Gallery::query()
            ->select(['galleries.*', 'events.nama_event'])
            ->join('events', 'galleries.uid_event', '=', 'events.uid');

        if ($eventUid) {
            $query->where('galleries.uid_event', '=', $eventUid);
        }

        $pagination = $query->orderBy('galleries.created_at', 'desc')->paginate(24, $page);

        return View::render('homepage.galeri', [
            'user' => $this->sessionLogin,
            'notification' => Helper::get_flash('notification'),
            'title' => 'Khafid Swimming Club (KSC) - Official Website | Galeri',
            'events' => $events,
            'galleries' => $pagination['data'],
            'pagination' => $pagination,
            'activeEvent' => $eventUid,
        ]);
    }

    public function contact()
    {
        return View::render('homepage.kontak', [
            'user' => $this->sessionLogin,
            'notification' => Helper::get_flash('notification'),
            'title' => 'Khafid Swimming Club (KSC) - Official Website | Kontak',
        ]);
    }

    public function contactProcess(KontakRequest $kontak)
    {
        try {
            $data = $kontak->validated();
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'N/A';

            $htmlBody = Helper::renderToString('email.contact-notification', [
                'data' => $data,
                'ip_address' => $ip_address
            ]);

            if ($htmlBody === false) {
                $htmlBody = "Pesan baru dari {$data['nama_lengkap']} ({$data['email']}):\n\n{$data['pesan']}";
            }

            $status = EmailHandler::send(Config::get('MAIL_TO'), 'Kontak: ' . $data['subjek'], $htmlBody, []);

            if (!$status) {
                return Helper::redirect('/contact', 'error', 'Terjadi kesalahan saat mengirim email', 10);
            }

            return Helper::redirect('/contact', 'success', 'Pesan anda berhasil terkirim', 10);
        } catch (Exception $e) {
            return Helper::redirect('/contact', 'error', 'Terjadi kesalahan : ' . $e->getMessage(), 10);
        }
    }
}
