<?php
$file = 'a:/PHP/WEB-MANAGEMENT-KOLAM-RENANG/resources/views/dashboard/general/event.blade.php';
$content = file_get_contents($file);

// 1. Fix requirements initialization loop to be more robust
$oldCode = "'requirements' => array_map(function (\$r) use (\$requirement_parameters) {
                                             \$paramMetadata = null;
                                             foreach (\$requirement_parameters as \$p) {
                                                 if (\$p['parameter_key'] === \$r['parameter_name']) {
                                                     \$paramMetadata = \$p;
                                                     break;
                                                 }
                                             }
                                             return [
                                                 'parameter_name' => \$r['parameter_name'],
                                                 'operator' => \$r['operator'],
                                                 'parameter_value' => \$r['parameter_value'],
                                                 'input_type' => \$paramMetadata['input_type'] ?? 'text',
                                                 'options' => json_decode(\$paramMetadata['input_options'] ?? '[]', true),
                                                 'allowed_operators' => json_decode(\$paramMetadata['allowed_operators'] ?? '[]', true)
                                             ];
                                         }, \$m['requirements'] ?? []),";

$newCode = "'requirements' => array_map(function (\$r) use (\$requirement_parameters) {
                                            \$paramMetadata = null;
                                            foreach (\$requirement_parameters as \$p) {
                                                if (\$p['parameter_key'] === \$r['parameter_name']) {
                                                    \$paramMetadata = \$p;
                                                    break;
                                                }
                                            }
                                            return [
                                                'parameter_name' => \$r['parameter_name'],
                                                'operator' => \$r['operator'],
                                                'parameter_value' => \$r['parameter_value'],
                                                'input_type' => \$paramMetadata['input_type'] ?? 'text',
                                                'options' => json_decode(\$paramMetadata['input_options'] ?? '[]', true),
                                                'allowed_operators' => json_decode(\$paramMetadata['allowed_operators'] ?? '[]', true)
                                            ];
                                        }, isset(\$m['requirements']) ? (is_array(\$m['requirements']) ? \$m['requirements'] : (is_object(\$m['requirements']) ? \$m['requirements']->all() : [])) : []),";

$content = str_replace($oldCode, $newCode, $content);

// 2. Fix addMatch to include nomor_acara
$content = str_replace(
    "addMatch() { this.matches.push({ uid_category: '', nama_acara: '',",
    "addMatch() { this.matches.push({ uid_category: '', nomor_acara: '', nama_acara: '',",
    $content
);

file_put_contents($file, $content);
echo "Final fix for event.blade.php applied";
