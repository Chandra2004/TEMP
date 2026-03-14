<?php

namespace TheFramework\Http\Controllers;

use TheFramework\App\View;
use TheFramework\Helpers\Helper;
use TheFramework\Config\UploadHandler;
use Exception;
use TheFramework\Models\Gallery;
use TheFramework\Models\Event;
use TheFramework\Models\User;
use TheFramework\Http\Controllers\Services\ErrorController;
use TheFramework\Http\Requests\GalleryRequest;
use Carbon\Carbon;
use TheFramework\App\Config; // Tambahkan ini untuk Carbon::now(Config::get('DB_TIMEZONE'))

class GalleryController extends DashboardController
{
    protected Gallery $gallery;

    public function __construct()
    {
        parent::__construct();
        $this->gallery = new Gallery();
    }

    public function gallery($page = 1)
    {
        $galleries = Gallery::query()
            ->with(['event'])
            ->latest()
            ->paginate(50, $page);

        $events = Event::query()
            ->select(['uid', 'nama_event'])
            ->all();

        // Hitung Statistik Penyimpanan
        $totalItems = Gallery::query()->count();
        $maxItems = 1000;

        // Hitung Ukuran Folder (Opsional: Bisa berat jika ribuan file, tapi untuk 1000 masih ok)
        $totalSize = 0;
        $uploadDir = ROOT_DIR . '/public/assets/uploads/gallery';
        if (is_dir($uploadDir)) {
            $files = array_diff(scandir($uploadDir), ['.', '..']);
            foreach ($files as $file) {
                $totalSize += filesize($uploadDir . '/' . $file);
            }
        }

        $maxSize = 1000 * 2 * 1024 * 1024; // 2GB
        $storageStats = [
            'total_items' => $totalItems,
            'max_items' => $maxItems,
            'item_percentage' => round(($totalItems / $maxItems) * 100, 1),
            'total_size_mb' => round($totalSize / (1024 * 1024), 2),
            'max_size_mb' => 2000,
            'size_percentage' => round(($totalSize / $maxSize) * 100, 1),
        ];

        return View::render('dashboard.general.gallery', array_merge($this->dataTetap, [
            'title' => 'Manajemen Gallery | Khafid Swimming Club (KSC) - Official Website',
            'galleries' => $galleries,
            'events' => $events,
            'stats' => $storageStats,
        ]));
    }

    public function galleryPage($role, $page)
    {
        return $this->gallery($page);
    }

    public function galleryCreateProcess($role, $uidUser, GalleryRequest $request)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            // Cek Limit 1000 Foto
            $currentCount = Gallery::query()->count();
            if ($currentCount >= 1000) {
                throw new Exception('Kapasitas galeri penuh (Maksimal 1000 foto).');
            }

            $validatedData = $request->validated();
            $files = $request->file('foto_event');

            if (!$request->hasFile('foto_event')) {
                throw new Exception('Foto event wajib diunggah.');
            }

            // Normalisasi agar selalu array of files
            if (isset($files['name'])) {
                $files = [$files];
            }

            // Filter mendalam: pastikan hanya menghitung file yang benar-benar ada/valid errornya OK
            $validFiles = [];
            foreach ($files as $f) {
                if (isset($f['error']) && $f['error'] === UPLOAD_ERR_OK) {
                    $validFiles[] = $f;
                }
            }

            if (count($validFiles) > 10) {
                throw new Exception('Maksimal upload adalah 10 foto sekaligus.');
            }

            if (empty($validFiles)) {
                throw new Exception('Tidak ada file valid yang dipilih.');
            }

            $uploadedPaths = [];
            $batchData = [];
            $now = Carbon::now(Config::get('DB_TIMEZONE'))->toDateTimeString();

            foreach ($validFiles as $file) {
                // Validasi manual per file (karena multiple upload)
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($ext, $allowed)) {
                    throw new Exception("Format file {$file['name']} tidak didukung (Hanya jpg, jpeg, png, webp).");
                }
                if ($file['size'] > 2 * 1024 * 1024) {
                    throw new Exception("Ukuran file {$file['name']} terlalu besar (Maksimal 2MB).");
                }

                $fotoPath = UploadHandler::handleUploadToWebP($file, '/gallery', 'gallery_');
                if (UploadHandler::isError($fotoPath)) {
                    throw new Exception(UploadHandler::getErrorMessage($fotoPath));
                }

                $uploadedPaths[] = $fotoPath;
                $batchData[] = [
                    'uid' => Helper::uuid(),
                    'uid_event' => $validatedData['uid_event'],
                    'foto_event' => $fotoPath,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Simpan setiap data (Menggunakan batch insert dalam satu transaksi)
            $this->gallery->addMultipleGallery($batchData);

            return Helper::redirect(Helper::previous(), 'success', count($batchData) . ' foto berhasil ditambahkan ke galeri.', 10);
        } catch (Exception $e) {
            // Hapus file yang sudah terlanjur diupload jika error
            if (!empty($uploadedPaths)) {
                foreach ($uploadedPaths as $path) {
                    UploadHandler::delete($path, '/gallery');
                }
            }
            return Helper::redirect(Helper::previous(), 'error', 'Terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }

    public function galleryEditProcess($role, $uidUser, $uidGallery, GalleryRequest $request)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $validatedData = $request->validated();
            $oldGallery = Gallery::where('uid', $uidGallery)->first();

            if (!$oldGallery) {
                return Helper::redirect(Helper::previous(), 'error', 'Galeri tidak ditemukan.', 10);
            }

            $fotoEvent = $oldGallery['foto_event'];
            if ($request->hasFile('foto_event')) {
                if ($oldGallery['foto_event']) {
                    UploadHandler::delete($oldGallery['foto_event'], '/gallery');
                }
                $fotoEvent = UploadHandler::handleUploadToWebP($request->file('foto_event'), '/gallery', 'gallery_');
                if (UploadHandler::isError($fotoEvent)) {
                    throw new Exception(UploadHandler::getErrorMessage($fotoEvent));
                }
            }

            $data = [
                'uid_event' => $validatedData['uid_event'],
                'foto_event' => $fotoEvent,
                'updated_at' => Carbon::now(Config::get('DB_TIMEZONE'))->toDateTimeString(),
            ];

            $this->gallery->updateGallery($data, $uidGallery);

            return Helper::redirect(Helper::previous(), 'success', 'Foto galeri berhasil diperbarui.', 10);
        } catch (Exception $e) {
            if (isset($fotoEvent) && $request->hasFile('foto_event')) { // Only delete if a new file was uploaded and caused error
                UploadHandler::delete($fotoEvent, '/gallery');
            }
            return Helper::redirect(Helper::previous(), 'error', 'Terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }

    public function galleryDeleteProcess($role, $uidUser, $uidGallery)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $gallery = Gallery::where('uid', $uidGallery)->first();
            if (!$gallery) {
                return Helper::redirect(Helper::previous(), 'error', 'Galeri tidak ditemukan.', 10);
            }

            if ($gallery['foto_event']) {
                UploadHandler::delete($gallery['foto_event'], '/gallery');
            }

            $this->gallery->deleteGallery($uidGallery);

            return Helper::redirect(Helper::previous(), 'success', 'Foto galeri berhasil dihapus.', 10);
        } catch (Exception $e) {
            return Helper::redirect(Helper::previous(), 'error', 'Terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }
}