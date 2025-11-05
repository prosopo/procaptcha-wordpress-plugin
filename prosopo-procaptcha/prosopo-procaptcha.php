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

( function () {
	/**
	 * @var \Io\Prosopo\Procaptcha\Procaptcha_Plugin $plugin_instance
	 */
	$plugin_instance = require __DIR__ . '/load_plugin.php';

	$plugin_instance->set_hooks();
} )();
