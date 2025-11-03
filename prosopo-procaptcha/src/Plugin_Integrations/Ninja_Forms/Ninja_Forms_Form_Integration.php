<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Ninja_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\External_Widget_Integration_Trait;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use NF_Abstracts_Input;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\arr;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

// todo: find a way to make the field required by default.
class Ninja_Forms_Form_Integration extends NF_Abstracts_Input implements Form_Integration {
	use External_Widget_Integration_Trait;

	public function __construct() {
		parent::__construct();

		$field_name = self::get_widget()->get_field_name();

		$this->_name      = $field_name;
		$this->_nicename  = self::get_widget()->get_field_label();
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
		$widget = self::get_widget();

		$element = $widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES   => array(
					// Without this class, the border around field won't appear when its validation is failed.
					'class' => 'ninja-forms-field',
					'style' => 'padding:0',
				),
				Widget_Settings::IS_DESIRED_ON_GUESTS => true,
				Widget_Settings::IS_RETURN_ONLY       => true,
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);

		$element .= '<prosopo-procaptcha-ninja-forms-integration></prosopo-procaptcha-ninja-forms-integration>';
		$widget->load_plugin_integration_script( 'ninja-forms/ninja-forms-integration.min.js' );

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
		$widget = self::get_widget();

		if ( ! is_array( $field ) ||
		! is_array( $data ) ||
		! $widget->is_protection_enabled() ) {
			return array();
		}

		$token = string( $field, 'value' );

		if ( ! $widget->is_verification_token_valid( $token ) ) {
			// For some reason it doesn't display error if array is returned...
			return $widget->get_validation_error_message(); // @phpstan-ignore-line.
		}

		return array();
	}
}
