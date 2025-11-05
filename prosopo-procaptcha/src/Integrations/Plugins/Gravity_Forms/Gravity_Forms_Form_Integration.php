<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Gravity_Forms;

defined( 'ABSPATH' ) || exit;

use GF_Field;
use Io\Prosopo\Procaptcha\Integration\Widget\External_Widget_Integration;
use Io\Prosopo\Procaptcha\Integration\Widget\External_Widget_Integration_Trait;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\int;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

final class Gravity_Forms_Form_Integration extends GF_Field implements External_Widget_Integration {
	use External_Widget_Integration_Trait;

	public string $type;
    public bool $isRequired; // @phpcs:ignore

	/**
	 * @param array<string,mixed> $data
	 */
	public function __construct( $data = array() ) {
		parent::__construct( $data );

		$this->type       = self::get_widget()->get_field_name();
        $this->isRequired = true; // @phpcs:ignore
	}

	/**
	 * Returns the field title.
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return self::get_widget()->get_field_label();
	}

	/**
	 * Returns the class names of the settings which should be available on the field in the form editor.
	 *
	 * @return string[]
	 */
	public function get_form_editor_field_settings() {
		return array(
			// Some value is required, otherwise the editor produces the JS error.
			'duplicate_setting',
			'rules_setting',
		);
	}

	/**
	 * Retrieve the field label.
	 *
	 * @param bool $force_frontend_label Should the frontend label be displayed in the admin even if an admin label is configured.
	 * @param string $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 *
	 * @return string
	 * @since 2.5     Move conditions about the singleproduct and calculation fields to their own class.
	 *
	 * @since unknown
	 */
	public function get_field_label( $force_frontend_label, $value ) {
		return self::get_widget()->get_field_label();
	}

	/**
	 * Returns the field's form editor icon.
	 *
	 * This could be an icon url or a gform-icon class.
	 *
	 * @return string
	 * @since 2.5
	 */
	public function get_form_editor_field_icon() {
		return 'gform-icon--recaptcha';
	}

	/**
	 * Returns the field button properties for the form editor. The array contains two elements:
	 * 'group' => 'standard_fields' // or  'advanced_fields', 'post_fields', 'pricing_fields'
	 * 'text'  => 'Button text'
	 *
	 * Built-in fields don't need to implement this because the buttons are added in sequence in GFFormDetail
	 *
	 * @return array<string, string>
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'icon'  => $this->get_form_editor_field_icon(),
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	/**
	 * Returns the field markup; including field label, description, validation, and the form editor admin buttons.
	 *
	 * The {FIELD} placeholder will be replaced in GFFormDisplay::get_field_content with the markup returned by GF_Field::get_field_input().
	 *
	 * @param string|array<string,mixed> $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param bool $force_frontend_label Should the frontend label be displayed in the admin even if an admin label is configured.
	 * @param array<string,mixed> $form The Form Object currently being processed.
	 *
	 * @return string
	 */
	// @phpstan-ignore-next-line
	public function get_field_content( $value, $force_frontend_label, $form ) {
		if ( $this->is_form_editor() ) {
			return parent::get_field_content( $value, $force_frontend_label, $form );
		}

		$form_id               = int( $form, 'id' );
		$validation_message_id = 'validation_message_' . $form_id . '_' . string( $this->id );

		$validation_message = true === $this->failed_validation &&
		is_string( $this->validation_message ) &&
		'' !== $this->validation_message ?
			sprintf(
				"<div id='%s' class='gfield_description validation_message gfield_validation_message'>%s</div>",
				esc_attr( $validation_message_id ),
				esc_html( $this->validation_message )
			) :
			'';

		$field_id = string( $this->id );

		return self::get_widget()->print_form_field(
			array(
				Widget_Settings::HIDDEN_INPUT_ATTRIBUTES => array(
					'name' => sprintf( 'input_%s', $field_id ),
				),
				Widget_Settings::IS_DESIRED_ON_GUESTS    => true,
				Widget_Settings::IS_RETURN_ONLY          => true,
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		) . $validation_message;
	}

	/**
	 * Override this method to perform custom validation logic.
	 *
	 * Return the result (bool) by setting $this->failed_validation.
	 * Return the validation message (string) by setting $this->validation_message.
	 *
	 * @param string|array<string,mixed> $value The field value from get_value_submission().
	 * @param array<string,mixed> $form The Form Object currently being processed.
	 *
	 * @return void
	 * @since 1.9
	 */
	// @phpstan-ignore-next-line
	public function validate( $value, $form ) {
		$widget = self::get_widget();

		$token = is_string( $value ) ?
			$value :
			'';

		if ( ! $widget->is_protection_enabled() ||
			$widget->is_verification_token_valid( $token ) ) {
			return;
		}

		$this->failed_validation  = true;
		$this->validation_message = $widget->get_validation_error_message();
	}
}
