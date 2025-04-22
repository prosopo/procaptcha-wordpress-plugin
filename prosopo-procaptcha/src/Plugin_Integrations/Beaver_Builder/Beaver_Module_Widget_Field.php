<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder;

use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\object;

defined( 'ABSPATH' ) || exit;

final class Beaver_Module_Widget_Field {
	/**
	 * @param callable(object $module_settings): bool $is_module_protection_enabled
	 */
	public static function integrate_widget( Widget $widget, string $module_name, callable $is_module_protection_enabled ): void {
		Beaver_Modules::on_module_item_render(
			function ( object $module ) use ( $is_module_protection_enabled, $widget ) {
				$module_settings = object( $module, 'settings' );

				if ( $is_module_protection_enabled( $module_settings ) &&
					$widget->is_protection_enabled() ) {
					$widget->print_form_field(
						array(
							Widget_Settings::ELEMENT_ATTRIBUTES => array(
								'style' => 'margin: 0 0 10px;',
							),
							Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
						)
					);

					printf( '<%s></%1$s>', 'prosopo-procaptcha-beaver-builder-integration' );

					$widget->load_plugin_integration_script(
						'beaver-builder/beaver-builder-integration.min.js'
					);
				}
			},
			$module_name,
			'button'
		);
	}
}
