<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Module\Configurable_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Module\Module_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Forms\WP_Comment_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Forms\WP_Login_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Forms\WP_Lost_Password_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Forms\WP_Password_Protected_Form_Integration;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Forms\{WP_Register_Form_Integration
};
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

final class WordPress_Integration extends Module_Integration_Base implements Configurable_Module_Integration {
	private WordPress_Integration_Settings $wordpress_integration_settings;

	public function __construct( Widget $widget, WordPress_Integration_Settings $wordpress_integration_settings ) {
		parent::__construct( $widget );

		$this->wordpress_integration_settings = $wordpress_integration_settings;
	}

	public function get_about(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'WordPress Core Forms';
		$about->docs_url = self::get_docs_url( 'wordpress-plugin/#3-supported-core-wordpress-forms' );

		return $about;
	}

	public function get_settings_tab(): Settings_Tab {
		return new WordPress_Integration_Settings();
	}

	protected function get_hookable_integrations(): array {
		$settings                = $this->wordpress_integration_settings->get_settings();
		$is_on_wp_comment_form   = bool( $settings, WordPress_Integration_Settings::IS_ON_WP_COMMENT_FORM );
		$is_on_wp_login_form     = bool( $settings, WordPress_Integration_Settings::IS_ON_WP_LOGIN_FORM );
		$is_on_wp_lost_pass_form = bool( $settings, WordPress_Integration_Settings::IS_ON_WP_LOST_PASSWORD_FORM );
		$is_on_wp_post_form      = bool( $settings, WordPress_Integration_Settings::IS_ON_WP_POST_FORM );
		$is_on_wp_register_form  = bool( $settings, WordPress_Integration_Settings::IS_ON_WP_REGISTER_FORM );

		$integrations = array(
			new WP_Shortcode_Integration( $this->widget ),
		);

		if ( $is_on_wp_comment_form ) {
			$integrations[] = new WP_Comment_Form_Integration( $this->widget );
		}

		if ( $is_on_wp_login_form ) {
			$integrations[] = new WP_Login_Form_Integration( $this->widget );
		}

		if ( $is_on_wp_lost_pass_form ) {
			$integrations[] = new WP_Lost_Password_Form_Integration( $this->widget );
		}

		if ( $is_on_wp_post_form ) {
			$integrations[] = new WP_Password_Protected_Form_Integration( $this->widget );
		}

		if ( $is_on_wp_register_form ) {
			$integrations[] = new WP_Register_Form_Integration( $this->widget );
		}

		return $integrations;
	}
}
