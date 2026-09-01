<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Divi\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

/**
 * The Divi Login module submits to the native WordPress login flow,
 * therefore the validation is handled by the WordPress Login integration.
 */
final class Divi_Login extends Widget_Integration_Base {
	const MODULE_SLUG = 'et_pb_login';

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter( self::MODULE_SLUG . '_shortcode_output', array( $this, 'integrate_widget_into_form' ), 10, 2 );
	}

	/**
	 * @param mixed $module_output
	 *
	 * @return mixed
	 */
	public function integrate_widget_into_form( $module_output, string $module_slug ) {
		if ( ! is_string( $module_output ) ||
			$this->is_visual_builder() ) {
			return $module_output;
		}

		$widget_field = $this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px;width:100%',
				),
				Widget_Settings::IS_RETURN_ONLY     => true,
			)
		);

		$submit_button = '/(<p[^>]*>\s*<button)/';

		return (string) preg_replace(
			$submit_button,
			$widget_field . "\n$1",
			$module_output,
			1
		);
	}

	protected function is_visual_builder(): bool {
		return function_exists( 'et_core_is_fb_enabled' ) &&
			(bool) et_core_is_fb_enabled();
	}
}
