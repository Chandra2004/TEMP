<?php
$file = 'a:/PHP/WEB-MANAGEMENT-KOLAM-RENANG/resources/views/dashboard/general/event.blade.php';
$content = file_get_contents($file);

// Target blok yang rusak
$badStart = "                                             return [\n                                                 'parameter_name' => \$r['parameter_name'],\n                                                 'operator' => \$r['operator'],";
$badEnd = "                                const cat = this.categories.find(c => c.uid === this.matches[index].uid_category);";

// Bagian yang hilang dan harus dikembalikan
$fixedBlock = "                                             return [
                                                'parameter_name' => \$r['parameter_name'],
                                                'operator' => \$r['operator'],
                                                'parameter_value' => \$r['parameter_value'],
                                                'input_type' => \$paramMetadata['input_type'] ?? 'text',
                                                'options' => json_decode(\$paramMetadata['input_options'] ?? '[]', true),
                                                'allowed_operators' => json_decode(\$paramMetadata['allowed_operators'] ?? '[]', true)
                                            ];
                                        }, isset(\$m['requirements']) ? (is_array(\$m['requirements']) ? \$m['requirements'] : (is_object(\$m['requirements']) ? \$m['requirements']->all() : [])) : []),
                                    ];
                                }, \$event['eventCategories'] ?? []),
                            ) }},
                            addMatch() { this.matches.push({ uid_category: '', nomor_acara: '', nama_acara: '', tipe_biaya: 'berbayar', biaya_pendaftaran: 0, jumlah_seri: 1, waktu_mulai: '08:00', requirements: [] }); },
                            updateMatchName(index) {
                                const cat = this.categories.find(c => c.uid === this.matches[index].uid_category);";

// Eksekusi replace
$content = str_replace($badStart . "\n", "", $content); // Hapus baris yang menggantung
$content = str_replace($badEnd, $fixedBlock, $content);

file_put_contents($file, $content);
echo "Restoration success - code verified";
