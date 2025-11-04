<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration\Forms\UR_Login_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration\Forms\UR_Lost_Password_Form_Integration;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class User_Registration_Integration extends Plugin_Integration_Base {
	private Account_Form_Settings $account_form_settings;

	public function __construct( Widget $widget, Account_Form_Settings $account_form_settings ) {
		parent::__construct( $widget );

		$this->account_form_settings = $account_form_settings;
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
		$integrations = array(
			new UR_Form_Field_Prosopo_Procaptcha(),
		);

		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new UR_Login_Form_Integration( $this->widget );
		}

		if ( $this->account_form_settings->is_password_recovery_protected() ) {
			$integrations[] = new UR_Lost_Password_Form_Integration( $this->widget );
		}

		return $integrations;
	}

	protected function get_external_integrations(): array {
		return array( UR_Form_Field_Prosopo_Procaptcha::class );
	}
}
