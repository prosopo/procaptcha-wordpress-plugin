<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Divi;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Divi\Forms\Divi_Contact_Form;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Divi\Forms\Divi_Login;
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class Divi extends Plugin_Integration_Base {
	/**
	 * Themes that ship the Divi Builder.
	 */
	const BUILDER_THEMES = array( 'Divi', 'Extra' );

	private Account_Form_Settings $account_form_settings;

	public function __construct( Widget $widget, Account_Form_Settings $account_form_settings ) {
		parent::__construct( $widget );

		$this->account_form_settings = $account_form_settings;
	}

	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Divi';
		$about->docs_url = self::get_docs_url( 'divi' );

		return $about;
	}

	public function is_active(): bool {
		// The Divi Builder plugin defines its constant before the 'plugins_loaded' hook,
		// while the Divi and Extra themes are loaded later, so they're detected by the template name.
		return defined( 'ET_BUILDER_PLUGIN_VERSION' ) ||
			in_array( get_template(), self::BUILDER_THEMES, true );
	}

	protected function get_hookable_integrations(): array {
		$integrations = array(
			new Divi_Contact_Form( $this->widget ),
		);

		// The Login module submits to the native WordPress login flow, so validation happens there,
		// therefore that option should be active.
		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new Divi_Login( $this->widget );
		}

		return $integrations;
	}
}
