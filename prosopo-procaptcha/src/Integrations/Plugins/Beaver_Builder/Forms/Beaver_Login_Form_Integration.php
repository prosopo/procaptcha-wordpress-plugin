<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Forms;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Beaver_Module_Widget_Field;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;

defined( 'ABSPATH' ) || exit;

final class Beaver_Login_Form_Integration extends Widget_Integration {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		// no custom setting is needed, as it's handled by the native WP_Login_Form_Integration.

		Beaver_Module_Widget_Field::integrate_widget(
			$this->widget,
			'login-form',
			/**
			 * At this point, the setting is always true,
			 * since the Beaver Login form integration is conditionally loaded itself -
			 * see the 'Beaver_Builder_Integration' class.
			 */
			'__return_true'
		);

		// no custom validation is needed, as it's handled by the native WP_Login_Form_Integration.
	}
}
