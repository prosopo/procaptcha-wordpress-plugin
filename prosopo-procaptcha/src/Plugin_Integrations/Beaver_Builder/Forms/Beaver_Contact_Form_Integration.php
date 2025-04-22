<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Beaver_Module_Widget_Field;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Beaver_Modules;
use WP_Error;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\boolExtended;

defined( 'ABSPATH' ) || exit;

final class Beaver_Contact_Form_Integration extends Hookable_Form_Integration_Base {
	public function set_hooks( bool $is_admin_area ): void {
		$widget = self::get_form_helper()->get_widget();

		$module_name = 'contact-form';
		$field_name  = $widget->get_field_name();

		Beaver_Modules::add_module_setting(
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

		$is_module_protection_enabled = fn( object $module_settings ) => boolExtended( $module_settings, $field_name );

		Beaver_Module_Widget_Field::integrate_widget(
			$widget,
			$module_name,
			$is_module_protection_enabled
		);

		Beaver_Modules::add_contact_form_validation(
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
}
