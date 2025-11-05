<?php

// Check for the 'WP_UNINSTALL_PLUGIN' to prevent direct access.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

( function () {
	/**
	 * @var Io\Prosopo\Procaptcha\Procaptcha_Plugin $plugin_instance
	 */
	$plugin_instance = require __DIR__ . '/load_plugin.php';

	$plugin_instance->clear_data();
} )();
