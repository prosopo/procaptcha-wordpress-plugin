<?php
/**
 * Plugin Name: Prosopo Procaptcha
 * Description: GDPR compliant, privacy friendly and better value captcha.
 * Version: 1.10.0
 * Author: Prosopo Team
 * Author URI: https://prosopo.io/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: prosopo-procaptcha
 * Domain Path: /lang
 */

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin\Plugin;

require_once __DIR__ . '/autoloader.php';

( new Plugin( __FILE__ ) )->set_hooks( is_admin() );
