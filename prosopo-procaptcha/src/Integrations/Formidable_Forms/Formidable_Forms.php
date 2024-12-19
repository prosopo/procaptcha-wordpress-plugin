<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Formidable_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Hooks_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage_Interface;

class Formidable_Forms extends Plugin_Integration implements Hooks_Interface {
	public function get_target_plugin_classes(): array {
		return array( 'FrmAppHelper' );
	}

	public function get_form_integrations( Settings_Storage_Interface $settings_storage ): array {
		return array(
			Formidable_Form_Field::class,
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'frm_get_field_type_class', array( $this, 'get_field_class' ), 10, 2 );
		add_filter( 'frm_available_fields', array( $this, 'sign_up_field_type' ) );
	}

	public function get_field_class( string $class_name, string $field_type ): string {
		if ( $this->get_captcha()->get_field_name() !== $field_type ) {
			return $class_name;
		}

		return Formidable_Form_Field::class;
	}

	/**
	 * @param array<string,mixed> $fields
	 *
	 * @return array<string,mixed>
	 */
	public function sign_up_field_type( array $fields ): array {
		$captcha = $this->get_captcha();

		return array_merge(
			$fields,
			array(
				$captcha->get_field_name() => array(
					'icon' => 'frm_icon_font frm_shield_check_icon',
					'name' => $captcha->get_field_label(),
				),
			)
		);
	}
}
