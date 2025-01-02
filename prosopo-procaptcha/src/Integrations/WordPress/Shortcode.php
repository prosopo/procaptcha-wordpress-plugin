<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration_Base;

defined( 'ABSPATH' ) || exit;

// Used as independent part of other integrations, e.g. JetPack Gutenberg Forms.
class Shortcode extends Hookable_Form_Integration_Base {
	public function set_hooks( bool $is_admin_area ): void {
		$captcha = self::get_form_helpers()->get_captcha();

		add_shortcode( $captcha->get_field_name(), array( $this, 'print_form_field' ) );
	}

	public function print_form_field(): string {
		$captcha = self::get_form_helpers()->get_captcha();

		return $captcha->print_form_field(
			array(
				Widget_Arguments::IS_DESIRED_ON_GUESTS => true,
				Widget_Arguments::IS_RETURN_ONLY       => true,
			)
		);
	}
}
