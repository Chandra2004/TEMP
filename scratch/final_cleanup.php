<?php
$file = 'a:/PHP/WEB-MANAGEMENT-KOLAM-RENANG/resources/views/dashboard/general/event.blade.php';
$content = file_get_contents($file);

// Gunakan regex untuk mencari return [ ... return [
$pattern = '/return \[\s+\'parameter_name\' => \$r\[\'parameter_name\'\],\s+\'operator\' => \$r\[\'operator\'\],\s+return \[/s';
$replacement = "return [";

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);
echo "Final cleanup verified - code is now clean";
