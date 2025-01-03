<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\Captcha;

defined( 'ABSPATH' ) || exit;

interface Captcha_Assets_Manager {
	public function add_integration_js( string $integration_name ): void;

	public function add_integration_css( string $css_code ): void;

	public function add_widget(): void;
}
