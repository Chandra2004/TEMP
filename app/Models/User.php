<?php

namespace TheFramework\Models;

use Exception;
use TheFramework\App\Model;
use TheFramework\Helpers\Helper;

/**
 * @method static \TheFramework\App\QueryBuilder query()
 * @method static array all()
 * @method static mixed find($id)
 * @method static mixed where($column, $value)
 * @method static mixed insert(array $data)
 * @method static mixed update(array $data, $id)
 * @method static mixed delete($id)
 * @method static mixed paginate(int $perPage = 10, int $page = 1)
 * @method static static with(array $relations)
 */
class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uid',
        'username',
        'email',
        'password',
        'is_active',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * Get profile completion status and missing fields.
     */
    public function getProfileCompletion(string $uid = null): array
    {
        $uid = $uid ?? $this->uid;
        
        $profile = (new \TheFramework\App\QueryBuilder(\TheFramework\App\Database::getInstance()))
            ->table('data_users')
            ->where('uid_user', '=', $uid)
            ->first();

        // Daftar kolom wajib (Essential Fields)
        $requiredFields = [
            'nama_lengkap' => 'Nama Lengkap', 
            'tanggal_lahir' => 'Tanggal Lahir', 
            'tempat_lahir' => 'Tempat Lahir', 
            'jenis_kelamin' => 'Jenis Kelamin', 
            'alamat_lengkap' => 'Alamat Lengkap', 
            'no_telepon' => 'No Telepon', 
            'nomor_ktp' => 'Nomor KTP', 
            'foto_profil' => 'Foto Profil', 
            'foto_ktp' => 'Foto KTP'
        ];

        if (!$profile) {
            return [
                'complete' => false, 
                'percentage' => 0, 
                'missing' => array_values($requiredFields),
                'is_complete' => false
            ];
        }

        $filledCount = 0;
        $missingLabels = [];

        foreach ($requiredFields as $field => $label) {
            if (!empty($profile[$field])) {
                $filledCount++;
            } else {
                $missingLabels[] = $label;
            }
        }

        $percentage = round(($filledCount / count($requiredFields)) * 100);

        return [
            'complete' => ($filledCount === count($requiredFields)),
            'percentage' => (int)$percentage,
            'missing' => $missingLabels,
            'is_complete' => ($filledCount === count($requiredFields))
        ];
    }

    public function checkEmail($identity)
    {
        return $this->query()
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
            ->where('users.email', '=', $identity)
            ->orWhere('users.username', '=', $identity)
            ->first();
    }

    public static function authorizeAction(string $role, string $uidUser): bool
    {
        $user = Helper::session_get('user');
        
        // Superadmin bypass
        if (isset($user['nama_role']) && $user['nama_role'] === 'superadmin') {
            return true;
        }

        return $user['nama_role'] === $role && $user['uid'] === $uidUser;
    }

    public function checkNomor($no_telepon)
    {
        return $this->query()
            ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
            ->where('data_users.no_telepon', '=', $no_telepon)
            ->first();
    }

    public function addUser($data)
    {
        try {
            $this->db->beginTransaction();
            
            $userData = [
                'uid' => $data['uid'] ?? Helper::uuid(),
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'is_active' => $data['is_active'] ?? 1,
            ];
            
            $this->query()->insert($userData);

            // 1.5 Assign Role (New RBAC)
            $user = $this->query()->where('uid', '=', $userData['uid'])->first();
            if ($user && isset($data['nama_role'])) {
                $instance = self::find($user->id);
                $instance->assignRole($data['nama_role']);
            } elseif ($user && isset($data['uid_role'])) {
                // Fallback jika masih pakai uid_role di input
                $role = Role::where('uid', $data['uid_role'])->first();
                if ($role) {
                     $instance = self::find($user->id);
                     $instance->assignRole($role->name);
                }
            }

            // 2. Data untuk tabel data_users
            $profileData = [
                'uid' => Helper::uuid(),
                'uid_user' => $userData['uid'],
                'nama_lengkap' => $data['nama_lengkap'] ?? $data['username'],
                'no_telepon' => $data['no_telepon'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'tempat_lahir' => $data['tempat_lahir'] ?? null,
                'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                'alamat_lengkap' => $data['alamat'] ?? $data['alamat_lengkap'] ?? null,
                'klub_renang' => $data['nama_klub'] ?? $data['klub_renang'] ?? null,
                'foto_profil' => $data['foto_profil'] ?? null,
                'foto_ktp' => $data['foto_ktp'] ?? null,
                'is_active' => 1
            ];

            \TheFramework\App\Schema::insert('data_users', [$profileData]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateUser($data)
    {
        try {
            $this->db->beginTransaction();
            
            $uid = $data['uid'];
            
            // 1. Update Tabel users
            $userData = [];
            if(isset($data['username'])) $userData['username'] = $data['username'];
            if(isset($data['email'])) $userData['email'] = $data['email'];
            if(isset($data['password'])) $userData['password'] = $data['password'];
            if(isset($data['is_active'])) $userData['is_active'] = $data['is_active'];

            if(!empty($userData)) {
                $this->query()->where('uid', '=', $uid)->update($userData);
            }

            // Update Role if provided
            if(isset($data['nama_role'])) {
                $user = self::where('uid', '=', $uid)->first();
                if ($user) {
                    // Clear old roles first if needed, but for now just assign
                    $user->assignRole($data['nama_role']);
                }
            }

            // 2. Update Tabel data_users
            $profileData = [];
            $profileCols = [
                'nama_lengkap', 'nama_panggilan', 'tanggal_lahir', 'tempat_lahir', 'jenis_kelamin', 
                'alamat_lengkap', 'kota', 'provinsi', 'kode_pos', 'no_telepon', 'no_telepon_darurat', 'email',
                'nomor_ktp', 'nomor_kk', 'foto_profil', 'foto_ktp', 'foto_akta',
                'tinggi_badan', 'berat_badan', 'golongan_darah', 'riwayat_penyakit', 'alergi', 'obat_rutin', 'vaksin_covid',
                'pengalaman_renang', 'tingkat_keahlian', 'prestasi_renang', 'klub_renang', 'pelatih_renang',
                'nama_ayah', 'nama_ibu', 'pekerjaan_ayah', 'pekerjaan_ibu', 'sekolah', 'kelas',
                'uid_klub', 'jabatan_klub', 'is_active'
            ];
            foreach($profileCols as $col) {
                if(isset($data[$col])) $profileData[$col] = $data[$col];
            }

            // Fallback for 'alamat' field which is often used in forms but named 'alamat_lengkap' in DB
            if (isset($data['alamat']) && !isset($data['alamat_lengkap'])) {
                $profileData['alamat_lengkap'] = $data['alamat'];
            }

            if (isset($data['nama_klub']) && !isset($data['klub_renang'])) {
                $profileData['klub_renang'] = $data['nama_klub'];
            }

            if(!empty($profileData)) {
                $checkHeader = (new \TheFramework\App\QueryBuilder($this->db))->table('data_users')->where('uid_user', '=', $uid)->first();
                if($checkHeader) {
                    (new \TheFramework\App\QueryBuilder($this->db))->table('data_users')->where('uid_user', '=', $uid)->update($profileData);
                } else {
                    $profileData['uid'] = Helper::uuid();
                    $profileData['uid_user'] = $uid;
                    \TheFramework\App\Schema::insert('data_users', [$profileData]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteUser($data)
    {
        try {
            $this->db->beginTransaction();
            $deleteUser = $this->query()->where('uid', '=', $data)->delete();
            $this->db->commit();
            return $deleteUser;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updatePassword($data)
    {
        unset($data['token']);
        unset($data['password_confirm']);
        try {
            $this->db->beginTransaction();

            $updatePassword = $this->query()
                ->where('email', '=', $data['email'])
                ->update($data);

            $this->db->commit();
            if ($updatePassword == 1) {
                return true;
            } else if ($updatePassword == 0) {
                return false;
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function checkKredensial($role, $uidUser)
    {
        $dataValid = $this->query()
            ->select([
                'users.*',
                'roles.name'
            ])
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', $role)
            ->where('users.uid', $uidUser)
            ->first();

        return !is_null($dataValid);
    }

    public function updateMyProfile($data)
    {
        return $this->updateUser($data);
    }

    /**
     * RBAC System - Spatie Like Implementation
     */

    public static function auth(): ?self
    {
        $session = Helper::session_get('user');
        if (!$session || !isset($session['id'])) return null;
        return self::find($session['id']);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        $placeholders = [];
        foreach ($roles as $i => $role) {
            $placeholders[] = ":role_" . $i;
        }
        
        $sql = "SELECT COUNT(*) as total 
                FROM roles 
                JOIN model_has_roles ON roles.id = model_has_roles.role_id 
                WHERE model_has_roles.model_id = :user_id 
                AND roles.name IN (" . implode(',', $placeholders) . ")";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $this->id);
        foreach ($roles as $i => $role) {
            $this->db->bind(':role_' . $i, $role);
        }
        
        $result = $this->db->single();
        return (int)($result['total'] ?? 0) > 0;
    }

    public function hasPermissionTo(string $permission): bool
    {
        $sql = "SELECT p.id 
                FROM permissions p
                LEFT JOIN model_has_permissions mhp ON p.id = mhp.permission_id AND mhp.model_id = :uid_dir
                LEFT JOIN role_has_permissions rhp ON p.id = rhp.permission_id
                LEFT JOIN model_has_roles mhr ON rhp.role_id = mhr.role_id AND mhr.model_id = :uid_role
                WHERE p.name = :perm_name 
                AND (mhp.model_id IS NOT NULL OR mhr.model_id IS NOT NULL)
                LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(':uid_dir', $this->id);
        $this->db->bind(':uid_role', $this->id);
        $this->db->bind(':perm_name', $permission);
        
        return !empty($this->db->single());
    }

    public function can(string $permission): bool
    {
        // Superadmin bypass (God Mode)
        if ($this->hasRole('superadmin')) {
            return true;
        }

        return $this->hasPermissionTo($permission);
    }

    public function hasAnyPermission(...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission)) return true;
        }
        return false;
    }

    /**
     * Assign a role to the user
     */
    public function assignRole(string $roleName): bool
    {
        $db = \TheFramework\App\Database::getInstance();
        $db->query("SELECT id FROM roles WHERE name = :name");
        $db->bind(':name', $roleName);
        $role = $db->single();

        if (!$role) return false;

        // Check if already assigned
        $db->query("SELECT 1 FROM model_has_roles WHERE role_id = :rid AND model_id = :uid AND model_type = 'User'");
        $db->bind(':rid', $role['id']);
        $db->bind(':uid', $this->id);
        if ($db->single()) return true;

        \TheFramework\App\Schema::insert('model_has_roles', [
            [
                'role_id' => $role['id'],
                'model_type' => 'User',
                'model_id' => $this->id,
            ]
        ]);

        return true;
    }

    /**
     * Give direct permission to the user
     */
    public function givePermissionTo(string $permissionName): bool
    {
        $db = \TheFramework\App\Database::getInstance();
        $db->query("SELECT id FROM permissions WHERE name = :name");
        $db->bind(':name', $permissionName);
        $permission = $db->single();

        if (!$permission) return false;

        // Check if already assigned
        $db->query("SELECT 1 FROM model_has_permissions WHERE permission_id = :pid AND model_id = :uid AND model_type = 'User'");
        $db->bind(':pid', $permission['id']);
        $db->bind(':uid', $this->id);
        if ($db->single()) return true;

        \TheFramework\App\Schema::insert('model_has_permissions', [
            [
                'permission_id' => $permission['id'],
                'model_type' => 'User',
                'model_id' => $this->id,
            ]
        ]);

        return true;
    }
}
