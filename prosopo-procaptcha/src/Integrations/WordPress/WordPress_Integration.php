<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Procaptcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Forms\{WP_Comment_Form_Integration_Integration,
	WP_Login_Form_Integration_Integration,
	WP_Lost_Password_Form_Integration_Integration,
	WP_Password_Protected_Form_Integration_Integration,
	WP_Register_Form_Integration_Integration,
	WP_Shortcode_Integration};
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;
use Io\Prosopo\Procaptcha\Settings\Tabs\Account_Forms_Procaptcha_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

class WordPress_Integration extends Procaptcha_Plugin_Integration {
	public function get_target_plugin_classes(): array {
		return array();
	}

	protected function get_form_integrations(): array {
		return array(
			WP_Shortcode_Integration::class,
		);
	}

	protected function get_conditional_form_integrations( Settings_Storage $settings_storage ): array {
		$account_forms = $settings_storage->get( Account_Forms_Procaptcha_Settings::class )->get_settings();

		return array(
			WP_Comment_Form_Integration_Integration::class => bool( $account_forms, Account_Forms_Procaptcha_Settings::IS_ON_WP_COMMENT_FORM ),
			WP_Login_Form_Integration_Integration::class   => bool( $account_forms, Account_Forms_Procaptcha_Settings::IS_ON_WP_LOGIN_FORM ),
			WP_Lost_Password_Form_Integration_Integration::class => bool( $account_forms, Account_Forms_Procaptcha_Settings::IS_ON_WP_LOST_PASSWORD_FORM ),
			WP_Password_Protected_Form_Integration_Integration::class => bool( $account_forms, Account_Forms_Procaptcha_Settings::IS_ON_WP_POST_FORM ),
			WP_Register_Form_Integration_Integration::class => bool( $account_forms, Account_Forms_Procaptcha_Settings::IS_ON_WP_REGISTER_FORM ),
		);
	}
}
