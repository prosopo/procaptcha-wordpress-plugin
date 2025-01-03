<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Elementor_Pro;

defined( 'ABSPATH' ) || exit;

use ElementorPro\Modules\Forms\Registrars\Form_Fields_Registrar;
use Io\Prosopo\Procaptcha\Interfaces\Hookable;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage;
use Io\Prosopo\Procaptcha\Integration\Plugin\Captcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Settings\Tabs\Account_Forms_Captcha_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

class Elementor_Pro_Integration extends Captcha_Plugin_Integration implements Hookable {
	public function get_target_plugin_classes(): array {
		return array(
			'ElementorPro\Plugin',
		);
	}

	public function requires_late_hooking(): bool {
		return true;
	}

	public function get_form_integrations( Settings_Storage $settings_storage ): array {
		return array_merge(
			array(
				Elementor_Form_Field::class,
			),
			$this->get_active_conditional_integrations( $settings_storage )
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'elementor_pro/forms/fields/register', array( $this, 'register_field' ) );
	}

	public function register_field( Form_Fields_Registrar $fields_manager ): void {
		$fields_manager->register( new Elementor_Form_Field() );
	}

	protected function get_conditional_integrations( Settings_Storage $settings_storage ): array {
		$account_forms = $settings_storage->get( Account_Forms_Captcha_Settings::class )->get_settings();

		return array(
			// Login Widget submits to wp-login.php, so validation happens there,
			// therefore that option should be active.
			Elementor_Login_Widget::class => bool( $account_forms, Account_Forms_Captcha_Settings::IS_ON_WP_LOGIN_FORM ),
		);
	}
}
