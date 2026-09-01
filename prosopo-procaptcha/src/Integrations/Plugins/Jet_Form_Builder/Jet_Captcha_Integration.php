<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Jet_Form_Builder;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Utils\Hookable;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class Jet_Captcha_Integration implements Hookable {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter( 'jet-form-builder/captcha/types', array( $this, 'add_captcha_type' ) );
	}

	/**
	 * @param mixed $captcha_types
	 *
	 * @return array<int,mixed>
	 */
	public function add_captcha_type( $captcha_types ): array {
		$captcha_types = is_array( $captcha_types ) ?
			array_values( $captcha_types ) :
			array();

		$captcha_types[] = new Jet_Captcha();

		return $captcha_types;
	}
}
