<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Elementor_Pro;

use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Fields\Field_Base;
use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Integration\Form\Form_Integration_Interface;
use function Io\Prosopo\Procaptcha\make_collection;

defined( 'ABSPATH' ) || exit;

class Elementor_Form_Field extends Field_Base implements Form_Integration_Interface {
	use Form_Integration;

	public function __construct() {
		parent::__construct();

		add_filter( 'elementor/frontend/the_content', array( $this, 'maybe_replace_field_stub' ) );
	}

	public function get_type(): string {
		return self::get_form_helper()->get_captcha()->get_field_name();
	}

	public function get_name(): string {
		return self::get_form_helper()->get_captcha()->get_field_label();
	}

	public function maybe_replace_field_stub( string $content ): string {
		$stub = $this->get_field_stub();

		if ( false === strpos( $content, $stub ) ) {
			return $content;
		}

		$captcha = self::get_form_helper()->get_captcha();

		// Remove the stub if the captcha is not present.
		if ( false === $captcha->is_present() ) {
			return str_replace( $stub, '', $content );
		}

		// Replace all occurrences of the stub with the real captcha field.
		return str_replace(
			$stub,
			$captcha->print_form_field(
				array(
					Widget_Arguments::ELEMENT_ATTRIBUTES => array(
						// Otherwise, the field's wrapper which has flex:center squashes the field.
						'style' => 'width:100%',
					),
					Widget_Arguments::IS_RETURN_ONLY     => true,
					Widget_Arguments::IS_WITHOUT_CLIENT_VALIDATION => true,
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
		$item_data = true === is_array( $item ) ?
			$item :
			array();
		$item_info = make_collection( $item_data );

		// Without an element with the target id, the built-in Elementor validation will not add the error message after failed validation,
		// while we can't get this field id during the real widget rendering.
		$hidden_input = sprintf( '<input type="hidden" id="form-field-field_%s" name="%1$s" value="1">', $item_info->get_string( '_id' ) );

		// @phpcs:ignore WordPress.Security.EscapeOutput
		echo $this->get_field_stub() . $hidden_input;
	}

	/**
	 * @param mixed $field
	 */
	public function validation( $field, Classes\Form_Record $record, Classes\Ajax_Handler $ajax_handler ): void {
		parent::validation( $field, $record, $ajax_handler );

		$captcha = self::get_form_helper()->get_captcha();

		if ( false === $captcha->is_present() ||
		true === $captcha->is_human_made_request() ) {
			return;
		}

		$field_data = true === is_array( $field ) ?
			$field :
			array();
		$field_info = make_collection( $field_data );

		$ajax_handler->add_error(
			$field_info->get_string( 'id' ),
			$captcha->get_validation_error_message()
		);
	}

	protected function get_field_stub(): string {
		return '{' . self::get_form_helper()->get_captcha()->get_field_name() . '}';
	}
}