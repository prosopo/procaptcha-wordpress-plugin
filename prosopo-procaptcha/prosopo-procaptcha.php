<?php
/**
 * Plugin Name: Prosopo Procaptcha
 * Description: GDPR compliant, privacy friendly and better value captcha.
 * Version: 1.9.0
 * Author: Prosopo Team
 * Author URI: https://prosopo.io/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: prosopo-procaptcha
 * Domain Path: /lang
 */

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

// the scoper-autoloader includes autoloader for this namespace as well.
require_once __DIR__ . '/prefixed-vendors/vendor/scoper-autoload.php';
require_once __DIR__ . '/src/helpers.php';

( new Plugin( __FILE__ ) )->set_hooks( is_admin() );
