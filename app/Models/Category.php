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
class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uid',
        'nama_kategori',
        'kode_ku',
        'slug_kategori'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function createCategory($data){
        try {
            $this->db->beginTransaction();
            $createCategory = $this->query()->insert($data);
            $this->db->commit();
            return $createCategory;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateCategory($data){
        try {
            $this->db->beginTransaction();
            $updateCategory = $this->query()->where('uid', '=', $data['uid'])->update($data);
            $this->db->commit();
            return $updateCategory;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
            //throw $th;
        }
    }

    public function deleteCategory($data){
        try {
            $this->db->beginTransaction();
            $deleteCategory = $this->query()->where('uid', '=', $data['uid'])->delete();
            $this->db->commit();
            return $deleteCategory;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
            //throw $th;
        }
    }

}