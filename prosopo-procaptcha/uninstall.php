<?php

namespace Io\Prosopo\Procaptcha;

// Check for the 'WP_UNINSTALL_PLUGIN' to prevent direct access.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

require_once __DIR__ . '/autoloader.php';

( new Procaptcha_Plugin( __DIR__ . '/prosopo-procaptcha.php' ) )->clear_data();
