<?php
$file = 'a:/PHP/WEB-MANAGEMENT-KOLAM-RENANG/resources/views/dashboard/general/event.blade.php';
$content = file_get_contents($file);

// Fix mapping
$content = str_replace(
    "'uid_category' => \$m['uid_category'],",
    "'uid_category' => \$m['uid_category'],\n                                         'nomor_acara' => \$m['nomor_acara'] ?? '',",
    $content
);

$content = str_replace(
    "'nama_acara' => '',",
    "'nomor_acara' => '', 'nama_acara' => '',",
    $content
);

file_put_contents($file, $content);
echo "Updated event.blade.php";
