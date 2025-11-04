<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\About_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Account_Forms_Tab;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms\Beaver_Contact_Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms\Beaver_Login_Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms\Beaver_Subscribe_Form_Integration;
use Io\Prosopo\Procaptcha\Widget\Widget;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

final class Beaver_Builder_Integration extends Plugin_Integration_Base {
	private Account_Forms_Tab $account_forms_tab;

	public function __construct( Widget $widget, Account_Forms_Tab $account_forms_tab ) {
		parent::__construct( $widget );

		$this->account_forms_tab = $account_forms_tab;
	}

	public function get_about(): About_Integration {
		$about = new About_Integration();

		$about->name     = 'Beaver Builder';
		$about->docs_url = self::get_docs_url( 'beaver-builder' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'FLBuilder' );
	}

	protected function get_hookable_integrations(): array {
		$account_settings = $this->account_forms_tab->get_settings();
		$is_on_wp_login   = bool( $account_settings, Account_Forms_Tab::IS_ON_WP_LOGIN_FORM );

		$integrations = array(
			new Beaver_Contact_Form_Integration( $this->widget ),
			new Beaver_Subscribe_Form_Integration( $this->widget ),
		);

		if ( $is_on_wp_login ) {
			$integrations[] = new Beaver_Login_Form_Integration( $this->widget );
		}

		return $integrations;
	}
}
