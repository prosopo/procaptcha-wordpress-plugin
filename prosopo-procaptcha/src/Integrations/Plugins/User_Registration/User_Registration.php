<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration\Forms\UR_Login;
use Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration\Forms\UR_Password_Recovery;
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class User_Registration extends Plugin_Integration_Base {
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

	protected function load(): void {
		// Class in a global namespace, as User Registration plugin will be calling this class by its name.
		require_once __DIR__ . '/UR_Form_Field_Prosopo_Procaptcha.php';
	}


	protected function get_external_integrations(): array {
		return array( UR_Form_Field_Prosopo_Procaptcha::class );
	}

	protected function get_hookable_integrations(): array {
		$integrations = array(
			new Ur_Field_Integration( $this->widget ),
			new UR_Form_Field_Prosopo_Procaptcha(),
		);

		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new UR_Login( $this->widget );
		}

		if ( $this->account_form_settings->is_password_recovery_protected() ) {
			$integrations[] = new UR_Password_Recovery( $this->widget );
		}

		return $integrations;
	}
}
