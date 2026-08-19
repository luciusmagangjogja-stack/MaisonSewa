<?php
$content = file_get_contents('resources/views/rentals/partials/premium-doc-head.blade.php');
preg_match_all('/<style>(.*?)<\/style>/s', $content, $matches);
$style = $matches[1][0] ?? '';
$open = substr_count($style, '{');
$close = substr_count($style, '}');
echo "Open braces: $open\n";
echo "Close braces: $close\n";
if ($open !== $close) {
    echo "ERROR: Unmatched braces!\n";
} else {
    echo "OK: Braces match.\n";
}
