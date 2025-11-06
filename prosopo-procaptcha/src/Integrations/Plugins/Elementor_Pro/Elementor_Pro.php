<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Elementor_Pro;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Elementor_Pro\Elementor_Field;
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class Elementor_Pro extends Plugin_Integration_Base {
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

	protected function get_hookable_integrations(): array {
		$integrations = array(
			new Elementor_Field_Integration(),
		);

		// Login Widget submits to wp-login.php, so validation happens there,
		// therefore that option should be active.
		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new Elementor_Login_Widget( $this->widget );
		}

		return $integrations;
	}

	protected function get_external_integrations(): array {
		return array(
			Elementor_Field::class,
		);
	}
}
