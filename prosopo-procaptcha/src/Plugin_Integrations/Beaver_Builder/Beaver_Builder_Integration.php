<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Plugin_Integration\Procaptcha_Plugin_Integration;

defined( 'ABSPATH' ) || exit;

final class Beaver_Builder_Integration extends Procaptcha_Plugin_Integration implements Hookable {
	public function get_target_plugin_classes(): array {
		return array( 'FLBuilder' );
	}

	public function set_hooks( bool $is_admin_area ): void {
		// todo.
		add_filter(
			'fl_builder_register_module_settings_form',
			function ( $form, $slug ) {
				if ( 'contact-form' === $slug ) {
					$form['general']['sections']['general']['fields']['procaptcha'] = array(
						'default' => 'disabled',
						'label'   => 'Procaptcha protection',
						'options' => array(
							'disabled' => 'Disabled',
							'enabled'  => 'Enabled',
						),
						'type'    => 'select',
					);
				}

				return $form;
			},
			10,
			2
		);

		// todo find a way to print procaptcha when the setting above is enabled
		// the form is printed in: bb-plugin/modules/contact-form/includes/frontend.php
		/*
		add_action(
			'fl_builder_render_module_html_before',
			function ( $type, $settings, $module ) {
					echo '<pre>';
					print_r(
						array(
							'module'   => $module,
							'settings' => $settings,
							'type'     => $type,
						)
					);
					echo '</pre>';
			},
			10,
			3
		);*/
	}
}
