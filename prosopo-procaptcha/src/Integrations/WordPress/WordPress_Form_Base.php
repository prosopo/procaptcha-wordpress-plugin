<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration_Base;

abstract class WordPress_Form_Base extends Hookable_Form_Integration_Base {
	public function print_form_field(): void {
		self::get_form_helpers()->get_captcha()->print_form_field(
			array(
				Widget_Arguments::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px',
				),
			)
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
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
