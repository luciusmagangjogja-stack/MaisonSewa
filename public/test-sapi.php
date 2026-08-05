<?php
// Test what getcwd() returns in built-in server context
echo "getcwd: " . getcwd() . PHP_EOL;
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . PHP_EOL;
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . PHP_EOL;
echo "PHP_SAPI: " . php_sapi_name() . PHP_EOL;
