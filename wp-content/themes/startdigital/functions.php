<?php

use TheStart\Core\Bootstrap;

$composer_autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composer_autoload)) {
	require_once $composer_autoload;
}

new Bootstrap();

// Do not add Anything here