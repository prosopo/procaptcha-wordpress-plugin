<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Widget;

defined( 'ABSPATH' ) || exit;

use WP_Error;

interface Widget {
	/**
	 * @param array<string,mixed> $settings
	 */
	public function print_form_field( array $settings = array() ): string;

	public function load_plugin_integration_script( string $integration_name ): void;

	public function add_integration_css( string $css_code ): void;

	public function is_verification_token_valid( ?string $token = null ): bool;

	public function get_validation_error( WP_Error $base_error = null ): WP_Error;

	public function get_validation_error_message(): string;

	// By default, skips captcha for authorized users, also can be customized by the filter 'prosopo/procaptcha/is_captcha_present'.
	public function is_protection_enabled(): bool;

	// true if both keys are set.
	public function is_available(): bool;

	public function get_field_name(): string;

	public function get_field_label(): string;
}
