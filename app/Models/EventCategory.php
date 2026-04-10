<?php

namespace TheFramework\Models;

use TheFramework\App\Model;

class EventCategory extends Model
{
    protected $table = 'event_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uid',
        'uid_event',
        'uid_category',
        'nomor_acara',
        'nama_acara',
        'tanggal_mulai',
        'waktu_mulai',
        'tipe_biaya',
        'biaya_pendaftaran',
        'jumlah_seri',
        'lokasi'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'uid_event', 'uid');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'uid_category', 'uid');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'uid_event_category', 'uid');
    }

    public function requirements()
    {
        return $this->hasMany(CategoryRequirement::class, 'uid_event_category', 'uid');
    }
}
