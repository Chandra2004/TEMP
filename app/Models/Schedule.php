<?php

namespace TheFramework\Models;

use TheFramework\App\Model;

class Schedule extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'id';

    public function addSchedule(array $data): bool
    {
        return $this->insert($data);
    }

    public function updateSchedule(array $data, string $uid): bool
    {
        return $this->where('uid', '=', $uid)->update($data);
    }

    public function deleteSchedule(string $uid): bool
    {
        return $this->where('uid', '=', $uid)->delete();
    }

    /**
     * Cari slot terkecil yang masih kosong untuk kategori tertentu
     */
    public static function findFirstAvailableSlot(string $uidEventCategory, int $maxLanes, int $maxSeri = 1): array
    {
        // Ambil semua slot yang sudah terisi untuk kategori ini
        $occupiedSlots = self::query()
            ->select(['nomor_seri', 'nomor_lintasan'])
            ->join('registrations', 'schedules.uid_registration', '=', 'registrations.uid')
            ->where('registrations.uid_event_category', '=', $uidEventCategory)
            ->orderBy('nomor_seri', 'ASC')
            ->orderBy('nomor_lintasan', 'ASC')
            ->all();

        // Mapping slot terisi ke format string "seri-lintasan" buat pengecekan cepat
        $occupiedMap = [];
        foreach ($occupiedSlots as $slot) {
            $occupiedMap[] = $slot['nomor_seri'] . '-' . $slot['nomor_lintasan'];
        }

        // Loop cari slot kosong berdasarkan batas Seri dan Lintasan
        for ($seri = 1; $seri <= $maxSeri; $seri++) {
            for ($lintasan = 1; $lintasan <= $maxLanes; $lintasan++) {
                $checkKey = $seri . '-' . $lintasan;
                if (!in_array($checkKey, $occupiedMap)) {
                    return [
                        'nomor_seri' => $seri,
                        'nomor_lintasan' => $lintasan
                    ];
                }
            }
        }

        // Fallback jika entah bagaimana logic pendaftaran lolos tapi slot penuh
        return ['nomor_seri' => $maxSeri, 'nomor_lintasan' => $maxLanes];
    }
}
