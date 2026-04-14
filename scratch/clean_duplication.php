<?php
$file = 'a:/PHP/WEB-MANAGEMENT-KOLAM-RENANG/resources/views/dashboard/general/event.blade.php';
$content = file_get_contents($file);

// Targetkan bagian yang terduplikasi secara spesifik
$duplicate = "                                            return [
                                                'parameter_name' => \$r['parameter_name'],
                                                'operator' => \$r['operator'],
                                             return [";

$content = str_replace($duplicate, "                                            return [", $content);

file_put_contents($file, $content);
echo "Duplication cleared - file clean";
