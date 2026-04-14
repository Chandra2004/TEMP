$path = "a:\PHP\WEB-MANAGEMENT-KOLAM-RENANG\resources\views\dashboard\general\event.blade.php"
$content = Get-Content $path -Raw

$oldFragment = 'title="Export Buku Acara">
                                                <i data-lucide="printer" class="w-4 h-4"></i>
                                            </a>'

$newButton = '
                                            <a href="{{ url(\'/\' . $user[\'nama_role\'] . \'/dashboard/management-event/\' . $event[\'uid\'] . \'/export-buku-hasil\') }}"
                                                target="_blank"
                                                class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm active:scale-90"
                                                title="Export Buku Hasil">
                                                <i data-lucide="award" class="w-4 h-4"></i>
                                            </a>'

$replacement = $oldFragment + $newButton

if ($content -match [regex]::Escape($oldFragment)) {
    $content = $content -replace [regex]::Escape($oldFragment), $replacement
    Set-Content $path $content -NoNewline
    Write-Host "Successfully updated the file."
} else {
    Write-Host "Could not find the target fragment."
}
