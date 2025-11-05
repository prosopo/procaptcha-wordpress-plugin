<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Elementor_Pro;

defined( 'ABSPATH' ) || exit;

use ElementorPro\Modules\Forms\Registrars\Form_Fields_Registrar;
use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Elementor_Pro\Elementor_Form_Integration;
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class Elementor_Pro_Integration extends Plugin_Integration_Base {
	private Account_Form_Settings $account_form_settings;

	public function __construct( Widget $widget, Account_Form_Settings $account_form_settings ) {
		parent::__construct( $widget );

		$this->account_form_settings = $account_form_settings;
	}

	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Elementor Pro';
		$about->docs_url = self::get_docs_url( 'elementor-pro' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'ElementorPro\Plugin' );
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $screen_detector );

		add_action(
			'elementor_pro/forms/fields/register',
			function ( Form_Fields_Registrar $registrar ) {
				$registrar->register( new Elementor_Form_Integration() );
			}
		);
	}

	protected function get_hookable_integrations(): array {
		$integrations = array();

		// Login Widget submits to wp-login.php, so validation happens there,
		// therefore that option should be active.
		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new Elementor_Login_Widget_Integration( $this->widget );
		}

		return $integrations;
	}

	protected function get_external_integrations(): array {
		return array(
			Elementor_Form_Integration::class,
		);
	}
}
