<?php

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

return ( function (): Procaptcha_Plugin {
	// the scoper-autoloader includes autoloader for this namespace as well.
	require_once __DIR__ . '/prefixed-vendors/vendor/scoper-autoload.php';
	require_once __DIR__ . '/src/helpers.php';

	$is_dev_mode = defined( 'PROSOPO_PROCAPTCHA_DEV_MODE' ) && PROSOPO_PROCAPTCHA_DEV_MODE;

	$plugin_instance = new Procaptcha_Plugin( __FILE__, $is_dev_mode );

	$plugin_instance->load();

	return $plugin_instance;
} )();
