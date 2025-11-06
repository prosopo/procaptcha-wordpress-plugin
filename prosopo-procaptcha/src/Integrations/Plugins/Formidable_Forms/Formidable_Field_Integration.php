<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Formidable_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class Formidable_Field_Integration extends Widget_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter( 'frm_get_field_type_class', array( $this, 'get_field_class' ), 10, 2 );
		add_filter( 'frm_available_fields', array( $this, 'sign_up_field_type' ) );
	}

	public function get_field_class( string $class_name, string $field_type ): string {
		if ( $this->widget->get_field_name() === $field_type ) {
			return Formidable_Field::class;
		}

		return $class_name;
	}

	/**
	 * @param array<string,mixed> $fields
	 *
	 * @return array<string,mixed>
	 */
	public function sign_up_field_type( array $fields ): array {
		return array_merge(
			$fields,
			array(
				$this->widget->get_field_name() => array(
					'icon' => 'frm_icon_font frm_shield_check_icon',
					'name' => $this->widget->get_field_label(),
				),
			)
		);
	}
}
