<?php
echo 'Web PHP:' . PHP_EOL;
echo 'imagick extension_loaded: ' . (extension_loaded('imagick') ? 'YES' : 'NO') . PHP_EOL;
echo 'Imagick class_exists: ' . (class_exists('Imagick') ? 'YES' : 'NO') . PHP_EOL;
echo 'php.ini: ' . php_ini_loaded_file() . PHP_EOL;
echo 'PHP Version: ' . phpversion() . PHP_EOL;
echo 'SAPI: ' . php_sapi_name() . PHP_EOL;