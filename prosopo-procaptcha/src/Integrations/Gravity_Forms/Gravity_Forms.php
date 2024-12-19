<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Gravity_Forms;

defined( 'ABSPATH' ) || exit;

use GF_Fields;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Hooks_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage_Interface;

class Gravity_Forms extends Plugin_Integration implements Hooks_Interface {
	public function get_target_plugin_classes(): array {
		return array(
			'GF_Fields',
		);
	}

	public function get_form_integrations( Settings_Storage_Interface $settings_storage ): array {
		return array(
			Gravity_Form_Field::class,
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		if ( true === class_exists( 'GF_Fields' ) &&
		true === is_callable( array( 'GF_Fields', 'register' ) ) ) {
			// While we create the object ourselves, don't pass objects directly, as GravityForms will save its class,
			// and then create instances itself on the fly.
			GF_Fields::register( new Gravity_Form_Field() );
		}
	}
}