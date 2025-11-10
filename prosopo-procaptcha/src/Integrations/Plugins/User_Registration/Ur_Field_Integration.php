<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use UR_Form_Field_Prosopo_Procaptcha;

final class Ur_Field_Integration extends Widget_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter(
			sprintf(
				'%s_admin_template',
				UR_Form_Field_Prosopo_Procaptcha::NAME_PREFIX . $this->widget->get_field_name()
			),
			fn() => __DIR__ . '/admin_template.php'
		);

		add_filter( 'user_registration_registered_form_fields', array( $this, 'add_field_type' ) );

		add_filter(
			'user_registration_field_keys',
			array( $this, 'add_field_key' ),
			10,
			2
		);
	}

	/**
	 * @param string[] $field_types
	 *
	 * @return string[]
	 */
	public function add_field_type( array $field_types ): array {
		return array_merge(
			$field_types,
			array(
				$this->widget->get_field_name(),
			)
		);
	}

	public function add_field_key( string $field_type, string $field_key ): string {
		$widget = $this->widget;

		if ( $widget->get_field_name() === $field_key ) {
			return $widget->get_field_name();
		}

		return $field_type;
	}
}
