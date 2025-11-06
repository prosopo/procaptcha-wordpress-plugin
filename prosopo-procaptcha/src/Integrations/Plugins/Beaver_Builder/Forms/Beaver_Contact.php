<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Beaver_Module;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Beaver_Modules;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\boolExtended;

final class Beaver_Contact extends Widget_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		$this->extend_contact_form();
	}

	public function extend_contact_form(): void {
		$module_name = 'contact-form';
		$field_name  = $this->widget->get_field_name();

		Beaver_Modules::add_module_setting(
			$module_name,
			array( 'general', 'sections', 'general', 'fields', $field_name ),
			// callable, as translations are available only after 'init' hook.
			fn()=>array(
				'default' => 'disabled',
				'label'   => __( 'Procaptcha protection', 'prosopo-procaptcha' ),
				'options' => array(
					'off' => __( 'Disabled', 'prosopo-procaptcha' ),
					'on'  => __( 'Enabled', 'prosopo-procaptcha' ),
				),
				'type'    => 'select',
			)
		);

		$is_module_protection_enabled = fn ( object $module ) =>boolExtended( $module, array( 'settings', $field_name ) );

		Beaver_Module::integrate_widget(
			$this->widget,
			$module_name,
			$is_module_protection_enabled
		);

		Beaver_Modules::extend_module_submit_validation(
			'fl_builder_email',
			function ( object $module ) use ( $is_module_protection_enabled ): void {
				$is_form_valid = true;

				if ( $is_module_protection_enabled( $module ) &&
					$this->widget->is_protection_enabled() ) {
					$is_form_valid = $this->widget->is_verification_token_valid();
				}

				if ( $is_form_valid ) {
					return;
				}

				wp_send_json(
					array(
						'error'   => true,
						'message' => $this->widget->get_validation_error_message(),
					)
				);
			}
		);
	}
}
