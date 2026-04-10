<?php

namespace TheFramework\Middleware;

use TheFramework\App\SessionManager;
use TheFramework\Helpers\Helper;
use TheFramework\Http\Controllers\Services\ErrorController;
use TheFramework\Models\User;

class PermissionMiddleware implements Middleware {
    private $permission;

    public function __construct(string $permission) {
        $this->permission = $permission;
    }

    public function before() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            SessionManager::startSecureSession();
        }

        $user_session = Helper::session_get('user');

        if (empty($user_session) || !isset($user_session['id'])) {
            ErrorController::error403();
            exit();
        }
        
        // Ambil instance user dari DB untuk cek permission secara real-time
        $user = User::query()->where('id', '=', $user_session['id'])->first();
        
        if (!$user || !$user->can($this->permission)) {
            error_log("Access Denied: User " . $user_session['uid'] . " does not have permission: " . $this->permission);
            ErrorController::error403();
            exit();
        }
    }

    public function after() {}
}
