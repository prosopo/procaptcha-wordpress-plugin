<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\LearnDash;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\LearnDash\Forms\LD_Login;
use Io\Prosopo\Procaptcha\Integrations\Plugins\LearnDash\Forms\LD_Password_Recovery;
use Io\Prosopo\Procaptcha\Integrations\Plugins\LearnDash\Forms\LD_Registration;
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class LearnDash extends Plugin_Integration_Base {
	private Account_Form_Settings $account_form_settings;

	public function __construct( Widget $widget, Account_Form_Settings $account_form_settings ) {
		parent::__construct( $widget );

		$this->account_form_settings = $account_form_settings;
	}

	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'LearnDash LMS';
		$about->docs_url = self::get_docs_url( 'learndash' );

		return $about;
	}

	public function is_active(): bool {
		return defined( 'LEARNDASH_VERSION' );
	}

	protected function get_hookable_integrations(): array {
		$integrations = array(
			new LD_Registration( $this->widget ),
		);

		// The LearnDash login and password recovery forms are processed by the native WordPress flow,
		// so the validation happens there, therefore these options should be active.
		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new LD_Login( $this->widget );
		}

		if ( $this->account_form_settings->is_password_recovery_protected() ) {
			$integrations[] = new LD_Password_Recovery( $this->widget );
		}

		return $integrations;
	}
}
