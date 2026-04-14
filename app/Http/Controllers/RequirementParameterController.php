<?php

namespace TheFramework\Http\Controllers;

use TheFramework\Helpers\Helper;
use TheFramework\Models\RequirementParameter;
use TheFramework\Http\Requests\RequirementParameterRequest;
use TheFramework\App\View;
use TheFramework\Models\User;

class RequirementParameterController extends Controller
{
    public function index($role)
    {
        $parameters = RequirementParameter::query()->paginate(10, $_GET['page'] ?? 1);
        $user = User::query()
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
            ->where('users.uid', '=', Helper::session_get('user')['uid'])
            ->first();

        if (!$user) {
            return Helper::redirect('/login', 'error', 'Sesi berakhir, silakan login kembali');
        }

        $totalUnreadNotification = \TheFramework\Models\Notification::query()
            ->where('is_read', '=', 0)
            ->where('uid_user', '=', $user->uid)
            ->count();
        $unReadNotification = \TheFramework\Models\Notification::query()
            ->where('is_read', '=', 0)
            ->where('uid_user', '=', $user->uid)
            ->all();

        return View::render('dashboard.general.requirement-parameter', [
            'user' => $user,
            'role' => $role,
            'parameters' => $parameters,
            'totalUnreadNotification' => $totalUnreadNotification,
            'unReadNotification' => $unReadNotification,
            'notification' => Helper::get_flash('notification'),
            'title' => 'Manajemen Master Parameter Lomba'
        ]);
    }

    public function createProcess($role, $uidUser, RequirementParameterRequest $request)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                Helper::json(['status' => 'error', 'message' => 'Unauthorized'], 403);
                exit;
            }

            $data = $request->validated();
            $data['uid'] = Helper::uuid();
            $data['is_active'] = isset($data['is_active']) ? 1 : 0;

            RequirementParameter::query()->insert($data);

            return Helper::redirect("/{$role}/dashboard/management-requirement-parameter", 'success', "Parameter berhasil ditambahkan");
        } catch (\Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-requirement-parameter", 'error', "Gagal menambahkan parameter: " . $e->getMessage());
        }
    }

    public function editProcess($role, $uidUser, $uidParam, RequirementParameterRequest $request)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                Helper::json(['status' => 'error', 'message' => 'Unauthorized'], 403);
                exit;
            }

            $data = $request->validated();
            $data['is_active'] = isset($data['is_active']) ? 1 : 0;

            RequirementParameter::query()->where('uid', '=', $uidParam)->update($data);

            return Helper::redirect("/{$role}/dashboard/management-requirement-parameter", 'success', "Parameter berhasil diperbarui");
        } catch (\Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-requirement-parameter", 'error', "Gagal memperbarui parameter: " . $e->getMessage());
        }
    }

    public function deleteProcess($role, $uidUser, $uidParam)
    {
        try {
            if (User::authorizeAction($role, $uidUser) === false) {
                Helper::json(['status' => 'error', 'message' => 'Unauthorized'], 403);
                exit;
            }

            RequirementParameter::query()->where('uid', '=', $uidParam)->delete();

            return Helper::redirect("/{$role}/dashboard/management-requirement-parameter", 'success', "Parameter berhasil dihapus");
        } catch (\Exception $e) {
            return Helper::redirect("/{$role}/dashboard/management-requirement-parameter", 'error', "Gagal menghapus parameter: " . $e->getMessage());
        }
    }
}
