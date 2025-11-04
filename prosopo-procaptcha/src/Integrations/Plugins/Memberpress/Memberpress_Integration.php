<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress\Account\Memberpress_Login_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress\Account\Memberpress_Reset_Password_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress\Membership\Memberpress_Register_Integration;
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class Memberpress_Integration extends Plugin_Integration_Base {
	private Account_Form_Settings $account_form_settings;

	public function __construct( Widget $widget, Account_Form_Settings $account_form_settings ) {
		parent::__construct( $widget );

		$this->account_form_settings = $account_form_settings;
	}

	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Memberpress';
		$about->docs_url = self::get_docs_url( 'memberpress' );

		return $about;
	}

	public function is_active(): bool {
		return defined( 'MEPR_PLUGIN_SLUG' );
	}

	protected function get_hookable_integrations(): array {
		$integrations = array(
			new Memberpress_Register_Integration( $this->widget ),
		);

		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new Memberpress_Login_Integration( $this->widget );
		}

		if ( $this->account_form_settings->is_password_recovery_protected() ) {
			$integrations[] = new Memberpress_Reset_Password_Integration( $this->widget );
		}

		return $integrations;
	}
}
