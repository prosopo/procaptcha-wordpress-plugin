<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Everest_Forms;

defined( 'ABSPATH' ) || exit;

use EVF_Form_Fields;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Widget_Container;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class Everest_Forms_Form_Integration extends EVF_Form_Fields implements Form_Integration {
	use Widget_Container;

	public function __construct() {
		$this->name     = self::get_widget()->get_field_label();
		$this->type     = self::get_widget()->get_field_name();
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
		$field['label'] = self::get_widget()->get_field_label();

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
		$widget = self::get_widget();

		$field_id = string( $field, 'id' );
		$form_id  = string( $form_data, 'id' );

		$widget->print_form_field(
			array(
				Widget_Settings::HIDDEN_INPUT_ATTRIBUTES => array(
					'class' => 'input-text',
					'id'    => sprintf( 'evf-%s-field_%s', $form_id, $field_id ),
					'name'  => sprintf( 'everest_forms[form_fields][%s]', $field_id ),
				),
				Widget_Settings::IS_DESIRED_ON_GUESTS    => true,
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
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
		$field_submit = is_string( $field_submit ) ?
			$field_submit :
			'';

		$widget = self::get_widget();

		if ( ! $widget->is_protection_enabled() ||
		$widget->is_verification_token_valid( $field_submit ) ) {
			return;
		}

		$form_id = string( $form_data, 'id' );

		/**
		 * @var \EVF_Form_Task $task
		 */
		$task = evf()->task; // @phpstan-ignore-line

		if ( ! key_exists( $form_id, $task->errors ) ||
		! is_array( $task->errors[ $form_id ] ) ) {
			$task->errors[ $form_id ] = array();
		}

		$task->errors[ $form_id ][ $field_id ] = $widget->get_validation_error_message();
		update_option( 'evf_validation_error', 'yes' );
	}
}
