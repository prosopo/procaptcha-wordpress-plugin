<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration\Forms\UR_Login_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration\Forms\UR_Lost_Password_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\WordPress\WordPress_Integration_Settings;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

final class User_Registration_Integration extends Plugin_Integration_Base {
	private WordPress_Integration_Settings $account_forms_tab;

	public function __construct( Widget $widget, WordPress_Integration_Settings $account_forms_tab ) {
		parent::__construct( $widget );

		$this->account_forms_tab = $account_forms_tab;
	}

	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'User Registration';
		$about->docs_url = self::get_docs_url( 'user-registration' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'UserRegistration' );
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		// Class in a global namespace, as User Registration plugin will be calling this class by its name.
		require_once __DIR__ . '/UR_Form_Field_Prosopo_Procaptcha.php';

		parent::set_hooks( $screen_detector );

		add_filter( 'user_registration_registered_form_fields', array( $this, 'register_field_type' ) );
		add_filter(
			sprintf( '%s_admin_template', UR_Form_Field_Prosopo_Procaptcha::NAME_PREFIX . $this->widget->get_field_name() ),
			fn( string $path )=>__DIR__ . '/admin_template.php'
		);
		add_filter( 'user_registration_field_keys', array( $this, 'get_field_type' ), 10, 2 );
	}

	/**
	 * @param string[] $field_types
	 *
	 * @return string[]
	 */
	public function register_field_type( array $field_types ): array {
		return array_merge(
			$field_types,
			array(
				$this->widget->get_field_name(),
			)
		);
	}

	public function get_field_type( string $field_type, string $field_key ): string {
		$widget = $this->widget;

		if ( $widget->get_field_name() === $field_key ) {
			return $widget->get_field_name();
		}

		return $field_type;
	}

	protected function get_hookable_integrations(): array {
		$settings                = $this->account_forms_tab->get_settings();
		$is_on_wp_login_form     = bool( $settings, WordPress_Integration_Settings::IS_ON_WP_LOGIN_FORM );
		$is_on_wp_lost_pass_form = bool( $settings, WordPress_Integration_Settings::IS_ON_WP_LOST_PASSWORD_FORM );

		$integrations = array(
			new UR_Form_Field_Prosopo_Procaptcha(),
		);

		if ( $is_on_wp_login_form ) {
			$integrations[] = new UR_Login_Form_Integration( $this->widget );
		}

		if ( $is_on_wp_lost_pass_form ) {
			$integrations[] = new UR_Lost_Password_Form_Integration( $this->widget );
		}

		return $integrations;
	}

	protected function get_external_integrations(): array {
		return array( UR_Form_Field_Prosopo_Procaptcha::class );
	}
}
