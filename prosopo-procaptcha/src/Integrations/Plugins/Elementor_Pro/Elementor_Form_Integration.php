<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Elementor_Pro;

defined( 'ABSPATH' ) || exit;

use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Fields\Field_Base;
use Io\Prosopo\Procaptcha\Integration\Widget\External_Widget_Integration;
use Io\Prosopo\Procaptcha\Integration\Widget\External_Widget_Integration_Trait;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

final class Elementor_Form_Integration extends Field_Base implements External_Widget_Integration {
	use External_Widget_Integration_Trait;

	public function __construct() {
		parent::__construct();

		add_filter( 'elementor/frontend/the_content', array( $this, 'maybe_replace_field_stub' ) );
	}

	public function get_type(): string {
		return self::get_widget()->get_field_name();
	}

	public function get_name(): string {
		return self::get_widget()->get_field_label();
	}

	public function maybe_replace_field_stub( string $content ): string {
		$stub = $this->get_field_stub();

		if ( false === strpos( $content, $stub ) ) {
			return $content;
		}

		$widget = self::get_widget();

		// Remove the stub if the captcha is not present.
		if ( ! $widget->is_protection_enabled() ) {
			return str_replace( $stub, '', $content );
		}

		// Replace all occurrences of the stub with the real captcha field.
		return str_replace(
			$stub,
			$widget->print_form_field(
				array(
					Widget_Settings::ELEMENT_ATTRIBUTES => array(
						// Otherwise, the field's wrapper which has flex:center squashes the field.
						'style' => 'width:100%',
					),
					Widget_Settings::IS_RETURN_ONLY     => true,
					Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
				)
			),
			$content
		);
	}

	/**
	 * Using stub instead of the real HTML,
	 * as currently, with the elements cache feature been enabled (by default for new users),
	 * Elementor saves the whole form's HTML once, instead of generating it on the fly,
	 * and there is no way to control it on the field's level.
	 * https://github.com/elementor/elementor/issues/28372
	 *
	 * @param mixed $item
	 * @param mixed $item_index
	 * @param mixed $form
	 */
	public function render( $item, $item_index, $form ): void {
		$item_id = string( $item, '_id' );

		// Without an element with the target id, the built-in Elementor validation will not add the error message after failed validation,
		// while we can't get this field id during the real widget rendering.
		$hidden_input = sprintf( '<input type="hidden" id="form-field-field_%s" name="%1$s" value="1">', $item_id );

		// @phpcs:ignore WordPress.Security.EscapeOutput
		echo $this->get_field_stub() . $hidden_input;
	}

	/**
	 * @param mixed $field
	 */
	public function validation( $field, Classes\Form_Record $record, Classes\Ajax_Handler $ajax_handler ): void {
		parent::validation( $field, $record, $ajax_handler );

		$widget = self::get_widget();

		if ( ! $widget->is_protection_enabled() ||
		$widget->is_verification_token_valid() ) {
			return;
		}

		$field_id = string( $field, 'id' );

		$ajax_handler->add_error(
			$field_id,
			$widget->get_validation_error_message()
		);
	}

	protected function get_field_stub(): string {
		return '{' . self::get_widget()->get_field_name() . '}';
	}
}
