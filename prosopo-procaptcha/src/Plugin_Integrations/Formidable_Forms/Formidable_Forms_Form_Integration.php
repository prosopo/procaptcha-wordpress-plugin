<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Formidable_Forms;

defined( 'ABSPATH' ) || exit;

use FrmFieldType;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Widget_Container;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\arr;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class Formidable_Forms_Form_Integration extends FrmFieldType implements Form_Integration {
	use Widget_Container;

	/**
	 * @var bool
	 */
	protected $has_input = false;
	/**
	 * @var bool
	 */
	protected $has_html = false;

	/**
	 * @param array<string,mixed>|int|object $field
	 * @param string           $type
	 */
	public function __construct( $field = 0, $type = '' ) {
		$this->type = self::get_widget()->get_field_name();

		parent::__construct( $field, $type );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function get_new_field_defaults(): array {
		return array_merge(
			parent::get_new_field_defaults(),
			array(
				'required' => true,
			)
		);
	}

	/**
	 * @param mixed $field
	 *
	 * @return array<string, bool>
	 */
	public function displayed_field_type( $field ) {
		return array(
			$this->type => true,
		);
	}

	/**
	 * @param array<string,mixed> $args
	 * @param array<string,mixed> $shortcode_atts
	 *
	 * @return string
	 */
	// @phpstan-ignore-next-line
	public function front_field_input( $args, $shortcode_atts ) {
		$widget = self::get_widget();

		$field_id = string( $args, 'field_id' );

		$field_key = $this->get_field_key( $field_id );

		$form_errors = arr( $args, 'errors' );
		$is_error    = key_exists( $field_key, $form_errors );

		return $widget->print_form_field(
			array(
				Widget_Settings::HIDDEN_INPUT_ATTRIBUTES => array(
					'name' => sprintf( 'item_meta[%s]', $field_id ),
				),
				Widget_Settings::IS_DESIRED_ON_GUESTS    => true,
				Widget_Settings::IS_ERROR_ACTIVE         => $is_error,
				Widget_Settings::IS_RETURN_ONLY          => true,
			)
		);
	}

	/**
	 * @param array<string,mixed> $args
	 *
	 * @return array<string,string>
	 */
	// @phpstan-ignore-next-line
	public function validate( $args ) {
		$errors = array();
		$widget = self::get_widget();

		$token = string( $args, 'value' );

		if ( ! $widget->is_protection_enabled() ||
		$widget->is_verification_token_valid( $token ) ) {
			return $errors;
		}

		$field_id  = string( $args, 'id' );
		$field_key = $this->get_field_key( $field_id );

		$error_message = $widget->get_validation_error_message();

		return array_merge(
			$errors,
			array(
				$field_key => $error_message,
			)
		);
	}

	protected function get_field_key( string $field_id ): string {
		return 'field' . $field_id;
	}

	/**
	 * @return array<string,mixed>
	 */
	protected function field_settings_for_type(): array {
		return array_merge(
			parent::field_settings_for_type(),
			array(
				'required' => true,
			)
		);
	}
}
