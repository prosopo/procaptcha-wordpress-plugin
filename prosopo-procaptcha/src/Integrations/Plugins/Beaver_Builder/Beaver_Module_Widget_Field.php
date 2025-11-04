<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder;

use Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Beaver_Modules;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

defined( 'ABSPATH' ) || exit;

final class Beaver_Module_Widget_Field {
	/**
	 * @param callable(object $module): bool $is_module_protection_enabled
	 */
	public static function integrate_widget( Widget $widget, string $module_name, callable $is_module_protection_enabled ): void {
		Beaver_Modules::on_module_item_render(
			function ( object $module ) use ( $is_module_protection_enabled, $widget ) {
				if ( $is_module_protection_enabled( $module ) &&
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
