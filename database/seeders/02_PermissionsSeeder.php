<?php

namespace Database\Seeders;

use TheFramework\Database\Seeder;
use TheFramework\Helpers\Helper;
use TheFramework\App\Database;
use TheFramework\App\Schema;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $db = Database::getInstance();

        // 1. Daftar Permission
        $permissions = [
            'all-access',
            'manage-users',
            'manage-roles',
            'manage-events',
            'manage-categories',
            'manage-payments',
            'manage-galleries',
            'view-dashboard',
            'register-event',
            'view-results',
            'manage-registrations',
            'view-reports',
            'manage-members'
        ];

        $insertPerms = [];
        foreach ($permissions as $name) {
            $insertPerms[] = [
                'uid' => Helper::uuid(),
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => Helper::updateAt(),
                'updated_at' => Helper::updateAt(),
            ];
        }

        Schema::insert('permissions', $insertPerms);

        // Ambil ID yang baru saja diinsert
        $db->query("SELECT id, name FROM permissions");
        $allPerms = $db->resultSet();
        $permIds = [];
        foreach ($allPerms as $p) {
            $permIds[$p['name']] = $p['id'];
        }

        // 2. Ambil Role IDs
        $db->query("SELECT id, name FROM roles");
        $roles = $db->resultSet();
        $roleIds = [];
        foreach ($roles as $role) {
            $roleIds[$role['name']] = $role['id'];
        }

        // 3. Mapping Role ke Permission (role_has_permissions)
        $mapping = [
            'superadmin' => $permissions, // Semua akses
            'admin' => [
                'manage-users',
                'manage-events',
                'manage-categories',
                'manage-payments',
                'manage-galleries',
                'view-dashboard',
                'view-results',
                'manage-registrations',
                'view-reports',
                'manage-members'
            ],
            'pelatih' => [
                'manage-events',
                'manage-categories',
                'manage-galleries',
                'view-dashboard',
                'view-results',
                'manage-registrations',
                'view-reports',
                'manage-members'
            ],
            'atlet' => [
                'view-dashboard',
                'register-event',
                'view-results'
            ]
        ];

        foreach ($mapping as $roleName => $perms) {
            if (!isset($roleIds[$roleName])) continue;

            $insertData = [];
            foreach ($perms as $pName) {
                $insertData[] = [
                    'permission_id' => $permIds[$pName],
                    'role_id' => $roleIds[$roleName]
                ];
            }
            
            if (!empty($insertData)) {
                Schema::insert('role_has_permissions', $insertData);
            }
        }
    }
}
