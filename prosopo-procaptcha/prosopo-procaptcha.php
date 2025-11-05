<?php
/**
 * Plugin Name: Prosopo Procaptcha
 * Description: GDPR compliant, privacy friendly and better value captcha.
 * Version: 1.20.1
 * Author: Prosopo Team
 * Author URI: https://prosopo.io/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: prosopo-procaptcha
 * Domain Path: /lang
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var \Io\Prosopo\Procaptcha\Procaptcha_Plugin $prosopo_procaptcha
 */
$prosopo_procaptcha = require __DIR__ . '/load_plugin.php';

$prosopo_procaptcha->set_hooks();
