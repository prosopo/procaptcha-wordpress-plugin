<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Plugin_Integration\Procaptcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

// Note: CF7 v5.9.8 calls the RestAPI without the nonce, so we can't omit captcha for authorized users.
class Contact_Form_7_Integration extends Procaptcha_Plugin_Integration implements Hookable {
	public function get_target_plugin_classes(): array {
		return array(
			'WPCF7',
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'wpcf7_init', array( $this, 'add_field' ) );

		add_filter(
			sprintf( 'wpcf7_validate_%s', $this->get_widget()->get_field_name() ),
			array( $this, 'validate' ),
			10,
			2
		);
	}

	public function add_field(): void {
		if ( ! function_exists( 'wpcf7_add_form_tag' ) ) {
			return;
		}

		wpcf7_add_form_tag(
			$this->get_widget()->get_field_name(),
			array( $this, 'print_field' ),
			array(
				'display-block' => true,
			)
		);
	}

	public function print_field(): string {
		ob_start();

		printf(
			'<div class="wpcf7-form-control-wrap" data-name="%s">',
			esc_attr( $this->get_widget()->get_field_name() ),
		);

		$this->get_widget()->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'class' => 'wpcf7-form-control',
				),
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);

		echo '</div>';

		return (string) ( ob_get_clean() );
	}

	/**
	 * @param object $result
	 * @param object $tag
	 *
	 * @return object
	 */
	public function validate( $result, $tag ) {
		$widget = $this->get_widget();

		if ( property_exists( $tag, 'name' ) &&
			'' === $tag->name ) {
			$tag->name = $widget->get_field_name();
		}

		if ( $widget->is_verification_token_valid() ) {
			return $result;
		}

		if ( method_exists( $result, 'invalidate' ) ) {
			$result->invalidate( $tag, $widget->get_validation_error_message() );
		}

		return $result;
	}
}
