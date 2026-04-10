<?php

namespace TheFramework\Middleware;

use TheFramework\App\SessionManager;
use TheFramework\Helpers\Helper;
use TheFramework\Http\Controllers\Services\ErrorController;
use TheFramework\Models\User;

class RoleMiddleware implements Middleware {
    private $allowedRoles = [];

    public function __construct(array $allowedRoles) {
        // Normalize semua allowed roles ke lowercase
        $this->allowedRoles = array_map('strtolower', $allowedRoles);
    }

    public function before() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            SessionManager::startSecureSession();
        }

        $user_session = Helper::session_get('user');

        // 1. Cek User Session
        if (empty($user_session) || !isset($user_session['uid'])) {
            ErrorController::error403();
            exit();
        }
        
        // 2. Ambil user instance dan cek role menggunakan method hasRole()
        $user = User::find($user_session['id']);
        
        $currentRole = null;
        if ($user) {
            foreach ($this->allowedRoles as $role) {
                if ($user->hasRole($role)) {
                    $currentRole = $role;
                    break;
                }
            }
        }

        // 0. Superadmin bypass (ALL IN)
        if ($user && $user->hasRole('superadmin')) {
            return;
        }

        if (!$currentRole) {
            error_log("Access Denied: User " . $user_session['uid'] . " has no authorized role.");
        }

        // 3. Ambil role yang diminta dari URL segment pertama
        $roleTargetDiURL = strtolower(Helper::getUriSegment(1) ?? '');

        // 4. VALIDASI TAHAP 1: Apakah role user ada dalam daftar yang diizinkan?
        if (is_null($currentRole) || !in_array($currentRole, $this->allowedRoles)) {
            ErrorController::error403();
            exit();
        }

        // 5. VALIDASI TAHAP 2 (SMART): Proteksi Silang Link
        // Jika URL segment 1 adalah salah satu role yang kita jaga (misal: 'admin')
        // Tapi role user tersebut bukan 'admin', maka blokir.
        if (in_array($roleTargetDiURL, $this->allowedRoles)) {
            if ($currentRole !== $roleTargetDiURL) {
                // Member mencoba buka milik Admin atau sebaliknya
                ErrorController::error403();
                exit();
            }
        }
    }

    public function after() {}
}