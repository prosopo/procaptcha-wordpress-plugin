<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\User_Registration\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;

final class UR_Login_Form_Integration extends Widget_Integration {
	// Only print, without validation, as the UR plugin uses the auth hook, so WordPress/Login_Form.php will handle it.

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			'user_registration_login_form_before_submit_button',
			function () {
				$this->widget->print_form_field();
			}
		);
	}
}
