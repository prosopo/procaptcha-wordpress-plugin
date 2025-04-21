<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\boolExtended;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\setItem;

defined( 'ABSPATH' ) || exit;

final class Beaver_Contact_Form_Integration extends Hookable_Form_Integration_Base {
	private bool $is_enabled_in_rendering_form = false;

	public function set_hooks( bool $is_admin_area ): void {
		add_filter(
			'fl_builder_register_module_settings_form',
			fn ( array $form, string $slug ) =>'contact-form' === $slug ? $this->add_procaptcha_field( $form ) : $form,
			10,
			2
		);

		add_filter(
			'fl_builder_contact_form_fields',
			function ( array $fields, object $form_settings ) {

				$widget     = self::get_form_helper()->get_widget();
				$field_name = $widget->get_field_name();

				$this->is_enabled_in_rendering_form = boolExtended( $form_settings, $field_name );

				return $fields;
			},
			10,
			2
		);

		add_action(
			'fl_builder_render_module_html_before',
			function ( string $type ) {
				$widget = self::get_form_helper()->get_widget();

				if ( 'button' === $type &&
				$this->is_enabled_in_rendering_form &&
				$widget->is_protection_enabled() ) {
					$widget->print_form_field(
						array(
							Widget_Settings::ELEMENT_ATTRIBUTES => array(
								'style' => 'margin: 0 0 10px;',
							),
							Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
						)
					);
					echo '<prosopo-procaptcha-ninja-forms-integration></prosopo-procaptcha-ninja-forms-integration>';
					$widget->load_plugin_integration_script( 'ninja-forms/ninja-forms-integration.min.js' );
				}
			},
			10,
			3
		);

		add_action(
			'fl_module_contact_form_before_send',
			function ( string $mailto, string $subject, string $template, array $headers, object $settings ) {
				$widget                     = self::get_form_helper()->get_widget();
				$field_name                 = $widget->get_field_name();
				$is_form_protection_enabled = boolExtended( $settings, $field_name );

				if ( $is_form_protection_enabled &&
					$widget->is_protection_enabled() ) {
					if ( $widget->is_verification_token_valid() ) {
						return;
					}

					wp_send_json(
						array(
							'error'   => true,
							'message' => $widget->get_validation_error_message(),
						)
					);
				}
			},
			10,
			5
		);
	}

	/**
	 * @param array<string,mixed> $form
	 *
	 * @return array<string,mixed>
	 */
	protected function add_procaptcha_field( array $form ): array {
		$widget     = self::get_form_helper()->get_widget();
		$field_name = $widget->get_field_name();

		setItem(
			$form,
			array( 'general', 'sections', 'general', 'fields', $field_name ),
			$this->get_field_settings()
		);

		return $form;
	}

	/**
	 * @return array<string,mixed>
	 */
	protected function get_field_settings(): array {
		return array(
			'default' => 'disabled',
			'label'   => __( 'Procaptcha protection', 'prosopo-procaptcha' ),
			'options' => array(
				'off' => __( 'Disabled', 'prosopo-procaptcha' ),
				'on'  => __( 'Enabled', 'prosopo-procaptcha' ),
			),
			'type'    => 'select',
		);
	}
}
