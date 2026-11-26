<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Settings;

defined( 'ABSPATH' ) || exit;

interface Procaptcha_Settings {
	const VISITOR_IP_OFF             = 'off';
	const VISITOR_IP_CLOUDFLARE      = 'cloudflare';
	const VISITOR_IP_X_FORWARDED_FOR = 'x_forwarded_for';
	const VISITOR_IP_REMOTE_ADDR     = 'remote_addr';

	public function get_site_key(): string;

	public function get_secret_key(): string;

	public function get_theme(): string;

	/**
	 * Widget corners: 'rounded' or 'square'.
	 */
	public function get_shape(): string;

	/**
	 * CSS font-family list for the widget, or empty for the default font.
	 */
	public function get_font(): string;

	/**
	 * Widget palette colors that are set (palette key => hex color), the rest keep the theme default.
	 *
	 * @return array<string,string>
	 */
	public function get_palette(): array;

	/**
	 * Empty when the widget should follow the visitor's browser.
	 */
	public function get_language(): string;

	public function should_bypass_authorized_user(): bool;

	/**
	 * Where the visitor IP for matching comes from (one of the VISITOR_IP_* values), or empty when matching is off.
	 */
	public function get_visitor_ip_source(): string;
}
