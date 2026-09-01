<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Divi\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

/**
 * Divi Contact Form module.
 *
 * The built-in `Use Basic Captcha` toggle of the target module acts as the opt-in:
 * when it's enabled, Procaptcha replaces the Divi arithmetic captcha.
 *
 * Divi has no validation hook of its own, so the verification result is turned into
 * the module's native captcha error: the Divi captcha is kept enabled for the failed submission,
 * while its inputs are removed from the markup, so Divi rejects the submission on its own.
 */
final class Divi_Contact_Form extends Widget_Integration_Base {
	const CAPTCHA_PROP  = 'captcha';
	const MODULE_SLUG   = 'et_pb_contact_form';
	const PROP_DISABLED = 'off';
	const PROP_ENABLED  = 'on';

	private bool $is_protected_module_rendering = false;
	private ?bool $is_submission_verified       = null;

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter( 'pre_do_shortcode_tag', array( $this, 'verify_form_submission' ), 10, 2 );
		add_filter( 'et_pb_module_shortcode_attributes', array( $this, 'take_over_divi_captcha' ), 10, 3 );
		add_filter( self::MODULE_SLUG . '_shortcode_output', array( $this, 'integrate_widget_into_form' ), 10, 2 );
	}

	/**
	 * Divi processes the submission during the module rendering,
	 * so the verification has to happen before the shortcode callback is executed.
	 *
	 * @param mixed $short_circuit_value
	 *
	 * @return mixed
	 */
	public function verify_form_submission( $short_circuit_value, string $shortcode_name ) {
		if ( self::MODULE_SLUG !== $shortcode_name ||
			null !== $this->is_submission_verified ||
			! $this->is_form_submitted() ) {
			return $short_circuit_value;
		}

		$this->is_submission_verified = $this->widget->is_verification_token_valid();

		return $short_circuit_value;
	}

	/**
	 * @param mixed $module_props
	 * @param mixed $module_attributes
	 *
	 * @return mixed
	 */
	public function take_over_divi_captcha( $module_props, $module_attributes, string $module_slug ) {
		if ( self::MODULE_SLUG !== $module_slug ||
			! is_array( $module_props ) ) {
			return $module_props;
		}

		$is_protected_module = self::PROP_ENABLED === ( $module_props[ self::CAPTCHA_PROP ] ?? '' );

		$this->is_protected_module_rendering = $is_protected_module;

		if ( ! $is_protected_module ) {
			return $module_props;
		}

		// Keep the Divi captcha enabled for the rejected submission: its inputs are removed from the markup,
		// so Divi fails the submission with its own captcha error.
		$module_props[ self::CAPTCHA_PROP ] = false === $this->is_submission_verified ?
			self::PROP_ENABLED :
			self::PROP_DISABLED;

		return $module_props;
	}

	/**
	 * @param mixed $module_output
	 *
	 * @return mixed
	 */
	public function integrate_widget_into_form( $module_output, string $module_slug ) {
		if ( ! is_string( $module_output ) ||
			! $this->is_protected_module_rendering ||
			$this->is_visual_builder() ) {
			return $module_output;
		}

		$this->is_protected_module_rendering = false;

		$module_output = $this->remove_divi_captcha( $module_output );

		return $this->add_widget_field( $module_output );
	}

	protected function is_form_submitted(): bool {
		// Divi checks its own nonce.
		// @phpcs:ignore WordPress.Security.NonceVerification
		foreach ( array_keys( $_POST ) as $argument_name ) {
			if ( is_string( $argument_name ) &&
				1 === preg_match( '/^et_pb_contactform_submit_\d+$/', $argument_name ) ) {
				return true;
			}
		}

		return false;
	}

	protected function is_visual_builder(): bool {
		return function_exists( 'et_core_is_fb_enabled' ) &&
			(bool) et_core_is_fb_enabled();
	}

	protected function remove_divi_captcha( string $module_output ): string {
		$divi_captcha_column = '~<div class="et_pb_contact_right">[\s\S]*?</div>[\s\S]*?<!-- \.et_pb_contact_right -->~';

		return (string) preg_replace( $divi_captcha_column, '', $module_output );
	}

	protected function add_widget_field( string $module_output ): string {
		$widget_field = $this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 20px;width:100%',
				),
				Widget_Settings::IS_RETURN_ONLY     => true,
				// Divi re-renders the form with its own error, so the client-side check would be misleading.
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);

		$submit_wrapper = '<div class="et_contact_bottom_container">';

		return str_replace(
			$submit_wrapper,
			$widget_field . "\n" . $submit_wrapper,
			$module_output
		);
	}
}
