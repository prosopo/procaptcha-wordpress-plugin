<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Assets;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Assets\Assets_Loader;
use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Settings\Tabs\General_Procaptcha_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

final class Widget_Assets_Loader implements Hookable {
	private string $service_script_url;
	private string $service_script_handle;

	private bool $is_widget_in_use;
	/**
	 * Some JS-based forms (like NinjaForms) require a separate JS integration.
	 *
	 * @var string[]
	 */
	private array $plugin_integration_scripts;
	private string $integrations_css_code;

	private Assets_Loader $assets_loader;
	private Settings_Tab $general_settings;

	public function __construct(
		string $service_script_url,
		string $service_script_handle,
		Assets_Loader $assets_loader,
		Settings_Tab $general_settings
	) {
		$this->service_script_url    = $service_script_url;
		$this->service_script_handle = $service_script_handle;

		$this->general_settings = $general_settings;
		$this->assets_loader    = $assets_loader;

		$this->is_widget_in_use           = false;
		$this->plugin_integration_scripts = array();
		$this->integrations_css_code      = '';
	}

	public function set_hooks( bool $is_admin_area ): void {
		$hook = $is_admin_area ?
			'admin_print_footer_scripts' :
			'wp_print_footer_scripts';

		// priority must be less than 10, to make sure the wp_enqueue_script still has effect.
		add_action( $hook, array( $this, 'enqueue_assets_when_in_use' ), 1 );
	}

	public function load_integration_script( string $integration_name ): void {
		$this->plugin_integration_scripts[] = $integration_name;
	}

	public function load_integration_css( string $css_code ): void {
		$this->integrations_css_code .= $css_code;
	}

	public function load_widget(): void {
		$this->is_widget_in_use = true;
	}

	public function enqueue_assets_when_in_use(): void {
		if ( ! $this->is_widget_in_use ) {
			return;
		}

		$this->load_service_script();
		$this->load_widget_script();
		$this->load_plugin_integration_scripts();

		if ( '' !== $this->integrations_css_code ) {
			printf( '<style>%s</style>', esc_html( $this->integrations_css_code ) );
		}
	}

	protected function load_service_script(): void {
		$this->assets_loader->load_script( $this->service_script_handle, $this->service_script_url );
	}

	protected function load_widget_script(): void {
		$general_settings = $this->general_settings->get_settings();

		$widget_attributes = array(
			'captchaType' => string( $general_settings, General_Procaptcha_Settings::TYPE ),
			'siteKey'     => string( $general_settings, General_Procaptcha_Settings::SITE_KEY ),
			'theme'       => string( $general_settings, General_Procaptcha_Settings::THEME ),
		);

		$widget_attributes = apply_filters( 'prosopo/procaptcha/captcha_attributes', $widget_attributes );

		$this->assets_loader->load_script_asset(
			'procaptcha-integration/procaptcha-integration.min.js',
			array(),
			'procaptchaWpAttributes',
			$widget_attributes
		);
	}

	protected function load_plugin_integration_scripts(): void {
		foreach ( $this->plugin_integration_scripts as $integration_script ) {
			$relative_script_path = sprintf(
				'procaptcha-integration/plugins/%s',
				$integration_script
			);

			$this->assets_loader->load_script_asset( $relative_script_path );
		}
	}
}
