<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage;
use Io\Prosopo\Procaptcha\Integration\Plugin\Captcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Settings\Tabs\Account_Forms_Captcha_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

class WordPress_Integration extends Captcha_Plugin_Integration {
	public function get_form_integrations( Settings_Storage $settings_storage ): array {
		return array_merge(
			array(
				Shortcode::class,
			),
			$this->get_active_conditional_integrations( $settings_storage )
		);
	}

	public function get_target_plugin_classes(): array {
		return array();
	}

	protected function get_conditional_integrations( Settings_Storage $settings_storage ): array {
		$account_forms = $settings_storage->get( Account_Forms_Captcha_Settings::class )->get_settings();

		return array(
			Comment_Form::class            => bool( $account_forms, Account_Forms_Captcha_Settings::IS_ON_WP_COMMENT_FORM ),
			Login_Form::class              => bool( $account_forms, Account_Forms_Captcha_Settings::IS_ON_WP_LOGIN_FORM ),
			Lost_Password_Form::class      => bool( $account_forms, Account_Forms_Captcha_Settings::IS_ON_WP_LOST_PASSWORD_FORM ),
			Password_Protected_Form::class => bool( $account_forms, Account_Forms_Captcha_Settings::IS_ON_WP_POST_FORM ),
			Register_Form::class           => bool( $account_forms, Account_Forms_Captcha_Settings::IS_ON_WP_REGISTER_FORM ),
		);
	}
}
