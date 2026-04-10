<?php

namespace TheFramework\Http\Controllers;

use TheFramework\App\View;
use TheFramework\Helpers\Helper;
use Exception;
use TheFramework\Models\Role;
use TheFramework\Models\User;
use TheFramework\Http\Controllers\Services\ErrorController;
use TheFramework\Config\UploadHandler;

class MemberController extends DashboardController
{
    protected User $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    public function member()
    {
        return View::render('dashboard.general.member', array_merge($this->dataTetap, [
            'title' => 'Manajemen Member ' . Helper::session_get("user")['nama_role'] . ' | Khafid Swimming Club (KSC) - Official Website',
            'members' => User::query()
                ->select(['users.*', 'roles.name as nama_role', 'data_users.*', 'users.id as id', 'users.uid as uid'])
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
                ->where('roles.name', '=', 'atlet')
                ->all(),
            'roleMember' => Role::where('name', 'atlet')->first()
        ]));
    }

    public function memberCreateProcess($role, $uidUser, \TheFramework\Http\Requests\UserRequest $request)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $data = $request->validated();

            $data['uid'] = Helper::uuid();
            $data['nama_role'] = 'atlet';
            $data['password'] = Helper::hash_password($data['password']);
            $data['is_active'] = 1;

            // Check Email & Nomor
            if ($this->user->checkEmail($data['email'])) {
                throw new Exception('Email sudah terdaftar.');
            }
            if (!empty($data['no_telepon']) && $this->user->checkNomor($data['no_telepon'])) {
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

            return Helper::redirect("/{$role}/dashboard/management-member", 'success', "Berhasil menambahkan member: {$data['nama_lengkap']}", 10);
        } catch (Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-member", 'error', 'Gagal tambah member: ' . $e->getMessage(), 10);
        }
    }

    public function memberEditProcess($role, $uidUser, $uidMember, \TheFramework\Http\Requests\UserRequest $request)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $oldData = $this->user->where('uid', $uidMember)->first();
            if (!$oldData) {
                throw new Exception('Data member tidak ditemukan.');
            }

            $data = $request->validated();
            $data['uid'] = $uidMember;

            // Handle Password
            if (!empty($data['password'])) {
                $data['password'] = Helper::hash_password($data['password']);
            } else {
                unset($data['password']);
            }
            unset($data['password_confirm']);
            unset($data['checkbox']);

            // Handle Update Foto Profil
            if ($request->hasFile('foto_profil')) {
                $uploadProfil = UploadHandler::upload($request->file('foto_profil'), ['uploadDir' => '/users', 'prefix' => 'profil_']);
                if ($uploadProfil['success']) {
                    // Hapus foto lama jika ada
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
                    // Hapus foto lama jika ada
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

            return Helper::redirect("/{$role}/dashboard/management-member", 'success', 'Data member berhasil diperbarui.', 10);
        } catch (Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-member", 'error', 'Gagal update member: ' . $e->getMessage(), 10);
        }
    }

    public function memberDeleteProcess($role, $uidUser, $uidMember)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                ErrorController::error403();
            }

            $oldData = $this->user->where('uid', $uidMember)->first();
            if (!$oldData) {
                throw new Exception('Data member tidak ditemukan.');
            }

            // Hapus File
            if ($oldData['foto_profil']) {
                UploadHandler::delete($oldData['foto_profil'], '/users');
            }
            if ($oldData['foto_ktp']) {
                UploadHandler::delete($oldData['foto_ktp'], '/id_cards');
            }

            $this->user->deleteUser($uidMember);

            return Helper::redirect("/{$role}/dashboard/management-member", 'success', 'Data member berhasil dihapus.', 10);
        } catch (Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-member", 'error', 'Gagal hapus member: ' . $e->getMessage(), 10);
        }
    }
}