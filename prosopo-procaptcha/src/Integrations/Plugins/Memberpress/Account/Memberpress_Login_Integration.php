<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress\Account;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

final class Memberpress_Login_Integration extends Widget_Integration {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			'mepr-login-form-before-submit',
			function () {
				$this->widget->print_form_field(
					array(
						Widget_Settings::ELEMENT_ATTRIBUTES => array(
							'style' => 'margin:0 0 10px',
						),
					)
				);
			}
		);

		// validation is handled in the WordPress LoginForm integration.
	}
}
