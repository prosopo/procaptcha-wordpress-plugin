<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Jet_Form_Builder;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;

final class Jet_Form_Builder extends Plugin_Integration_Base {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'JetFormBuilder';
		$about->docs_url = self::get_docs_url( 'jetformbuilder' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'JFB_Modules\Captcha\Abstract_Captcha\Base_Captcha' );
	}

	protected function get_external_integrations(): array {
		return array(
			Jet_Captcha::class,
		);
	}

	protected function get_hookable_integrations(): array {
		return array(
			new Jet_Captcha_Integration(),
		);
	}
}
