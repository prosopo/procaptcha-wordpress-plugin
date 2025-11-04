<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Formidable_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\About_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;

final class Formidable_Forms_Integration extends Plugin_Integration_Base {
	public function get_about(): About_Integration {
		$about = new About_Integration();

		$about->name     = 'Formidable Forms';
		$about->docs_url = self::get_docs_url( 'formidable' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'FrmAppHelper' );
	}


	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $screen_detector );

		add_filter( 'frm_get_field_type_class', array( $this, 'get_field_class' ), 10, 2 );
		add_filter( 'frm_available_fields', array( $this, 'sign_up_field_type' ) );
	}

	public function get_field_class( string $class_name, string $field_type ): string {
		if ( $this->widget->get_field_name() === $field_type ) {
			return Formidable_Forms_Form_Integration::class;
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

	protected function get_external_integrations(): array {
		return array(
			Formidable_Forms_Form_Integration::class,
		);
	}
}
