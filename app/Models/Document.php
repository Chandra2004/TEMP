<?php

namespace TheFramework\Models;

use TheFramework\App\Model;

class Document extends Model
{
    protected $table = 'document';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uid',
        'uid_event',
        'judul',
        'deskripsi',
        'logo_kiri',
        'logo_kanan',
        'file_path'
    ];

    public function addDocument(array $data): bool
    {
        return $this->insert($data);
    }

    public function updateDocument(array $data, string $uid): bool
    {
        return $this->update($data, ['uid' => $uid]);
    }

    public function deleteDocument(string $uid): bool
    {
        return $this->delete(['uid' => $uid]);
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'uid_event', 'uid');
    }
}
