<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Beaver_Modules;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Beaver_Widget_Integration;

defined( 'ABSPATH' ) || exit;

final class Beaver_Login_Form_Integration extends Hookable_Form_Integration_Base {
	private Beaver_Modules $beaver_modules;
	private Beaver_Widget_Integration $beaver_widget_integration;

	public function construct(): void {
		$this->beaver_modules            = new Beaver_Modules();
		$this->beaver_widget_integration = new Beaver_Widget_Integration( $this->beaver_modules );
	}

	public function set_hooks( bool $is_admin_area ): void {
		$widget = self::get_form_helper()->get_widget();

		// no custom setting is needed, as it's handled by the native WP_Login_Form_Integration.

		$this->beaver_widget_integration->integrate_widget(
			$widget,
			'login-form',
			fn( object $module_settings ) =>true, // fixme bind on the global setting.
		);

		// no custom validation is needed, as it's handled by the native WP_Login_Form_Integration.
	}
}
