<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\About_Plugin_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress\Account\Memberpress_Login_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress\Account\Memberpress_Reset_Password_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress\Membership\Memberpress_Register_Integration;
use Io\Prosopo\Procaptcha\Settings\Account_Forms_Tab;
use Io\Prosopo\Procaptcha\Widget\Widget;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

final class Memberpress_Integration extends Plugin_Integration_Base {
	private Account_Forms_Tab $account_forms_tab;

	public function __construct( Widget $widget, Account_Forms_Tab $account_forms_tab ) {
		parent::__construct( $widget );

		$this->account_forms_tab = $account_forms_tab;
	}

	public function get_about(): About_Plugin_Integration {
		$about = new About_Plugin_Integration();

		$about->name     = 'Memberpress';
		$about->docs_url = self::get_docs_url( 'memberpress' );

		return $about;
	}

	public function is_active(): bool {
		return defined( 'MEPR_PLUGIN_SLUG' );
	}

	protected function get_hookable_integrations(): array {
		$settings           = $this->account_forms_tab->get_settings();
		$is_on_wp_login     = bool( $settings, Account_Forms_Tab::IS_ON_WP_LOGIN_FORM );
		$is_on_wp_lost_pass = bool( $settings, Account_Forms_Tab::IS_ON_WP_LOST_PASSWORD_FORM );

		$integrations = array(
			new Memberpress_Register_Integration( $this->widget ),
		);

		if ( $is_on_wp_login ) {
			$integrations[] = new Memberpress_Login_Integration( $this->widget );
		}

		if ( $is_on_wp_lost_pass ) {
			$integrations[] = new Memberpress_Reset_Password_Integration( $this->widget );
		}

		return $integrations;
	}
}
