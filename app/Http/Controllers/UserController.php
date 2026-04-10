<?php

namespace TheFramework\Http\Controllers;

use TheFramework\App\View;
use TheFramework\Helpers\Helper;
use Exception;
use TheFramework\Config\UploadHandler;
use TheFramework\Http\Controllers\Services\ErrorController;
use TheFramework\Http\Requests\UserRequest;
use TheFramework\Models\Role;
use TheFramework\Models\User;

class UserController extends DashboardController
{
    protected User $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    // TAMBAHKAN DATA ROLES AMBIL HANYA UID, NAMA_ROLE SAJA
    public function user()
    {
        return View::render('dashboard.admin.user', array_merge($this->dataTetap, [
            'title' => 'Manajemen Pengguna ' . Helper::session_get("user")['nama_role'] . ' | Khafid Swimming Club (KSC) - Official Website',
            'users' => User::query()
                ->select([
                    'users.*',
                    'roles.name as nama_role',
                    'data_users.*',
                    'users.id as id',
                    'users.uid as uid'
                ])
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->leftJoin('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->all(),
            'roles' => Role::query()->select([
                'uid',
                'name as nama_role'
            ])->all()
        ]));
    }

    public function userCreateProcess($role, $uidUser, UserRequest $request)
    {
        try {
            $dataPhoto = null;
            $dataKtp = null;

            if ($request->hasFile('foto_profil')) {
                $dataPhoto = UploadHandler::handleUploadToWebP($request->file('foto_profil'), '/users', 'user_');
                if (UploadHandler::isError($dataPhoto)) {
                    throw new Exception(UploadHandler::getErrorMessage($dataPhoto));
                }
            }

            if ($request->hasFile('foto_ktp')) {
                $dataKtp = UploadHandler::handleUploadToWebP($request->file('foto_ktp'), '/id_cards', 'card_');
                if (UploadHandler::isError($dataKtp)) {
                    throw new Exception(UploadHandler::getErrorMessage($dataKtp));
                }
            }

            $data = $request->validated();
            $data['uid'] = Helper::uuid();
            $data['password'] = Helper::hash_password($data['password']);
            $data['is_active'] = $data['is_active'] ? 1 : 0;
            $data['foto_profil'] = $dataPhoto;
            $data['foto_ktp'] = $dataKtp;


            $checkEmail = $this->user->checkEmail($data['email']);
            $checkNomor = $this->user->checkNomor($data['no_telepon']);

            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }
            if ($checkEmail != null) {
                return Helper::redirect("/{$role}/dashboard/management-user", 'error', 'Email sudah terdaftar', 10);
            } else if ($checkNomor != null) {
                return Helper::redirect("/{$role}/dashboard/management-user", 'error', 'Nomor telepon sudah terdaftar', 10);
            } else {
                unset($data['password_confirm']);
                unset($data['checkbox']);
                $this->user->addUser($data);
                return Helper::redirect("/{$role}/dashboard/management-user", 'success', "Berhasil mendaftar, mohon untuk login {$data['nama_lengkap']}", 10);
            }
        } catch (Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-user", 'error', 'terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }

    public function userEditProcess($role, $uidUser, $uidPerson, UserRequest $request)
    {
        try {
            $oldData = $this->user->where('uid', $uidPerson)->first();
            $dataPhoto = null;
            $dataKtp = null;

            if ($request->hasFile('foto_profil')) {
                if ($oldData['foto_profil'] != null) {
                    UploadHandler::delete($oldData['foto_profil'], '/users');
                }
                $dataPhoto = UploadHandler::handleUploadToWebP($request->file('foto_profil'), '/users', 'user_');
                if (UploadHandler::isError($dataPhoto)) {
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
                if (UploadHandler::isError($dataKtp)) {
                    throw new Exception(UploadHandler::getErrorMessage($dataKtp));
                }
            } else {
                $dataKtp = $oldData['foto_ktp'];
            }

            $data = $request->validated();
            $data['uid'] = $uidPerson;
            $data['foto_profil'] = $dataPhoto;
            $data['foto_ktp'] = $dataKtp;
            $data['is_active'] = $data['is_active'] ? 1 : 0;

            if (!empty($data['password']) && !empty($data['password_confirm'])) {
                $data['password'] = Helper::hash_password($data['password']);
                unset($data['password_confirm']);
            } else {
                unset($data['password']);
                unset($data['password_confirm']);
            }

            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $checkTelp = user::query()->where('no_telepon', '=', $data['no_telepon'])
                ->where('uid', '!=', $uidPerson)->first();
            if ($checkTelp) {
                return Helper::redirect("/{$role}/dashboard/management-user", 'error', 'nomor telepon sudah digunakan orang lain', 10);
            } else {
                unset($data['checkbox']);
                $status = $this->user->updateUser($data) ? true : false;
                if ($status === false) {
                    return Helper::redirect("/{$role}/dashboard/management-user", 'error', 'data gagal diperbarui', 10);
                } else {
                    return Helper::redirect("/{$role}/dashboard/management-user", 'success', 'data berhasil diperbarui', 10);
                }
            }
        } catch (Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-user", 'error', 'terjadi kesalahan: ' . $e->getMessage(), 10);
        }
    }

    // METHOD INI MENERIMA 3 PARAMETER LIHAT DI WEB.PHP
    public function userDeleteProcess($role, $uidUser, $uidPerson)
    {
        try {
            $oldData = $this->user->where('uid', $uidPerson)->first();
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            } else {
                $findUser = User::query()->where('uid', '=', $uidPerson)->first();
                if ($findUser === null) {
                    return Helper::redirect("/{$role}/dashboard/management-user", 'error', 'data tidak ditemukan', 10);
                } else {
                    $status = $this->user->deleteUser($uidPerson) ? true : false;
                    if ($status === false) {
                        return Helper::redirect("/{$role}/dashboard/management-user", 'error', 'data gagal dihapus', 10);
                    } else {
                        if ($oldData['foto_profil']) {
                            if ($oldData['foto_profil'] != null) {
                                $deletePhoto = UploadHandler::delete($oldData['foto_profil'], '/users');
                                if (UploadHandler::isError($deletePhoto)) {
                                    throw new Exception(UploadHandler::getErrorMessage($deletePhoto));
                                }
                            }
                        }

                        if ($oldData['foto_ktp']) {
                            if ($oldData['foto_ktp'] != null) {
                                $deleteKtp = UploadHandler::delete($oldData['foto_ktp'], '/id_cards');
                                if (UploadHandler::isError($deleteKtp)) {
                                    throw new Exception(UploadHandler::getErrorMessage($deleteKtp));
                                }
                            }
                        }
                        return Helper::redirect("/{$role}/dashboard/management-user", 'success', 'data berhasil dihapus', 10);
                    }
                }
            }
        } catch (Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-user", 'error', 'terjadi kesalahan ' . $e->getMessage(), 10);
        }
    }
}
