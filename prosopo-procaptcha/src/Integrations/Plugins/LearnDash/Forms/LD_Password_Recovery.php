<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\LearnDash\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

/**
 * LearnDash prints its password recovery form via the 'ld_reset_password' shortcode.
 *
 * The submission is processed by the native WordPress flow,
 * therefore the validation is handled by the WordPress Password Recovery integration.
 */
final class LD_Password_Recovery extends Widget_Integration_Base {
	const SHORTCODE_NAME = 'ld_reset_password';

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter( 'do_shortcode_tag', array( $this, 'add_form_field' ), 10, 2 );
	}

	/**
	 * @param mixed $shortcode_content
	 */
	public function add_form_field( $shortcode_content, string $shortcode_name ): string {
		$shortcode_content = is_string( $shortcode_content ) ?
			$shortcode_content :
			'';

		if ( self::SHORTCODE_NAME !== $shortcode_name ) {
			return $shortcode_content;
		}

		$widget_field = $this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px',
				),
				Widget_Settings::IS_RETURN_ONLY     => true,
			)
		);

		$submit_button = '/(<(?:input|button)\b[^>]*\btype=["\']submit["\'])/i';

		return (string) preg_replace(
			$submit_button,
			$widget_field . "\n$1",
			$shortcode_content,
			1
		);
	}
}
