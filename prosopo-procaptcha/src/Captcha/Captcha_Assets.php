<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Captcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Assets_Manager;
use Io\Prosopo\Procaptcha\Settings\Tabs\General_Captcha_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class Captcha_Assets {

	public function add_module_attr_when_missing( string $tag ): string {
		if (
			// make sure we don't make it twice if other Procaptcha integrations are present.
			false !== strpos( 'type="module"', $tag )
		) {
			return $tag;
		}

		// for old WP versions.
		$tag = str_replace( ' type="text/javascript"', '', $tag );

		return str_replace( 'src', 'type="module" src', $tag );
	}

	/**
	 * @param array<string,mixed> $general_settings
	 */
	public function enqueue_widget_js( string $handle, Assets_Manager $assets_manager, array $general_settings ): void {
		$captcha_attributes = array(
			'captchaType' => string( $general_settings, General_Captcha_Settings::TYPE ),
			'siteKey'     => string( $general_settings, General_Captcha_Settings::SITE_KEY ),
			'theme'       => string( $general_settings, General_Captcha_Settings::THEME ),
		);

		$captcha_attributes = apply_filters( 'prosopo/procaptcha/captcha_attributes', $captcha_attributes );

		// do not use wp_enqueue_module() because it doesn't work on the login screens.
		wp_enqueue_script(
			$handle,
			$assets_manager->get_asset_url( 'widget.min.js' ),
			array(),
			$assets_manager->get_assets_version(),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		wp_localize_script( $handle, 'procaptchaWpAttributes', $captcha_attributes );
	}

	/**
	 * @param string[] $integration_names
	 */
	public function enqueue_integration_js_files( string $handle_prefix, Assets_Manager $assets_manager, array $integration_names ): void {
		array_map(
			function ( $integration_name ) use ( $handle_prefix, $assets_manager ) {
				// do not use wp_enqueue_module() because it doesn't work on the login screens.
				wp_enqueue_script(
					$handle_prefix . $integration_name,
					$assets_manager->get_asset_url( 'integrations/' . $integration_name . '.min.js' ),
					array(),
					$assets_manager->get_assets_version(),
					array(
						'in_footer' => true,
						'strategy'  => 'defer',
					)
				);
			},
			$integration_names
		);
	}

	public function enqueue_service_js( string $handle, string $url ): void {
		// do not use wp_enqueue_module() because it doesn't work on the login screens.
		wp_enqueue_script(
			$handle,
			$url,
			array(),
			// Don't add any version, since it's remote, and can be changed regardless of the releases.
			null, // @phpcs:ignore
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}

	public function print_css_code( string $css_code ): void {
		printf( '<style>%s</style>', esc_html( $css_code ) );
	}
}
