<?php

// Check for the 'WP_UNINSTALL_PLUGIN' to prevent direct access.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * @var \Io\Prosopo\Procaptcha\Procaptcha_Plugin $prosopo_procaptcha
 */
$prosopo_procaptcha = require __DIR__ . '/load_plugin.php';

$prosopo_procaptcha->clear_data();
