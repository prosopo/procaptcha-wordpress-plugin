<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Simple_Membership;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integrations\Simple_Membership\Forms\SM_Login_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\Simple_Membership\Forms\SM_Registration_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\Simple_Membership\Forms\SM_Reset_Password_Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integration\Procaptcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Settings\Account_Forms_Settings_Tab;
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

class Simple_Membership_Integration extends Procaptcha_Plugin_Integration {
	public function get_target_plugin_classes(): array {
		return array( 'SimpleWpMembership' );
	}

	protected function get_conditional_form_integrations( Settings_Storage $settings_storage ): array {
		$account_forms = $settings_storage->get( Account_Forms_Settings_Tab::class )->get_settings();

		return array(
			SM_Login_Form_Integration::class          => bool( $account_forms, Account_Forms_Settings_Tab::IS_ON_WP_LOGIN_FORM ),
			SM_Registration_Form_Integration::class   => bool( $account_forms, Account_Forms_Settings_Tab::IS_ON_WP_REGISTER_FORM ),
			SM_Reset_Password_Form_Integration::class => bool( $account_forms, Account_Forms_Settings_Tab::IS_ON_WP_LOST_PASSWORD_FORM ),
		);
	}
}
