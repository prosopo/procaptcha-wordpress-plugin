<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Collection;
use Io\Prosopo\Procaptcha\Interfaces\Captcha\Captcha_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Tab_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Factory_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Interface;
use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Views\Settings\Settings_Form;
use function Io\Prosopo\Procaptcha\make_collection;

abstract class Settings_Tab implements Settings_Tab_Interface {
	const OPTION_BASE   = 'prosopo-procaptcha__settings';
	const OPTION_PREFIX = self::OPTION_BASE . '__';

	protected Collection $validated_fields;
	protected ?Collection $settings;

	public function __construct() {
		$this->settings         = null;
		$this->validated_fields = make_collection( array() );
	}

	abstract public function get_tab_title(): string;

	public function process_form( Query_Arguments $query_arguments ): void {
		$this->validate_settings( $query_arguments );
		$this->update_settings();
	}

	public function get_settings(): Collection {
		if ( null !== $this->settings ) {
			// Return a separate instance to avoid out-of-the-class modifications.
			return make_collection( $this->settings->to_array() );
		}

		$option_name = $this->get_option_name();

		// It's optional.
		$current = '' !== $option_name ?
			get_option( $this->get_option_name(), array() ) :
		array();

		$current = true === is_array( $current ) ?
			$current :
			array();

		$this->settings = make_collection( $current );

		// Setup defaults.
		$this->settings->merge( $this->get_default_values(), true );

		return $this->settings;
	}

	public function clear_data(): void {
		$option_name = $this->get_option_name();

		// It's optional.
		if ( '' === $option_name ) {
			return;
		}

		delete_option( $this->get_option_name() );
	}

	public function make_tab_component( View_Factory_Interface $creator, Captcha_Interface $captcha ): View_Interface {
		$string_settings = $this->get_string_settings();
		$bool_settings   = $this->get_bool_settings();
		$select_inputs   = $this->get_select_inputs();
		$password_inputs = $this->get_password_inputs();
		$inputs          = array();
		$checkboxes      = array();

		$this->load_field_values_from_settings();

		foreach ( $string_settings as $field_name => $field_label ) {
			$input = make_collection(
				array(
					'label' => $field_label,
					'name'  => $field_name,
					'type'  => 'text',
					'value' => $this->validated_fields->get_string( $field_name ),
				)
			);

			if ( true === key_exists( $field_name, $select_inputs ) ) {
				$input->add( 'options', $select_inputs[ $field_name ] );
				$input->add( 'type', 'select' );
			}

			if ( true === in_array( $field_name, $password_inputs, true ) ) {
				$input->add( 'type', 'password' );
			}

			$inputs[] = $input;
		}

		foreach ( $bool_settings as $field_name => $field_label ) {
			$checkboxes[] = make_collection(
				array(
					'label' => $field_label,
					'name'  => $field_name,
					'type'  => 'checkbox',
					'value' => $this->validated_fields->get_bool( $field_name ),
				)
			);
		}

		return $creator->make_view(
			Settings_Form::class,
			function ( Settings_Form $settings_form ) use ( $inputs, $checkboxes ) {
				$settings_form->nonce            = wp_create_nonce( Settings_Page::FORM_NONCE );
				$settings_form->tab_name         = $this->get_tab_name();
				$settings_form->inputs           = $inputs;
				$settings_form->checkboxes       = $checkboxes;
				$settings_form->inputs_title     = $this->get_inputs_title();
				$settings_form->checkboxes_title = $this->get_checkboxes_title();
			}
		);
	}

	public function get_tab_js_file(): string {
		return '';
	}

	public function get_tab_css_file(): string {
		return '';
	}

	public function get_tab_js_data( Settings_Storage_Interface $settings_storage ): array {
		return array();
	}

	abstract protected function get_option_name(): string;

	/**
	 * @return array<string,mixed>
	 */
	protected function get_default_values(): array {
		return array();
	}

	/**
	 * @return array<string,string>
	 */
	protected function get_bool_settings(): array {
		return array();
	}

	/**
	 * @return array<string,string>
	 */
	protected function get_string_settings(): array {
		return array();
	}

	/**
	 * @return array<string,array<string,string>> fieldName => [itemValue => itemLabel]
	 */
	protected function get_select_inputs(): array {
		return array();
	}

	/**
	 * @return string[]
	 */
	protected function get_password_inputs(): array {
		return array();
	}

	protected function get_inputs_title(): string {
		return '';
	}

	protected function get_checkboxes_title(): string {
		return '';
	}

	protected function validate_settings( Query_Arguments $query_arguments ): void {
		foreach ( array_keys( $this->get_bool_settings() ) as $bool_setting_name ) {
			$bool_setting_value = $query_arguments->get_bool_for_admin_action(
				$bool_setting_name,
				Settings_Page::FORM_NONCE,
				Query_Arguments::POST
			);

			$this->validated_fields->add( $bool_setting_name, $bool_setting_value );
		}

		$select_inputs = make_collection( $this->get_select_inputs() );

		foreach ( array_keys( $this->get_string_settings() ) as $string_setting_name ) {
			$string_setting_value = $query_arguments->get_string_for_admin_action(
				$string_setting_name,
				Settings_Page::FORM_NONCE,
				Query_Arguments::POST
			);

			if ( true === $select_inputs->exists( $string_setting_name ) ) {
				$options = $select_inputs->get_sub_collection( $string_setting_name );

				if ( false === $options->exists( $string_setting_value ) ) {
					continue;
				}
			}

			$this->validated_fields->add( $string_setting_name, $string_setting_value );
		}
	}

	protected function load_field_values_from_settings(): void {
		$settings = $this->get_settings();

		foreach ( array_keys( $this->get_bool_settings() ) as $bool_setting_name ) {
			$bool_setting_value = $settings->get_bool( $bool_setting_name );

			$this->validated_fields->add( $bool_setting_name, $bool_setting_value );
		}

		foreach ( array_keys( $this->get_string_settings() ) as $string_setting_name ) {
			$string_setting_value = $settings->get_string( $string_setting_name );

			$this->validated_fields->add( $string_setting_name, $string_setting_value );
		}
	}

	protected function update_settings(): void {
		$settings = $this->get_settings();

		// Merge instead of overwrite, as it may contain other settings as well.
		$settings->merge( $this->validated_fields );

		// Update our settings cache to reflect the merge changes.
		$this->settings = $settings;

		$option_name = $this->get_option_name();

		// It's optional.
		if ( '' === $option_name ) {
			return;
		}

		update_option( $option_name, $settings->to_array() );
	}
}
