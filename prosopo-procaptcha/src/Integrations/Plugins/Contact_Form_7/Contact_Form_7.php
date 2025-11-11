<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Contact_Form_7;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Hookable;

// Note: CF7 v5.9.8 calls the RestAPI without the nonce, so we can't omit captcha for authorized users.
final class Contact_Form_7 extends Plugin_Integration_Base implements Hookable {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Contact Form 7';
		$about->docs_url = self::get_docs_url( 'contact-form-7' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'WPCF7' );
	}

	protected function get_hookable_integrations(): array {
		return array(
			new CF7_Field( $this->widget ),
		);
	}
}
