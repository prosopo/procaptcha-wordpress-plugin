<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Settings;

defined( 'ABSPATH' ) || exit;

interface Procaptcha_Settings {
	public function get_site_key(): string;

	public function get_secret_key(): string;

	public function get_theme(): string;

	/**
	 * Empty when the widget should follow the visitor's browser.
	 */
	public function get_language(): string;

	public function should_bypass_authorized_user(): bool;
}
