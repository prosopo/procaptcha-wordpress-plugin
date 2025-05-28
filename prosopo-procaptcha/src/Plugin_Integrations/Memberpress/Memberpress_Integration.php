<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Procaptcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress\Account\Memberpress_Login_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress\Account\Memberpress_Reset_Password_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress\Membership\Memberpress_Register_Integration;
use Io\Prosopo\Procaptcha\Settings\Account_Forms_Settings_Tab;
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

final class Memberpress_Integration extends Procaptcha_Plugin_Integration {
	public function get_target_plugin_classes(): array {
		// no manager class is present in the main plugin file.
		return array();
	}

	public function get_target_plugin_constants(): array {
		return array( 'MEPR_PLUGIN_SLUG' );
	}

	protected function get_form_integrations(): array {
		return array(
			Memberpress_Register_Integration::class,
		);
	}

	protected function get_conditional_form_integrations( Settings_Storage $settings_storage ): array {
		$account_forms = $settings_storage->get( Account_Forms_Settings_Tab::class )->get_settings();

		return array(
			Memberpress_Login_Integration::class          => bool( $account_forms, Account_Forms_Settings_Tab::IS_ON_WP_LOGIN_FORM ),
			Memberpress_Reset_Password_Integration::class => bool( $account_forms, Account_Forms_Settings_Tab::IS_ON_WP_LOST_PASSWORD_FORM ),
		);
	}
}
