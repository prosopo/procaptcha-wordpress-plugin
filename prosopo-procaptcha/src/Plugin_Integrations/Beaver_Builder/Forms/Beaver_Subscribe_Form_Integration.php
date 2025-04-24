<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Beaver_Module_Widget_Field;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Beaver_Modules;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\boolExtended;

defined( 'ABSPATH' ) || exit;

final class Beaver_Subscribe_Form_Integration extends Hookable_Form_Integration_Base {
	public function set_hooks( bool $is_admin_area ): void {
		$widget = self::get_widget();

		$module_name = 'subscribe-form';
		$field_name  = $widget->get_field_name();

		Beaver_Modules::add_module_setting(
			$module_name,
			array( 'general', 'sections', 'structure', 'fields', $field_name ),
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

		$is_module_protection_enabled = fn( object $module ) => boolExtended( $module, array( 'settings', $field_name ) );

		Beaver_Module_Widget_Field::integrate_widget(
			$widget,
			$module_name,
			$is_module_protection_enabled
		);

		// fixme extend_module_submit_validation for fl_builder_subscribe_form_submit
	}
}
