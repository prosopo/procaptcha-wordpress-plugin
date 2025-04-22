<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Beaver_Builder_Modules;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Error;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\boolExtended;

defined( 'ABSPATH' ) || exit;

final class Beaver_Contact_Form_Integration extends Hookable_Form_Integration_Base {
	private Beaver_Builder_Modules $beaver_modules;
	private bool $is_enabled_on_rendering_form;

	public function construct(): void {
		$this->beaver_modules               = new Beaver_Builder_Modules();
		$this->is_enabled_on_rendering_form = false;
	}

	public function set_hooks( bool $is_admin_area ): void {
		$widget     = self::get_form_helper()->get_widget();
		$field_name = $widget->get_field_name();

		$this->beaver_modules->add_module_setting(
			'contact-form',
			array( 'general', 'sections', 'general', 'fields', $field_name ),
			array(
				'default' => 'disabled',
				'label'   => __( 'Procaptcha protection', 'prosopo-procaptcha' ),
				'options' => array(
					'off' => __( 'Disabled', 'prosopo-procaptcha' ),
					'on'  => __( 'Enabled', 'prosopo-procaptcha' ),
				),
				'type'    => 'select',
			)
		);

		$is_enabled_on_form = fn( object $form )=>boolExtended( $form, $field_name );

		$this->beaver_modules->on_form_render(
			function ( object $form ) use ( $is_enabled_on_form ) {
				$this->is_enabled_on_rendering_form = $is_enabled_on_form( $form );
			}
		);

		$this->beaver_modules->on_module_render(
			'button',
			function () use ( $widget ) {
				if ( $this->is_enabled_on_rendering_form &&
				$widget->is_protection_enabled() ) {
					$this->print_widget_field(
						array(
							Widget_Settings::ELEMENT_ATTRIBUTES           => array(
								'style' => 'margin: 0 0 10px;',
							),
							Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
						),
						'prosopo-procaptcha-beaver-builder-integration',
						'beaver-builder/beaver-builder-integration.min.js'
					);
				}
			}
		);

		$this->beaver_modules->add_form_validation(
			function ( object $form ) use ( $is_enabled_on_form, $widget ): ?WP_Error {
				$is_form_valid = true;

				if ( $is_enabled_on_form( $form ) &&
					$widget->is_protection_enabled() ) {
					$is_form_valid = $widget->is_verification_token_valid();
				}

				return $is_form_valid ?
					null :
					$widget->get_validation_error();
			}
		);
	}

	/**
	 * @param array<string,mixed> $widget_settings
	 */
	protected function print_widget_field( array $widget_settings, string $field_tag, string $integration_script ): void {
		$widget = self::get_form_helper()->get_widget();

		$widget->print_form_field( $widget_settings );

		printf( '<%s></%1$s>', esc_html( $field_tag ) );

		$widget->load_plugin_integration_script(
			$integration_script
		);
	}
}
