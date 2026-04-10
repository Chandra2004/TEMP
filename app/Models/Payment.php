<?php

namespace TheFramework\Models;

use Exception;
use TheFramework\App\Model;

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
class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uid',
        'uid_registration',
        'amount',
        'payment_method',
        'status_pembayaran',
        'catatan_admin',
        'tanggal_pembayaran',
        'bukti_pembayaran',
    ];

    protected $hidden = [
        'uid_registration',
        'created_at',
        'updated_at'
    ];

    public function addPayment($data)
    {
        try {
            $this->db->beginTransaction();
            $addPayment = $this->query()->insert($data);
            $this->db->commit();
            return $addPayment;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updatePayment($data, $id)
    {
        try {
            $this->db->beginTransaction();
            $updatePayment = $this->query()->where('uid', '=', $id)->update($data);
            $this->db->commit();
            return $updatePayment;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deletePayment($uid)
    {
        try {
            $this->db->beginTransaction();
            $delete = $this->query()->where('uid', '=', $uid)->delete();
            $this->db->commit();
            return $delete;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}