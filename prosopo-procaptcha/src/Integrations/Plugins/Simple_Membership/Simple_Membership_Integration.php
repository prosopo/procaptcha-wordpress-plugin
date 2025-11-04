<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership\Forms\SM_Login_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership\Forms\SM_Registration_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership\Forms\SM_Reset_Password_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\WordPress\WordPress_Integration_Settings;
use Io\Prosopo\Procaptcha\Widget\Widget;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

class Simple_Membership_Integration extends Plugin_Integration_Base {
	private WordPress_Integration_Settings $account_forms_tab;

	public function __construct( Widget $widget, WordPress_Integration_Settings $account_forms_tab ) {
		parent::__construct( $widget );

		$this->account_forms_tab = $account_forms_tab;
	}

	public function get_about(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Simple Membership';
		$about->docs_url = self::get_docs_url( '#4-supported-account-plugins' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'SimpleWpMembership' );
	}

	protected function get_hookable_integrations(): array {
		$settings                = $this->account_forms_tab->get_settings();
		$is_on_wp_login          = bool( $settings, WordPress_Integration_Settings::IS_ON_WP_LOGIN_FORM );
		$is_on_wp_register       = bool( $settings, WordPress_Integration_Settings::IS_ON_WP_REGISTER_FORM );
		$is_on_wp_reset_password = bool( $settings, WordPress_Integration_Settings::IS_ON_WP_LOST_PASSWORD_FORM );

		$integrations = array();

		if ( $is_on_wp_login ) {
			$integrations[] = new SM_Login_Form_Integration( $this->widget );
		}

		if ( $is_on_wp_register ) {
			$integrations[] = new SM_Registration_Form_Integration( $this->widget );
		}

		if ( $is_on_wp_reset_password ) {
			$integrations[] = new SM_Reset_Password_Form_Integration( $this->widget );
		}

		return $integrations;
	}
}
