<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Beaver_Builder_Modules;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Error;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\boolExtended;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\object;

defined( 'ABSPATH' ) || exit;

final class Beaver_Contact_Form_Integration extends Hookable_Form_Integration_Base {
	private Beaver_Builder_Modules $beaver_modules;

	public function construct(): void {
		$this->beaver_modules = new Beaver_Builder_Modules();
	}

	public function set_hooks( bool $is_admin_area ): void {
		$widget      = self::get_form_helper()->get_widget();
		$module_name = 'contact-form';
		$field_name  = $widget->get_field_name();

		$this->beaver_modules->add_module_setting(
			$module_name,
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

		$is_module_protection_enabled = fn( object $module_settings )=>boolExtended( $module_settings, $field_name );

		$this->beaver_modules->on_module_item_render(
			function ( object $module ) use ( $is_module_protection_enabled, $widget ) {
				$module_settings = object( $module, 'settings' );

				if ( $is_module_protection_enabled( $module_settings ) &&
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
			},
			$module_name,
			'button'
		);

		$this->beaver_modules->add_contact_form_validation(
			function ( object $form_settings ) use ( $is_module_protection_enabled, $widget ): ?WP_Error {
				$is_form_valid = true;

				if ( $is_module_protection_enabled( $form_settings ) &&
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
