<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress\Forms;

use Io\Prosopo\Procaptcha\Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

defined( 'ABSPATH' ) || exit;

// Used as independent part of other integrations, e.g. JetPack Gutenberg Forms.
class WP_Shortcode_Integration extends Hookable_Form_Integration_Base {
	public function set_hooks( bool $is_admin_area ): void {
		$widget = self::get_form_helper()->get_widget();

		add_shortcode( $widget->get_field_name(), array( $this, 'print_form_field' ) );
	}

	public function print_form_field(): string {
		$widget = self::get_form_helper()->get_widget();

		return $widget->print_form_field(
			array(
				Widget_Settings::IS_DESIRED_ON_GUESTS => true,
				Widget_Settings::IS_RETURN_ONLY       => true,
			)
		);
	}
}
