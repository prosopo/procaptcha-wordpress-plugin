<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\User_Registration;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Hooks_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage_Interface;
use Io\Prosopo\Procaptcha\Settings\Tabs\Account_Forms_Settings;
use UR_Form_Field_Prosopo_Procaptcha;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

class User_Registration extends Plugin_Integration implements Hooks_Interface {
	public function get_target_plugin_classes(): array {
		return array(
			'UserRegistration',
		);
	}

	public function include_form_integrations(): void {
		// Class in a global namespace, as User Registration plugin will be calling this class by its name.
		require_once __DIR__ . '/UR_Form_Field_Prosopo_Procaptcha.php';
	}

	public function get_form_integrations( Settings_Storage_Interface $settings_storage ): array {
		return array_merge(
			array( UR_Form_Field_Prosopo_Procaptcha::class ),
			$this->get_active_conditional_integrations( $settings_storage ),
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'user_registration_registered_form_fields', array( $this, 'register_field_type' ) );
		add_filter(
			sprintf( '%s_admin_template', UR_Form_Field_Prosopo_Procaptcha::NAME_PREFIX . $this->get_captcha()->get_field_name() ),
			array( $this, 'register_admin_field_view' )
		);
		add_filter( 'user_registration_field_keys', array( $this, 'get_field_type' ), 10, 2 );
	}

	/**
	 * @param string[] $field_types
	 *
	 * @return string[]
	 */
	public function register_field_type( array $field_types ): array {
		return array_merge(
			$field_types,
			array(
				$this->get_captcha()->get_field_name(),
			)
		);
	}

	public function get_field_type( string $field_type, string $field_key ): string {
		$captcha = $this->get_captcha();

		if ( $captcha->get_field_name() !== $field_key ) {
			return $field_type;
		}

		return $captcha->get_field_name();
	}

	public function register_admin_field_view( string $path ): string {
		return __DIR__ . '/admin_template.php';
	}

	protected function get_conditional_integrations( Settings_Storage_Interface $settings_storage ): array {
		$account_forms = $settings_storage->get( Account_Forms_Settings::class )->get_settings();

		return array(
			UR_Login_Form::class         => bool( $account_forms, Account_Forms_Settings::IS_ON_WP_LOGIN_FORM ),
			UR_Lost_Password_Form::class => bool( $account_forms, Account_Forms_Settings::IS_ON_WP_LOST_PASSWORD_FORM ),
		);
	}
}
