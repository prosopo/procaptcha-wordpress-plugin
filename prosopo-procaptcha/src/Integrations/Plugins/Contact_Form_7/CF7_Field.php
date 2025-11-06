<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Contact_Form_7;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

final class CF7_Field extends Widget_Integration_Base {
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
