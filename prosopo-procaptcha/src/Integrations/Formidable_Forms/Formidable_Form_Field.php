<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Formidable_Forms;

defined( 'ABSPATH' ) || exit;

use FrmFieldType;
use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Integration\Form\Form_Integration_Interface;
use function Io\Prosopo\Procaptcha\make_collection;

class Formidable_Form_Field extends FrmFieldType implements Form_Integration_Interface {
	use Form_Integration;

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
		$this->type = self::get_form_helper()->get_captcha()->get_field_name();

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
		$captcha = self::get_form_helper()->get_captcha();

		$arguments = make_collection( $args );

		$field_id  = $arguments->get_string( 'field_id' );
		$field_key = $this->get_field_key( $field_id );

		$form_errors = $arguments->get_array( 'errors' );
		$is_error    = true === key_exists( $field_key, $form_errors );

		return $captcha->print_form_field(
			array(
				Widget_Arguments::HIDDEN_INPUT_ATTRIBUTES => array(
					'name' => sprintf( 'item_meta[%s]', $field_id ),
				),
				Widget_Arguments::IS_DESIRED_ON_GUESTS    => true,
				Widget_Arguments::IS_ERROR_ACTIVE         => $is_error,
				Widget_Arguments::IS_RETURN_ONLY          => true,
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
		$errors    = array();
		$captcha   = self::get_form_helper()->get_captcha();
		$arguments = make_collection( $args );

		$token = $arguments->get_string( 'value' );

		if ( false === $captcha->is_present() ||
		true === $captcha->is_human_made_request( $token ) ) {
			return $errors;
		}

		$field_id  = $arguments->get_string( 'id' );
		$field_key = $this->get_field_key( $field_id );

		$error_message = $captcha->get_validation_error_message();

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