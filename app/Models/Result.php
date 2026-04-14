<?php

namespace TheFramework\Models;

use TheFramework\App\Model;

class Result extends Model
{
    protected $table = 'results';
    protected $fillable = [
        'uid',
        'uid_registration',
        'waktu_akhir',
        'total_milliseconds',
        'status',
        'peringkat',
        'nama_penandatangan',
        'score'
    ];

    /**
     * Relasi ke Pendaftaran
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class, 'uid_registration', 'uid');
    }

    /**
     * Mencari Waktu Terbaik (Best Time) untuk atlet di kategori master tertentu
     * Hanya mengambil dari event yang tanggal mulainya SEBELUM event saat ini
     */
    public static function getBestTime($uidUser, $uidCategory, $currentEventDate = null)
    {
        $query = self::query()
            ->select(['results.*'])
            ->join('registrations', 'results.uid_registration', '=', 'registrations.uid')
            ->join('event_categories', 'registrations.uid_event_category', '=', 'event_categories.uid')
            ->join('events', 'event_categories.uid_event', '=', 'events.uid')
            ->where('registrations.uid_user', '=', $uidUser)
            ->where('event_categories.uid_category', '=', $uidCategory)
            ->where('results.status', '=', 'FINISH')
            ->where('results.total_milliseconds', '>', 0);

        if ($currentEventDate) {
            $query->where('events.tanggal_mulai', '<', $currentEventDate);
        }

        $best = $query->orderBy('results.total_milliseconds', 'ASC')->first();

        return $best ? $best['waktu_akhir'] : 'NT';
    }
}
