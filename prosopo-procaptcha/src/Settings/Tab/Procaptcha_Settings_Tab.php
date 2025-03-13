<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Tab;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Settings\Settings_Page;
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;
use Io\Prosopo\Procaptcha\Templates\Settings\Settings_Form;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Widget\Widget;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

abstract class Procaptcha_Settings_Tab implements Settings_Tab {
	const OPTION_BASE   = 'prosopo-procaptcha__settings';
	const OPTION_PREFIX = self::OPTION_BASE . '__';

	/**
	 * @var array<string,mixed>
	 */
	protected array $validated_fields;
	/**
	 * @var array<string,mixed>|null
	 */
	protected ?array $settings;

	public function __construct() {
		$this->settings         = null;
		$this->validated_fields = array();
	}

	abstract public function get_tab_title(): string;

	public function process_form( Query_Arguments $query_arguments ): void {
		$this->validate_settings( $query_arguments );
		$this->update_settings();
	}

	public function get_settings(): array {
		if ( null !== $this->settings ) {
			// Return a separate instance to avoid out-of-the-class modifications.
			return $this->settings;
		}

		$option_name = $this->get_option_name();

		// It's optional.
		$current = '' !== $option_name ?
			get_option( $this->get_option_name(), array() ) :
		array();

		$current = is_array( $current ) ?
			$current :
			array();

		$this->settings = $current;

		// Setup defaults.
		$this->settings = array_merge( $this->get_default_values(), $this->settings );

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

	public function make_tab_component(
		ModelFactoryInterface $factory,
		Widget $widget
	): TemplateModelInterface {
		$string_settings = $this->get_string_settings();
		$bool_settings   = $this->get_bool_settings();
		$select_inputs   = $this->get_select_inputs();
		$password_inputs = $this->get_password_inputs();
		$inputs          = array();
		$checkboxes      = array();

		$this->load_field_values_from_settings();

		foreach ( $string_settings as $field_name => $field_label ) {
			$input = array(
				'label' => $field_label,
				'name'  => $field_name,
				'type'  => 'text',
				'value' => string( $this->validated_fields, $field_name ),
			);

			if ( key_exists( $field_name, $select_inputs ) ) {
				$input['options'] = $select_inputs[ $field_name ];
				$input['type']    = 'select';
			}

			if ( in_array( $field_name, $password_inputs, true ) ) {
				$input['type'] = 'password';
			}

			$inputs[] = $input;
		}

		foreach ( $bool_settings as $field_name => $field_label ) {
			$checkboxes[] = array(
				'label' => $field_label,
				'name'  => $field_name,
				'type'  => 'checkbox',
				'value' => bool( $this->validated_fields, $field_name ),
			);
		}

		return $factory->createModel(
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

	public function get_tab_script_asset(): string {
		return '';
	}

	public function get_style_asset(): string {
		return '';
	}

	public function get_tab_js_data( Settings_Storage $settings_storage ): array {
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

			$this->validated_fields[ $bool_setting_name ] = $bool_setting_value;
		}

		$select_inputs = $this->get_select_inputs();

		foreach ( array_keys( $this->get_string_settings() ) as $string_setting_name ) {
			$string_setting_value = $query_arguments->get_string_for_admin_action(
				$string_setting_name,
				Settings_Page::FORM_NONCE,
				Query_Arguments::POST
			);

			if ( key_exists( $string_setting_name, $select_inputs ) ) {
				$options = $select_inputs[ $string_setting_name ];

				if ( ! key_exists( $string_setting_value, $options ) ) {
					continue;
				}
			}

			$this->validated_fields[ $string_setting_name ] = $string_setting_value;
		}
	}

	protected function load_field_values_from_settings(): void {
		$settings = $this->get_settings();

		foreach ( array_keys( $this->get_bool_settings() ) as $bool_setting_name ) {
			$bool_setting_value = bool( $settings, $bool_setting_name );

			$this->validated_fields[ $bool_setting_name ] = $bool_setting_value;
		}

		foreach ( array_keys( $this->get_string_settings() ) as $string_setting_name ) {
			$string_setting_value = string( $settings, $string_setting_name );

			$this->validated_fields[ $string_setting_name ] = $string_setting_value;
		}
	}

	protected function update_settings(): void {
		$settings = $this->get_settings();

		// Merge instead of overwrite, as it may contain other settings as well.
		$settings = array_merge( $settings, $this->validated_fields );

		// Update our settings cache to reflect the merge changes.
		$this->settings = $settings;

		$option_name = $this->get_option_name();

		// It's optional.
		if ( '' === $option_name ) {
			return;
		}

		update_option( $option_name, $settings );
	}
}
