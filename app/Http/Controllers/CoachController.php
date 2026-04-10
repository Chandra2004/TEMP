<?php

namespace TheFramework\Http\Controllers;

use TheFramework\App\View;
use TheFramework\Helpers\Helper;
use Exception;
use TheFramework\Models\Role;
use TheFramework\Models\User;
use TheFramework\Http\Controllers\Services\ErrorController;
use TheFramework\Config\UploadHandler;

class CoachController extends DashboardController
{
    protected User $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    public function coach()
    {
        return View::render('dashboard.admin.coach', array_merge($this->dataTetap, [
            'title' => 'Manajemen Pelatih ' . Helper::session_get("user")['nama_role'] . ' | Khafid Swimming Club (KSC) - Official Website',
            'coaches' => User::query()
                ->select(['users.*', 'roles.name as nama_role', 'data_users.*', 'users.id as id', 'users.uid as uid'])
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->where('roles.name', '=', 'pelatih')
                ->all(),
            'roleCoach' => Role::where('name', 'pelatih')->first()
        ]));
    }

    public function coachCreateProcess($role, $uidUser, \TheFramework\Http\Requests\UserRequest $request)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $data = $request->validated();

            $data['uid'] = Helper::uuid();
            $data['nama_role'] = 'pelatih';
            $data['password'] = Helper::hash_password($data['password']);
            $data['is_active'] = 1; // Default aktif untuk pelatih yang didaftarkan admin

            // Check Email & Nomor
            if ($this->user->checkEmail($data['email'])) {
                throw new Exception('Email sudah terdaftar.');
            }
            if ($this->user->checkNomor($data['no_telepon'])) {
                throw new Exception('Nomor telepon sudah terdaftar.');
            }

            unset($data['password_confirm']);
            unset($data['checkbox']);

            // Handle Upload Foto Profil
            if ($request->hasFile('foto_profil')) {
                $uploadProfil = UploadHandler::upload($request->file('foto_profil'), ['uploadDir' => '/users', 'prefix' => 'profil_']);
                if ($uploadProfil['success']) {
                    $data['foto_profil'] = $uploadProfil['filename'];
                } else {
                    throw new Exception('Gagal upload foto profil: ' . $uploadProfil['error']);
                }
            } else {
                unset($data['foto_profil']);
            }

            // Handle Upload Foto KTP
            if ($request->hasFile('foto_ktp')) {
                $uploadKTP = UploadHandler::upload($request->file('foto_ktp'), ['uploadDir' => '/id_cards', 'prefix' => 'ktp_']);
                if ($uploadKTP['success']) {
                    $data['foto_ktp'] = $uploadKTP['filename'];
                } else {
                    throw new Exception('Gagal upload foto KTP: ' . $uploadKTP['error']);
                }
            } else {
                unset($data['foto_ktp']);
            }

            $this->user->addUser($data);

            return Helper::redirect("/{$role}/dashboard/management-coach", 'success', "Berhasil menambahkan pelatih: {$data['nama_lengkap']}", 10);
        } catch (Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-coach", 'error', 'Gagal tambah pelatih: ' . $e->getMessage(), 10);
        }
    }

    public function coachEditProcess($role, $uidUser, $uidCoach, \TheFramework\Http\Requests\UserRequest $request)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $oldData = $this->user->where('uid', $uidCoach)->first();
            if (!$oldData) {
                throw new Exception('Data pelatih tidak ditemukan.');
            }

            $data = $request->validated();
            $data['uid'] = $uidCoach;

            // Handle Password
            if (!empty($data['password'])) {
                $data['password'] = Helper::hash_password($data['password']);
            } else {
                unset($data['password']);
            }
            unset($data['password_confirm']);
            unset($data['checkbox']);

            // Check Duplicate Nomor (kecuali milik sendiri)
            $checkNomor = User::query()->where('no_telepon', '=', $data['no_telepon'])->where('uid', '!=', $uidCoach)->first();
            if ($checkNomor) {
                throw new Exception('Nomor telepon sudah digunakan oleh pengguna lain.');
            }

            // Handle Update Foto Profil
            if ($request->hasFile('foto_profil')) {
                $uploadProfil = UploadHandler::upload($request->file('foto_profil'), ['uploadDir' => '/users', 'prefix' => 'profil_']);
                if ($uploadProfil['success']) {
                    if ($oldData['foto_profil']) {
                        UploadHandler::delete($oldData['foto_profil'], '/users');
                    }
                    $data['foto_profil'] = $uploadProfil['filename'];
                } else {
                    throw new Exception('Gagal update foto profil: ' . $uploadProfil['error']);
                }
            } else {
                unset($data['foto_profil']);
            }

            // Handle Update Foto KTP
            if ($request->hasFile('foto_ktp')) {
                $uploadKTP = UploadHandler::upload($request->file('foto_ktp'), ['uploadDir' => '/id_cards', 'prefix' => 'ktp_']);
                if ($uploadKTP['success']) {
                    if ($oldData['foto_ktp']) {
                        UploadHandler::delete($oldData['foto_ktp'], '/id_cards');
                    }
                    $data['foto_ktp'] = $uploadKTP['filename'];
                } else {
                    throw new Exception('Gagal update foto KTP: ' . $uploadKTP['error']);
                }
            } else {
                unset($data['foto_ktp']);
            }

            $this->user->updateUser($data);

            return Helper::redirect("/{$role}/dashboard/management-coach", 'success', 'Data pelatih berhasil diperbarui.', 10);
        } catch (Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-coach", 'error', 'Gagal update pelatih: ' . $e->getMessage(), 10);
        }
    }

    public function coachDeleteProcess($role, $uidUser, $uidCoach)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $oldData = $this->user->where('uid', $uidCoach)->first();
            if (!$oldData) {
                throw new Exception('Data pelatih tidak ditemukan.');
            }

            // Hapus File jika ada
            if ($oldData['foto_profil']) {
                UploadHandler::delete($oldData['foto_profil'], '/users');
            }
            if ($oldData['foto_ktp']) {
                UploadHandler::delete($oldData['foto_ktp'], '/id_cards');
            }

            $this->user->deleteUser($uidCoach);

            return Helper::redirect("/{$role}/dashboard/management-coach", 'success', 'Data pelatih berhasil dihapus.', 10);
        } catch (Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-coach", 'error', 'Gagal hapus pelatih: ' . $e->getMessage(), 10);
        }
    }
}