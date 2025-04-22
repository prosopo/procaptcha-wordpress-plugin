<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder;

use Io\Prosopo\Procaptcha\Plugin_Integration\Procaptcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms\Beaver_Contact_Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms\Beaver_Login_Form_Integration;
use Io\Prosopo\Procaptcha\Settings\Account_Forms_Settings_Tab;
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

defined( 'ABSPATH' ) || exit;

final class Beaver_Builder_Integration extends Procaptcha_Plugin_Integration {

	public function get_target_plugin_classes(): array {
		return array( 'FLBuilder' );
	}

	protected function get_form_integrations(): array {
		return array(
			Beaver_Contact_Form_Integration::class,
		);
	}

	protected function get_conditional_form_integrations( Settings_Storage $settings_storage ): array {
		$account_forms = $settings_storage->get( Account_Forms_Settings_Tab::class )->get_settings();

		return array(
			Beaver_Login_Form_Integration::class => bool( $account_forms, Account_Forms_Settings_Tab::IS_ON_WP_LOGIN_FORM ),
		);
	}
}
