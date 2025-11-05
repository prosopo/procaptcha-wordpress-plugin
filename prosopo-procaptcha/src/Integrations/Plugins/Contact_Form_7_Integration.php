<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

// Note: CF7 v5.9.8 calls the RestAPI without the nonce, so we can't omit captcha for authorized users.
final class Contact_Form_7_Integration extends Plugin_Integration_Base implements Hookable {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Contact Form 7';
		$about->docs_url = self::get_docs_url( 'contact-form-7' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'WPCF7' );
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action( 'wpcf7_init', array( $this, 'add_field' ) );

		add_filter(
			sprintf( 'wpcf7_validate_%s', $this->widget->get_field_name() ),
			array( $this, 'validate' ),
			10,
			2
		);
	}

	public function add_field(): void {
		if ( function_exists( 'wpcf7_add_form_tag' ) ) {
			wpcf7_add_form_tag(
				$this->widget->get_field_name(),
				array( $this, 'print_field' ),
				array(
					'display-block' => true,
				)
			);
		}
	}

	public function print_field(): string {
		ob_start();

		printf(
			'<div class="wpcf7-form-control-wrap" data-name="%s">',
			esc_attr( $this->widget->get_field_name() ),
		);

		$this->widget->print_form_field(
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
		if ( property_exists( $tag, 'name' ) &&
			'' === $tag->name ) {
			$tag->name = $this->widget->get_field_name();
		}

		if ( $this->widget->is_verification_token_valid() ) {
			return $result;
		}

		if ( method_exists( $result, 'invalidate' ) ) {
			$result->invalidate( $tag, $this->widget->get_validation_error_message() );
		}

		return $result;
	}
}
