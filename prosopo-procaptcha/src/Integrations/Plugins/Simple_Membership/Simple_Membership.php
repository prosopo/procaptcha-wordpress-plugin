<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership\Forms\SM_Login;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership\Forms\SM_Password_Recovery;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership\Forms\SM_Registration;
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Widget\Widget;

class Simple_Membership extends Plugin_Integration_Base {
	private Account_Form_Settings $account_form_settings;

	public function __construct( Widget $widget, Account_Form_Settings $account_form_settings ) {
		parent::__construct( $widget );

		$this->account_form_settings = $account_form_settings;
	}

	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Simple Membership';
		$about->docs_url = self::get_docs_url( '#4-supported-account-plugins' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'SimpleWpMembership' );
	}

	protected function get_hookable_integrations(): array {
		$integrations = array();

		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new SM_Login( $this->widget );
		}

		if ( $this->account_form_settings->is_registration_protected() ) {
			$integrations[] = new SM_Registration( $this->widget );
		}

		if ( $this->account_form_settings->is_password_recovery_protected() ) {
			$integrations[] = new SM_Password_Recovery( $this->widget );
		}

		return $integrations;
	}
}
