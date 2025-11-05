<?php

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

/**
 * @return Procaptcha_Plugin
 */
return ( function (): Procaptcha_Plugin {
	// the scoper-autoloader includes autoloader for this namespace as well.
	require_once __DIR__ . '/prefixed-vendors/vendor/scoper-autoload.php';
	require_once __DIR__ . '/src/helpers.php';

	$plugin_file = __DIR__ . '/prosopo-procaptcha.php';
	$is_dev_mode = defined( 'PROSOPO_PROCAPTCHA_DEV_MODE' ) && PROSOPO_PROCAPTCHA_DEV_MODE;

	return new Procaptcha_Plugin( $plugin_file, $is_dev_mode );
} )();
