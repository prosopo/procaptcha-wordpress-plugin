<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Formidable_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Plugin\Procaptcha_Plugin_Integration;

class Formidable_Forms_Integration extends Procaptcha_Plugin_Integration implements Hookable {
	public function get_target_plugin_classes(): array {
		return array( 'FrmAppHelper' );
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'frm_get_field_type_class', array( $this, 'get_field_class' ), 10, 2 );
		add_filter( 'frm_available_fields', array( $this, 'sign_up_field_type' ) );
	}

	public function get_field_class( string $class_name, string $field_type ): string {
		if ( $this->get_widget()->get_field_name() !== $field_type ) {
			return $class_name;
		}

		return Formidable_Forms_Form_Integration::class;
	}

	/**
	 * @param array<string,mixed> $fields
	 *
	 * @return array<string,mixed>
	 */
	public function sign_up_field_type( array $fields ): array {
		$widget = $this->get_widget();

		return array_merge(
			$fields,
			array(
				$widget->get_field_name() => array(
					'icon' => 'frm_icon_font frm_shield_check_icon',
					'name' => $widget->get_field_label(),
				),
			)
		);
	}

	protected function get_form_integrations(): array {
		return array(
			Formidable_Forms_Form_Integration::class,
		);
	}
}
