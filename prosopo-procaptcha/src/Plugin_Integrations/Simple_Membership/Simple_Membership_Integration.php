<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Simple_Membership;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\About_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Account_Forms_Tab;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Simple_Membership\Forms\SM_Login_Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Simple_Membership\Forms\SM_Registration_Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Simple_Membership\Forms\SM_Reset_Password_Form_Integration;
use Io\Prosopo\Procaptcha\Widget\Widget;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

class Simple_Membership_Integration extends Plugin_Integration_Base {
	private Account_Forms_Tab $account_forms_tab;

	public function __construct( Widget $widget, Account_Forms_Tab $account_forms_tab ) {
		parent::__construct( $widget );

		$this->account_forms_tab = $account_forms_tab;
	}

	public function get_about(): About_Integration {
		$about = new About_Integration();

		$about->name     = 'Simple Membership';
		$about->docs_url = self::get_docs_url( '#4-supported-account-plugins' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'SimpleWpMembership' );
	}

	protected function get_hookable_integrations(): array {
		$settings                = $this->account_forms_tab->get_settings();
		$is_on_wp_login          = bool( $settings, Account_Forms_Tab::IS_ON_WP_LOGIN_FORM );
		$is_on_wp_register       = bool( $settings, Account_Forms_Tab::IS_ON_WP_REGISTER_FORM );
		$is_on_wp_reset_password = bool( $settings, Account_Forms_Tab::IS_ON_WP_LOST_PASSWORD_FORM );

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
