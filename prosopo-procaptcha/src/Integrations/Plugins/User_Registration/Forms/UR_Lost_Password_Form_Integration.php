<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class UR_Lost_Password_Form_Integration extends Widget_Integration {
	// Only print, without validation, as the UR plugin uses the 'lostpassword_post' hook, so WordPress/Lost_Password_Form.php will handle it.

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			'user_registration_lostpassword_form',
			function () {
				$this->widget->print_form_field();
			}
		);
	}
}
