<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integrations\WordPress\WP_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use WP_Error;

final class WP_Register_Form_Integration extends WP_Form_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $screen_detector );

		add_filter(
			'registration_errors',
			fn( WP_Error $errors ): WP_Error => $this->widget->is_verification_token_valid() ?
			$errors :
			$this->widget->get_validation_error( $errors )
		);
	}

	protected function get_print_field_action(): string {
		return 'register_form';
	}
}
