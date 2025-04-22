<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Beaver_Module_Widget_Field;

defined( 'ABSPATH' ) || exit;

final class Beaver_Login_Form_Integration extends Hookable_Form_Integration_Base {
	public function set_hooks( bool $is_admin_area ): void {
		$widget = self::get_form_helper()->get_widget();

		// no custom setting is needed, as it's handled by the native WP_Login_Form_Integration.

		Beaver_Module_Widget_Field::integrate_widget(
			$widget,
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
