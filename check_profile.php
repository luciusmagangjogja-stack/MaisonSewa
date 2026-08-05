<?php
$html = file_get_contents('http://localhost/profile');
file_put_contents('profile_check.html', $html);
echo "HTML saved, length: " . strlen($html) . "\n";

// Find the email input line
$lines = explode("\n", $html);
foreach ($lines as $i => $line) {
    if (strpos($line, 'email') !== false && strpos($line, 'pl-') !== false) {
        echo "Line " . ($i+1) . ": " . trim($line) . "\n";
    }
}
