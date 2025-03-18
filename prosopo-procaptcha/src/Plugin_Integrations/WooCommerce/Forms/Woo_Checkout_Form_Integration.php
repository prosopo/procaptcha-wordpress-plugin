<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Procaptcha_Plugin;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Error;

class Woo_Checkout_Form_Integration extends Hookable_Form_Integration_Base {
	public function print_classic_field(): void {
		$widget = self::get_form_helper()->get_widget();

		if ( ! $widget->is_protection_enabled() ) {
			return;
		}

		$widget->print_form_field(
			array(
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);
	}

	/**
	 * @param array<string,mixed> $data
	 */
	public function verify( array $data, WP_Error $errors ): void {
		$widget = self::get_form_helper()->get_widget();

		if ( ! $widget->is_protection_enabled() ||
		$widget->is_verification_token_valid() ) {
			return;
		}

		$widget->get_validation_error( $errors );
	}

	/**
	 * @param mixed $value
	 * @param array<string,mixed> $field
	 *
	 * @return WP_Error|void
	 */
	public function verify_block_field( $value, array $field ) {
		$widget = self::get_form_helper()->get_widget();

		$token = is_string( $value ) ?
			$value :
			'';

		// Without checking ->is_present() because this Woo Rest API doesn't pass the Auth cookie.
		if ( $widget->is_verification_token_valid( $token ) ) {
			return;
		}

		return $widget->get_validation_error();
	}

	/**
	 * @param array<string,mixed> $block
	 */
	public function print_blocks_checkout_field( string $content, array $block ): string {
		$suffix     = '';
		$block_name = $block['blockName'] ?? '';

		if ( 'woocommerce/checkout-additional-information-block' === $block_name ) {
			// always print, since it's handled via Rest API without the Auth cookie, so we can't get the user id.
			$suffix  = self::get_form_helper()->get_widget()->print_form_field(
				array(
					Widget_Settings::ELEMENT_ATTRIBUTES => array(
						'style' => 'margin:-30px 0 30px',
					),
					Widget_Settings::IS_RETURN_ONLY     => true,
					Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
				)
			);
			$suffix .= '<prosopo-procaptcha-woo-checkout-form></prosopo-procaptcha-woo-checkout-form>';

			$widget = self::get_form_helper()->get_widget();

			$widget->load_plugin_integration_script( 'woocommerce/woocommerce-blocks-checkout-integration.min.js' );
			$widget->add_integration_css( '.wc-block-components-address-form__prosopo-procaptcha-prosopo_procaptcha { display: none; }' );
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
					'id'                         => Procaptcha_Plugin::SLUG . '/' . self::get_form_helper()->get_widget()->get_field_name(),
					'label'                      => self::get_form_helper()->get_widget()->get_field_name(),
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
