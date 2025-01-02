<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Gravity_Forms;

defined( 'ABSPATH' ) || exit;

use GF_Fields;
use Io\Prosopo\Procaptcha\Definition\Hookable;
use Io\Prosopo\Procaptcha\Definition\Settings\Settings_Storage;
use Io\Prosopo\Procaptcha\Integration\Plugin\Captcha_Plugin_Integration;

class Gravity_Forms extends Captcha_Plugin_Integration implements Hookable {
	public function get_target_plugin_classes(): array {
		return array(
			'GF_Fields',
		);
	}

	public function get_form_integrations( Settings_Storage $settings_storage ): array {
		return array(
			Gravity_Form_Field::class,
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		if ( class_exists( 'GF_Fields' ) &&
		is_callable( array( 'GF_Fields', 'register' ) ) ) {
			// While we create the object ourselves, don't pass objects directly, as GravityForms will save its class,
			// and then create instances itself on the fly.
			GF_Fields::register( new Gravity_Form_Field() );
		}
	}
}
