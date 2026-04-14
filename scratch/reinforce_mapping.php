<?php
$file = 'a:/PHP/WEB-MANAGEMENT-KOLAM-RENANG/resources/views/dashboard/general/event.blade.php';
$content = file_get_contents($file);

// 1. Perbaiki mapping requirements agar benar-benar presisi
$replacement = "'requirements' => array_map(function (\$r) use (\$requirement_parameters) {
                                            \$rData = is_object(\$r) ? \$r->toArray() : \$r;
                                            \$pName = (string)(\$rData['parameter_name'] ?? '');
                                            \$paramMetadata = null;
                                            foreach (\$requirement_parameters as \$p) {
                                                if ((string)\$p['parameter_key'] === \$pName) {
                                                    \$paramMetadata = \$p;
                                                    break;
                                                }
                                            }
                                            return [
                                                'parameter_name' => \$pName,
                                                'operator' => (string)(\$rData['operator'] ?? '='),
                                                'parameter_value' => \$rData['parameter_value'],
                                                'input_type' => \$paramMetadata['input_type'] ?? 'text',
                                                'options' => json_decode(\$paramMetadata['input_options'] ?? '[]', true),
                                                'allowed_operators' => json_decode(\$paramMetadata['allowed_operators'] ?? '[]', true)
                                            ];
                                        }, isset(\$m['requirements']) ? (is_array(\$m['requirements']) ? \$m['requirements'] : (is_object(\$m['requirements']) ? \$m['requirements']->all() : [])) : []),";

$pattern = "/'requirements' => array_map\(function \(\\\$r\) use \(\\\$requirement_parameters\) {.*?}, isset\(.*?\) : \[\]\) : \[\]\),/s";
$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);
echo "Data mapping reinforcement complete";
