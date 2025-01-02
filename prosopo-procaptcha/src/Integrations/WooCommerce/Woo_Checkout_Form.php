<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WooCommerce;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Plugin;
use WP_Error;

class Woo_Checkout_Form extends Hookable_Form_Integration_Base {
	public function print_classic_field(): void {
		$captcha = self::get_form_helpers()->get_captcha();

		if ( ! $captcha->present() ) {
			return;
		}

		$captcha->print_form_field(
			array(
				Widget_Arguments::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);
	}

	/**
	 * @param array<string,mixed> $data
	 */
	public function verify( array $data, WP_Error $errors ): void {
		$captcha = self::get_form_helpers()->get_captcha();

		if ( ! $captcha->present() ||
		$captcha->human_made_request() ) {
			return;
		}

		$captcha->add_validation_error( $errors );
	}

	/**
	 * @param mixed $value
	 * @param array<string,mixed> $field
	 *
	 * @return WP_Error|void
	 */
	public function verify_block_field( $value, array $field ) {
		$captcha = self::get_form_helpers()->get_captcha();

		$token = is_string( $value ) ?
			$value :
			'';

		// Without checking ->is_present() because this Woo Rest API doesn't pass the Auth cookie.
		if ( $captcha->human_made_request( $token ) ) {
			return;
		}

		return $captcha->add_validation_error();
	}

	/**
	 * @param array<string,mixed> $block
	 */
	public function print_blocks_checkout_field( string $content, array $block ): string {
		$suffix     = '';
		$block_name = $block['blockName'] ?? '';

		if ( 'woocommerce/checkout-additional-information-block' === $block_name ) {
			// always print, since it's handled via Rest API without the Auth cookie, so we can't get the user id.
			$suffix  = self::get_form_helpers()->get_captcha()->print_form_field(
				array(
					Widget_Arguments::ELEMENT_ATTRIBUTES => array(
						'style' => 'margin:-30px 0 30px',
					),
					Widget_Arguments::IS_RETURN_ONLY     => true,
					Widget_Arguments::IS_WITHOUT_CLIENT_VALIDATION => true,
				)
			);
			$suffix .= '<prosopo-procaptcha-woo-checkout-form></prosopo-procaptcha-woo-checkout-form>';

			$captcha = self::get_form_helpers()->get_captcha();

			$captcha->add_integration_js( 'woo-blocks-checkout' );
			$captcha->add_integration_css( '.wc-block-components-address-form__prosopo-procaptcha-prosopo_procaptcha { display: none; }' );
		}

		return $content . $suffix;
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'woocommerce_review_order_before_submit', array( $this, 'print_classic_field' ) );
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'verify' ), 10, 2 );

		// It's necessary to use priority, otherwise for some reason Woo prints this block multiple times.
		add_filter( 'render_block', array( $this, 'print_blocks_checkout_field' ), 999, 2 );

		if ( function_exists( 'woocommerce_register_additional_checkout_field' ) ) {
			woocommerce_register_additional_checkout_field(
				array(
					'id'                         => Plugin::SLUG . '/' . self::get_form_helpers()->get_captcha()->get_field_name(),
					'label'                      => self::get_form_helpers()->get_captcha()->get_field_name(),
					'location'                   => 'order',
					'required'                   => true,
					'show_in_order_confirmation' => false,
					'type'                       => 'text',
					'validate_callback'          => array( $this, 'verify_block_field' ),
				)
			);
		}
	}
}
