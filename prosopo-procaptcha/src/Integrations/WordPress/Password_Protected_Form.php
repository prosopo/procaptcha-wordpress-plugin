<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use WP_Post;

class Password_Protected_Form extends WordPress_Form_Base {
	public function add_form_field( string $output, WP_Post $post ): string {
		$form_field = self::get_form_helpers()->get_captcha()->print_form_field(
			array(
				Widget_Arguments::IS_DESIRED_ON_GUESTS => true,
				Widget_Arguments::IS_RETURN_ONLY       => true,
			)
		);

		return str_replace( '</form>', $form_field . '</form>', $output );
	}

	public function verify_submission(): void {
		$captcha = self::get_form_helpers()->get_captcha();

		if ( ! $captcha->present() ||
		$captcha->human_made_request() ) {
			return;
		}

		wp_die(
			esc_html( $captcha->get_validation_error_message() ),
			'Procaptcha',
			array(
				'back_link' => true,
				'response'  => 303,
			)
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		parent::set_hooks( $is_admin_area );

		add_filter( 'the_password_form', array( $this, 'add_form_field' ), 10, 2 );
		add_action( 'login_form_postpass', array( $this, 'verify_submission' ) );
	}
}
