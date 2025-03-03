<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Widget\Assets;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin\Assets\Plugin_Frontend_Assets;
use Io\Prosopo\Procaptcha\Settings\Tabs\General_Procaptcha_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class Widget_Assets {
	/**
	 * @param array<string,mixed> $general_settings
	 */
	public function enqueue_widget_js( string $handle, Plugin_Frontend_Assets $assets_manager, array $general_settings ): void {
		$widget_attributes = array(
			'captchaType' => string( $general_settings, General_Procaptcha_Settings::TYPE ),
			'siteKey'     => string( $general_settings, General_Procaptcha_Settings::SITE_KEY ),
			'theme'       => string( $general_settings, General_Procaptcha_Settings::THEME ),
		);

		$widget_attributes = apply_filters( 'prosopo/procaptcha/captcha_attributes', $widget_attributes );

		// do not use wp_enqueue_module() because it doesn't work on the login screens.
		wp_enqueue_script(
			$handle,
			$assets_manager->get_asset_url( 'widget/widget.min.js' ),
			array(),
			$assets_manager->get_assets_version(),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		wp_localize_script( $handle, 'procaptchaWpAttributes', $widget_attributes );
	}

	/**
	 * @param string[] $integration_names
	 */
	public function enqueue_integration_js_files( string $handle_prefix, Plugin_Frontend_Assets $assets_manager, array $integration_names ): void {
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
