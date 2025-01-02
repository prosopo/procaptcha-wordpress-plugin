<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Ninja_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Definition\Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Integration\Form\Form_Integration_Helpers_Container;
use NF_Abstracts_Input;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\arr;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

// todo: find a way to make the field required by default.
class Ninja_Form_Field extends NF_Abstracts_Input implements Form_Integration {
	use Form_Integration_Helpers_Container;

	public function __construct() {
		parent::__construct();

		$field_name = self::get_form_helpers()->get_captcha()->get_field_name();

		$this->_name      = $field_name;
		$this->_nicename  = self::get_form_helpers()->get_captcha()->get_field_label();
		$this->_type      = $field_name;
		$this->_templates = array( $field_name );
		$this->_section   = 'misc';
		$this->_icon      = 'filter';

		add_filter( sprintf( 'ninja_forms_localize_field_%s', $field_name ), array( $this, 'render_field' ) );
	}

	/**
	 * @param array<string,mixed> $field
	 *
	 * @return array<int|string,mixed>
	 */
	public function render_field( array $field ): array {
		$captcha = self::get_form_helpers()->get_captcha();

		$element = $captcha->print_form_field(
			array(
				Widget_Arguments::ELEMENT_ATTRIBUTES   => array(
					// Without this class, the border around field won't appear when its validation is failed.
					'class' => 'ninja-forms-field',
					'style' => 'padding:0',
				),
				Widget_Arguments::IS_DESIRED_ON_GUESTS => true,
				Widget_Arguments::IS_RETURN_ONLY       => true,
				Widget_Arguments::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);

		$element .= '<prosopo-procaptcha-ninja-forms-integration></prosopo-procaptcha-ninja-forms-integration>';
		$captcha->add_integration_js( 'ninja-forms' );

		$field['settings'] = array_merge(
			arr( $field, 'settings' ),
			array(
				'label_pos'  => 'hidden', // Hide the label.
				'procaptcha' => $element,
			)
		);

		return $field;
	}

	/**
	 * Validate
	 *
	 * @param mixed $field
	 * @param mixed $data
	 * @return mixed[] $errors
	 */
	public function validate( $field, $data ) {
		$captcha = self::get_form_helpers()->get_captcha();

		if ( ! is_array( $field ) ||
		! is_array( $data ) ||
		! $captcha->present() ) {
			return array();
		}

		$token = string( $field, 'value' );

		if ( ! $captcha->human_made_request( $token ) ) {
			// For some reason it doesn't display error if array is returned...
			return $captcha->get_validation_error_message(); // @phpstan-ignore-line.
		}

		return array();
	}
}
