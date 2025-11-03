<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Elementor_Pro;

defined( 'ABSPATH' ) || exit;

use ElementorPro\Modules\Forms\Registrars\Form_Fields_Registrar;
use Io\Prosopo\Procaptcha\Integration\Plugin\About_Plugin_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Settings\Account_Forms_Tab;
use Io\Prosopo\Procaptcha\Widget\Widget;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

final class Elementor_Pro_Integration extends Plugin_Integration_Base {
	private Account_Forms_Tab $account_forms_tab;

	public function __construct( Widget $widget, Account_Forms_Tab $account_forms_tab ) {
		parent::__construct( $widget );

		$this->account_forms_tab = $account_forms_tab;
	}

	public function get_about(): About_Plugin_Integration {
		$about = new About_Plugin_Integration();

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
		$settings      = $this->account_forms_tab->get_settings();
		$is_on_wp_form = bool( $settings, Account_Forms_Tab::IS_ON_WP_LOGIN_FORM );
		$integrations  = array();

		// Login Widget submits to wp-login.php, so validation happens there,
		// therefore that option should be active.
		if ( $is_on_wp_form ) {
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
