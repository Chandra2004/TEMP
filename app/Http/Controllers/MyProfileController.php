<?php

namespace TheFramework\Http\Controllers;

use TheFramework\Models\user;
use TheFramework\App\View;
use TheFramework\Helpers\Helper;
use Exception;
use TheFramework\Config\UploadHandler;
use TheFramework\Http\Controllers\Services\ErrorController;
use TheFramework\Http\Requests\MyProfileRequest;

class MyProfileController extends DashboardController
{
    protected user $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new user();
    }
    public function myProfile()
    {
        return View::render('dashboard.general.my-profile', array_merge($this->dataTetap, [
            'title' => 'Manajemen Pendaftaran ' .   Helper::session_get("user")['nama_role'] . ' | Khafid Swimming Club (KSC) - Official Website'
        ]));
    }

    public function myProfileEditProcess($role, $uidUser, MyProfileRequest $request)
    {
        try {
            $oldData = $this->user->where('uid', $uidUser)->first();
            $dataPhoto = null;
            $dataKtp = null;
            $dataAkta = null;

            if ($request->hasFile('foto_profil')) {
                if ($oldData['foto_profil'] != null) {
                    UploadHandler::delete($oldData['foto_profil'], '/users');
            }
                $dataPhoto = UploadHandler::handleUploadToWebP($request->file('foto_profil'), '/users', 'user_');
                if(UploadHandler::isError($dataPhoto)) {
                    throw new Exception(UploadHandler::getErrorMessage($dataPhoto));
                }
            } else {
                $dataPhoto = $oldData['foto_profil'];
            }

            if ($request->hasFile('foto_ktp')) {
                if ($oldData['foto_ktp'] != null) {
                    UploadHandler::delete($oldData['foto_ktp'], '/id_cards');
                }    
                $dataKtp = UploadHandler::handleUploadToWebP($request->file('foto_ktp'), '/id_cards', 'card_');
                if(UploadHandler::isError($dataKtp)) {
                    throw new Exception(UploadHandler::getErrorMessage($dataKtp));
                }
            } else {
                $dataKtp = $oldData['foto_ktp'];
            }

            if ($request->hasFile('foto_akta')) {
                if ($oldData['foto_akta'] != null) {
                    UploadHandler::delete($oldData['foto_akta'], '/birth_certificates');
                }    
                $dataAkta = UploadHandler::handleUploadToWebP($request->file('foto_akta'), '/birth_certificates', 'akta_');
                if(UploadHandler::isError($dataAkta)) {
                    throw new Exception(UploadHandler::getErrorMessage($dataAkta));
                }
            } else {
                $dataAkta = $oldData['foto_akta'];
            }
            
            $data = $request->validated();
            $data['uid'] = $uidUser;
            $data['foto_profil'] = $dataPhoto;
            $data['foto_ktp'] = $dataKtp;
            $data['foto_akta'] = $dataAkta;

            if (!empty($data['password'])) {
                $data['password'] = Helper::hash_password($data['password']);
            } else {
                unset($data['password']);
            }

            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $checkTelp = User::query()
                ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->where('data_users.no_telepon', '=', $data['no_telepon'])
                ->where('users.uid', '!=', $uidUser)
                ->first();

            if ($checkTelp) {
                return Helper::redirect("/{$role}/dashboard/my-profile", 'error', 'Nomor telepon sudah digunakan oleh pengguna lain.', 10);
            } else {
                $status = $this->user->updateMyProfile($data) ? true : false;
                if ($status === false) {
                    return Helper::redirect("/{$role}/dashboard/my-profile", 'error', 'data gagal diperbarui', 10);
                } else {
                    $sessionUser = Helper::session_get("user");
                    $data = array_merge($sessionUser, $data);
                    Helper::session_write("user", $data, true);
                    return Helper::redirect("/{$role}/dashboard/my-profile", 'success', 'data berhasil diperbarui', 10);
                }
            }
        } catch (Exception $e) {
            return Helper::redirect("/{$role}/dashboard/my-profile", 'error', 'terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }
}
