<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Forms\Beaver_Contact;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Forms\Beaver_Login;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Forms\Beaver_Subscribe;
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class Beaver_Builder extends Plugin_Integration_Base {
	private Account_Form_Settings $account_form_settings;

	public function __construct( Widget $widget, Account_Form_Settings $account_form_settings ) {
		parent::__construct( $widget );

		$this->account_form_settings = $account_form_settings;
	}

	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Beaver Builder';
		$about->docs_url = self::get_docs_url( 'beaver-builder' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'FLBuilder' );
	}

	protected function get_hookable_integrations(): array {
		$integrations = array(
			new Beaver_Contact( $this->widget ),
			new Beaver_Subscribe( $this->widget ),
		);

		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new Beaver_Login( $this->widget );
		}

		return $integrations;
	}
}
