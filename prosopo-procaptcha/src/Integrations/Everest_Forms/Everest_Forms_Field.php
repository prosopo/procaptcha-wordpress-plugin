<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Everest_Forms;

use EVF_Form_Fields;
use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Integration\Form\Form_Integration_Interface;
use function Io\Prosopo\Procaptcha\make_collection;

defined( 'ABSPATH' ) || exit;

class Everest_Forms_Field extends EVF_Form_Fields implements Form_Integration_Interface {
	use Form_Integration;

	public function __construct() {
		$this->name     = self::get_form_helper()->get_captcha()->get_field_label();
		$this->type     = self::get_form_helper()->get_captcha()->get_field_name();
		$this->icon     = 'evf-icon evf-icon-hcaptcha';
		$this->order    = '240';
		$this->group    = 'advanced';
		$this->settings = array(
			'basic-options' => array(
				'field_options' => array(
					// It's required by Everest Forms.
					'meta',
				),
			),
		);

		parent::__construct();
	}

	/**
	 * @param array<string, mixed> $field
	 *
	 * @return void
	 */
	// @phpstan-ignore-next-line
	public function field_preview( $field ) {
		$field['label'] = self::get_form_helper()->get_captcha()->get_field_label();

		$this->field_preview_option( 'label', $field );
	}

	/**
	 * @param array<string, mixed> $field
	 * @param array<string, mixed> $field_atts
	 * @param array<string, mixed> $form_data
	 *
	 * @return void
	 */
	// @phpstan-ignore-next-line
	public function field_display( $field, $field_atts, $form_data ) {
		$captcha = self::get_form_helper()->get_captcha();

		$field_id = make_collection( $field )
			->get_string( 'id' );
		$form_id  = make_collection( $form_data )
			->get_string( 'id' );

		$captcha->print_form_field(
			array(
				Widget_Arguments::HIDDEN_INPUT_ATTRIBUTES => array(
					'class' => 'input-text',
					'id'    => sprintf( 'evf-%s-field_%s', $form_id, $field_id ),
					'name'  => sprintf( 'everest_forms[form_fields][%s]', $field_id ),
				),
				Widget_Arguments::IS_DESIRED_ON_GUESTS    => true,
				Widget_Arguments::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);
	}

	/**
	 * @param string $field_id
	 * @param string|mixed[] $field_submit
	 * @param array $form_data
	 *
	 * @return void
	 */
	// @phpstan-ignore-next-line
	public function validate( $field_id, $field_submit, $form_data ) {
		$field_submit = true === is_string( $field_submit ) ?
			$field_submit :
			'';

		$captcha = self::get_form_helper()->get_captcha();

		if ( false === $captcha->is_present() ||
		true === $captcha->is_human_made_request( $field_submit ) ) {
			return;
		}

		$form_id = make_collection( $form_data )
			->get_string( 'id' );

		/**
		 * @var \EVF_Form_Task $task
		 */
		$task = evf()->task; // @phpstan-ignore-line

		if ( false === key_exists( $form_id, $task->errors ) ||
		false === is_array( $task->errors[ $form_id ] ) ) {
			$task->errors[ $form_id ] = array();
		}

		$task->errors[ $form_id ][ $field_id ] = $captcha->get_validation_error_message();
		update_option( 'evf_validation_error', 'yes' );
	}
}