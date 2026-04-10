<?php

namespace TheFramework\Models;

use Exception;
use TheFramework\App\Model;

use function PHPUnit\Framework\throwException;

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
class Event extends Model
{
    protected $table = 'events';
    // INI ADALAH PERBAIKAN FINAL. Mengubah primary key agar sesuai dengan penggunaan di seluruh aplikasi.
    protected $primaryKey = 'uid';

    protected $fillable = [
        'uid',
        'nama_event',
        'banner_event',
        'logo_kiri',
        'logo_kanan',
        'deskripsi',
        'lokasi_event',
        'waktu_event',
        'tanggal_event',
        'biaya_event',
        'status_event',
        'kuota_peserta',
        'tipe_event',
        'slug',
        'uid_klub',
        'uid_author',
        'uid_payment_method',
        'jumlah_lintasan'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'uid_kategori',
        'uid_author',
        // 'slug'
    ];

    public function eventCategories()
    {
        return $this->hasMany(EventCategory::class, 'uid_event', 'uid');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'uid', 'uid_kategori')->select(['nama_kategori']);
    }

    public function author()
    {
        return $this->hasOne(User::class, 'uid', 'uid_author')
            ->join('data_users', 'users.uid', '=', 'data_users.uid_user')
            ->select(['users.uid', 'data_users.nama_lengkap']);
    }

    public function addEvent($data)
    {
        try {
            $this->db->beginTransaction();
            $addUser = $this->query()->insert($data);
            $this->db->commit();
            return $addUser;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateEvent($data, $uid)
    {
        try {
            $this->db->beginTransaction();
            $updateEvent = $this->query()->where('uid', '=', $uid)->update($data);
            $this->db->commit();
            return $updateEvent;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteEvent($uid)
    {
        try {
            $this->db->beginTransaction();
            $deleteEvent = $this->query()->where('uid', '=', $uid)->delete();
            $this->db->commit();
            return $deleteEvent;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}

