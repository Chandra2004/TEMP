<?php
$file = 'a:/PHP/WEB-MANAGEMENT-KOLAM-RENANG/resources/views/dashboard/general/event.blade.php';
$content = file_get_contents($file);

// Kita cari anchor point sebelum kerusakan (form-edit)
// Dan anchor point sesudah kerusakan (updateMatchName)

$formStart = '<form id="form-edit-event-{{ $event[\'uid\'] }}" class="p-6 md:p-8 overflow-y-auto max-h-[75vh]"';
$afterDamage = 'const cat = this.categories.find(c => c.uid === this.matches[index].uid_category);';

// Saya akan merekonstruksi seluruh x-data modal edit
$reconstructedXData = '                        action="{{ url(\'/\' . $user[\'nama_role\'] . \'/\' . $user[\'uid\'] . \'/\' . $event[\'uid\'] . \'/dashboard/management-event/edit/process\') }}"
                        method="POST" enctype="multipart/form-data" x-data="{
                            tipe: \'berbayar\',
                            categories: {{ json_encode($categories) }},
                            master_params: {{ json_encode($requirement_parameters) }},
                            matches: {{ json_encode(
                                array_map(function ($m) use ($requirement_parameters) {
                                    return [
                                        \'uid_category\' => $m[\'uid_category\'],
                                        \'nomor_acara\' => $m[\'nomor_acara\'] ?? \'\',
                                        \'nama_acara\' => $m[\'nama_acara\'],
                                        \'tipe_biaya\' => $m[\'tipe_biaya\'],
                                        \'biaya_pendaftaran\' => $m[\'biaya_pendaftaran\'],
                                        \'jumlah_seri\' => $m[\'jumlah_seri\'],
                                        \'waktu_mulai\' => $m[\'waktu_mulai\'] ?? \'08:00\',
                                        \'requirements\' => array_map(function ($r) use ($requirement_parameters) {
                                            $paramMetadata = null;
                                            foreach ($requirement_parameters as $p) {
                                                if ($p[\'parameter_key\'] === $r[\'parameter_name\']) {
                                                    $paramMetadata = $p;
                                                    break;
                                                }
                                            }
                                            return [
                                                \'parameter_name\' => $r[\'parameter_name\'],
                                                \'operator\' => $r[\'operator\'],
                                                \'parameter_value\' => $r[\'parameter_value\'],
                                                \'input_type\' => $paramMetadata[\'input_type\'] ?? \'text\',
                                                \'options\' => json_decode($paramMetadata[\'input_options\'] ?? \'[]\', true),
                                                \'allowed_operators\' => json_decode($paramMetadata[\'allowed_operators\'] ?? \'[]\', true)
                                            ];
                                        }, isset($m[\'requirements\']) ? (is_array($m[\'requirements\']) ? $m[\'requirements\'] : (is_object($m[\'requirements\']) ? $m[\'requirements\']->all() : [])) : []),
                                    ];
                                }, $event[\'eventCategories\'] ?? []),
                            ) }},
                            addMatch() { this.matches.push({ uid_category: \'\', nomor_acara: \'\', nama_acara: \'\', tipe_biaya: \'berbayar\', biaya_pendaftaran: 0, jumlah_seri: 1, waktu_mulai: \'08:00\', requirements: [] }); },
                            updateMatchName(index) {
                                const cat = this.categories.find(c => c.uid === this.matches[index].uid_category);';

// Gunakan script PHP untuk mereplace secara aman karena tools regex saya sebelumnya gagal
file_put_contents('a:/PHP/WEB-MANAGEMENT-KOLAM-RENANG/scratch/restore_event.php', '<?php
$file = "a:/PHP/WEB-MANAGEMENT-KOLAM-RENANG/resources/views/dashboard/general/event.blade.php";
$content = file_get_contents($file);

// Cari pola yang rusak (action url sampai updateMatchName)
$pattern = "/action=\"{{ url\(\'\/.*?\)\" method=\"POST\" enctype=\"multipart\/form-data\" x-data=\"{.*?const cat = this.categories.find/s";
$replacement = \'' . addslashes($reconstructedXData) . '\';

// Bersihkan addslashes berlebih untuk \$
$replacement = str_replace("\\\$", "$", $replacement);

$newContent = preg_replace($pattern, $replacement, $content);
file_put_contents($file, $newContent);
echo "Restoration complete";
');
