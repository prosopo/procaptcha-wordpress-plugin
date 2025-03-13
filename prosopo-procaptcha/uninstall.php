<?php

namespace Io\Prosopo\Procaptcha;

// Check for the 'WP_UNINSTALL_PLUGIN' to prevent direct access.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

use Io\Prosopo\Procaptcha\Plugin\Plugin;

require_once __DIR__ . '/autoloader.php';

( new Plugin() )->clear_data();
