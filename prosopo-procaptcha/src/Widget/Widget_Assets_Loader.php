<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Widget;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Assets\Assets_Loader;
use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Settings\Procaptcha_Settings;

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
	private Procaptcha_Settings $procaptcha_settings;

	public function __construct(
		string $service_script_url,
		string $service_script_handle,
		Assets_Loader $assets_loader,
		Procaptcha_Settings $procaptcha_settings
	) {
		$this->service_script_url    = $service_script_url;
		$this->service_script_handle = $service_script_handle;

		$this->procaptcha_settings = $procaptcha_settings;
		$this->assets_loader       = $assets_loader;

		$this->is_widget_in_use           = false;
		$this->plugin_integration_scripts = array();
		$this->integrations_css_code      = '';
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		$hook = $screen_detector->is_admin_area() ?
			'admin_print_footer_scripts' :
			'wp_print_footer_scripts';

		// priority must be less than 10, to make sure the wp_enqueue_script still has effect.
		add_action( $hook, array( $this, 'enqueue_assets_when_in_use' ), 1 );
	}

	public function load_integration_script( string $integration_name ): void {
		// skip loading the same script twice.
		if ( in_array( $integration_name, $this->plugin_integration_scripts, true ) ) {
			return;
		}

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
		$widget_attributes = array(
			'captchaType' => $this->procaptcha_settings->get_type(),
			'siteKey'     => $this->procaptcha_settings->get_site_key(),
			'theme'       => $this->procaptcha_settings->get_theme(),
		);

		$widget_attributes = apply_filters( 'prosopo/procaptcha/captcha_attributes', $widget_attributes );

		$this->assets_loader->load_script_asset(
			'integrations/procaptcha/procaptcha-integration.min.js',
			array(),
			'procaptchaWpAttributes',
			$widget_attributes
		);
	}

	protected function load_plugin_integration_scripts(): void {
		foreach ( $this->plugin_integration_scripts as $integration_script ) {
			$relative_script_path = sprintf(
				'integrations/plugins/%s',
				$integration_script
			);

			$this->assets_loader->load_script_asset( $relative_script_path );
		}
	}
}
