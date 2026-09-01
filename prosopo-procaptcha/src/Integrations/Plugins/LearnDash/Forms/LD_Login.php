<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\LearnDash\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

/**
 * LearnDash renders its login modal via wp_login_form(), so the field goes into the 'login_form_middle' filter.
 *
 * The submission is processed by the native WordPress login flow,
 * therefore the validation is handled by the WordPress Login integration.
 */
final class LD_Login extends Widget_Integration_Base {
	private bool $is_learndash_form = false;

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter( 'learndash-login-form-args', array( $this, 'mark_learndash_form' ) );
		add_filter( 'login_form_middle', array( $this, 'add_form_field' ) );
	}

	/**
	 * @param mixed $form_args
	 *
	 * @return mixed
	 */
	public function mark_learndash_form( $form_args ) {
		$this->is_learndash_form = true;

		return $form_args;
	}

	/**
	 * @param mixed $content
	 */
	public function add_form_field( $content ): string {
		$content = is_string( $content ) ?
			$content :
			'';

		if ( ! $this->is_learndash_form ) {
			return $content;
		}

		// The args filter runs once per form, so release the mark to not affect the other forms on the page.
		$this->is_learndash_form = false;

		return $content . $this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px',
				),
				Widget_Settings::IS_RETURN_ONLY     => true,
			)
		);
	}
}
