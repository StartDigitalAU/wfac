<?php

use TheStart\Core\Bootstrap;

$composer_autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composer_autoload)) {
	require_once $composer_autoload;
}

new Bootstrap();

// Do not add Anything here

add_filter('upload_dir', function ($dirs) {
	if ($_SERVER['HTTP_HOST'] === 'wfac.test') {
		$dirs['baseurl'] = 'https://wfac.org.au/wp-content/uploads';
		$dirs['url']     = $dirs['baseurl'] . $dirs['subdir'];
	}
	return $dirs;
});
