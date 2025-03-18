<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WPForms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Helper\Form_Integration_Helper_Container;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class WPForms_Form_Integration extends \WPForms_Field implements Form_Integration {
	use Form_Integration_Helper_Container;

	/**
	 * @return void
	 */
	public function init() {
		$this->name     = self::get_form_helper()->get_widget()->get_field_label();
		$this->keywords = 'captcha, procaptcha';
		$this->type     = self::get_form_helper()->get_widget()->get_field_name();
		$this->icon     = 'fa-check-square-o';
		$this->order    = 180;
	}

	/**
	 * @param array<string, mixed> $field
	 *
	 * @return void
	 */
	// @phpstan-ignore-next-line
	public function field_options( $field ) {
		$this->field_option(
			'required',
			$field,
			array(
				'default' => true,
			)
		);
	}

	/**
	 * @param array<string, mixed> $field
	 *
	 * @return void
	 */
	// @phpstan-ignore-next-line
	public function field_preview( $field ) {
		$field['label'] = self::get_form_helper()->get_widget()->get_field_label();

		$this->field_preview_option( 'label', $field );
	}

	/**
	 * @param array<string, mixed> $field
	 * @param array<string, mixed> $field_atts
	 * @param array<string, mixed> $form_data
	 */
	// @phpstan-ignore-next-line
	public function field_display( $field, $field_atts, $form_data ) {
		$id   = string( $field, 'properties.inputs.primary.id' );
		$name = string( $field, 'properties.inputs.primary.attr.name' );

		self::get_form_helper()->get_widget()->print_form_field(
			array(
				Widget_Settings::HIDDEN_INPUT_ATTRIBUTES => array(
					'class' => 'wpforms-field-required',
					'id'    => $id,
					'name'  => $name,
				),
				Widget_Settings::IS_DESIRED_ON_GUESTS    => true,
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);
	}

	/**
	 * @param int $field_id
	 * @param mixed $field_submit
	 * @param array<string,mixed> $form_data
	 *
	 * @return void
	 */
	// @phpstan-ignore-next-line
	public function validate( $field_id, $field_submit, $form_data ) {
		$token  = is_string( $field_submit ) ?
			$field_submit :
			'';
		$widget = self::get_form_helper()->get_widget();

		if ( ! $widget->is_protection_enabled() ||
			$widget->is_verification_token_valid( $token ) ) {
			return;
		}

		if ( ! function_exists( 'wpforms' ) ) {
			return;
		}

		wpforms()->obj( 'process' )
			->errors[ $form_data['id'] ][ $field_id ] = $widget->get_validation_error_message();
	}
}
