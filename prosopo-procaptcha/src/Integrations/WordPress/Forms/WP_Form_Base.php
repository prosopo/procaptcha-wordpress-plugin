<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

abstract class WP_Form_Base extends Widget_Integration_Base {
	public function print_form_field(): void {
		$this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px',
				),
			)
		);
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		$print_field_action = $this->get_print_field_action();

		// it can be missing, if a special approach is required.
		if ( '' === $print_field_action ) {
			return;
		}

		add_action( $print_field_action, array( $this, 'print_form_field' ) );
	}

	protected function get_print_field_action(): string {
		return '';
	}
}
