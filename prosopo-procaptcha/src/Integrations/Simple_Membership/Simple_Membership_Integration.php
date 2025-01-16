<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Simple_Membership;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Procaptcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Integrations\Simple_Membership\Forms\SM_Login_Form_Integration;
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;

class Simple_Membership_Integration extends Procaptcha_Plugin_Integration {
	public function get_target_plugin_classes(): array {
		return array( 'SimpleWpMembership' );
	}

	public function get_form_integrations( Settings_Storage $settings_storage ): array {
		// fixme check if LoginForm is enabled in the settings.
		return array(
			SM_Login_Form_Integration::class,
		);
	}
}
