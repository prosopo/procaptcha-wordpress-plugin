<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress\Account;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

final class Memberpress_Login_Integration extends Hookable_Form_Integration_Base {
	public function set_hooks( bool $is_admin_area ): void {
		add_action(
			'mepr-login-form-before-submit',
			function () {
				self::get_widget()->print_form_field(
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
