<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Procaptcha_Assets_Manager;
use Io\Prosopo\Procaptcha\Captcha\Procaptcha;
use Io\Prosopo\Procaptcha\Definition\Captcha\Captcha;
use Io\Prosopo\Procaptcha\Captcha\Captcha_Assets;
use Io\Prosopo\Procaptcha\Integrations\{BBPress\BBPress,
	Contact_Form_7,
	Elementor_Pro\Elementor_Pro,
	Everest_Forms\Everest_Forms,
	Fluent_Forms\Fluent_Forms,
	Formidable_Forms\Formidable_Forms,
	Gravity_Forms\Gravity_Forms,
	JetPack\JetPack,
	Ninja_Forms\Ninja_Forms,
	Spectra\Spectra,
	User_Registration\User_Registration,
	WooCommerce\WooCommerce,
	WordPress\WordPress,
	WPForms\WPForms};
use Io\Prosopo\Procaptcha\Integration\Form\Captcha_Form_Integration_Helpers;
use Io\Prosopo\Procaptcha\Definition\Hookable;
use Io\Prosopo\Procaptcha\Definition\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integrations;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integrator;
use Io\Prosopo\Procaptcha\Definition\Settings\Settings_Tab;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceConfig;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewTemplateRenderer;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\ViewsManager;
use Io\Prosopo\Procaptcha\Settings\{Settings_Page,
	Captcha_Settings_Storage,
	Tabs\Account_Forms_Captcha_Settings,
	Tabs\General_Captcha_Settings,
	Tabs\Statistics};
use WP_Filesystem_Base;

class Plugin implements Hookable {
	const SLUG               = 'prosopo-procaptcha';
	const SERVICE_SCRIPT_URL = 'https://js.prosopo.io/js/procaptcha.bundle.js';

	private string $version = '1.9.0';
	private string $plugin_file;
	private Captcha $captcha;
	private Procaptcha_Assets_Manager $captcha_assets_manager;
	private Query_Arguments $query_arguments;
	private Captcha_Settings_Storage $settings_storage;
	private Settings_Page $settings_page;
	private Plugin_Integrations $plugin_integrations;
	private Plugin_Assets_Manager $assets_manager;

	/**
	 * @param string $plugin_file Optional, empty if called from the uninstall.php
	 */
	public function __construct( string $plugin_file = '' ) {
		$this->plugin_file = $plugin_file;
		$wp_filesystem     = $this->get_wp_filesystem();

		$view_template_renderer = new ViewTemplateRenderer();

		$namespace_config = ( new ViewNamespaceConfig( $view_template_renderer ) )
			->setTemplatesRootPath( __DIR__ . '/views' )
			->setTemplateFileExtension( '.blade.php' );

		$views_manager = new ViewsManager();
		$views_manager->registerNamespace( 'Io\\Prosopo\\Procaptcha\\Template_Models', $namespace_config );

		$this->assets_manager = new Plugin_Assets_Manager( $plugin_file, $this->version, $wp_filesystem );

		$this->settings_storage       = new Captcha_Settings_Storage();
		$this->captcha_assets_manager = new Procaptcha_Assets_Manager(
			self::SERVICE_SCRIPT_URL,
			'prosopo-procaptcha',
			$this->assets_manager,
			$this->settings_storage->get( General_Captcha_Settings::class ),
			new Captcha_Assets()
		);

		$this->query_arguments = new Query_Arguments();

		$this->captcha       = new Procaptcha(
			$this->settings_storage,
			$this->captcha_assets_manager,
			$this->query_arguments,
			$views_manager
		);
		$this->settings_page = new Settings_Page(
			$this,
			$this->settings_storage,
			$this->captcha,
			$this->query_arguments,
			$views_manager,
			$views_manager,
			$this->assets_manager
		);

		$this->plugin_integrations = new Plugin_Integrations(
			new Plugin_Integrator(),
			$this->settings_storage,
			new Captcha_Form_Integration_Helpers( $this->captcha, $this->query_arguments ),
			$this->settings_page,
			is_admin()
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'init', array( $this, 'load_translations' ) );

		$this->settings_page->set_hooks( $is_admin_area );
		$this->captcha_assets_manager->set_hooks( $is_admin_area );

		$plugin_integrations = $this->make_plugin_integrations();
		$this->plugin_integrations->initialize_integrations( $plugin_integrations );

		$this->settings_page->add_setting_tabs( $this->get_independent_setting_tabs() );

		$this->assets_manager->set_hooks( $is_admin_area );
	}

	public function clear_data(): void {
		$independent_setting_tabs = $this->get_independent_setting_tabs();

		$plugin_integrations      = $this->make_plugin_integrations();
		$integration_setting_tabs = $this->plugin_integrations->get_setting_tabs( $plugin_integrations );

		$settings_classes = array_merge( $independent_setting_tabs, $integration_setting_tabs );

		array_map(
			function ( string $settings_class ) {
				$settings_tab = $this->settings_storage->get( $settings_class );
				$settings_tab->clear_data();
			},
			$settings_classes
		);
	}

	public function get_basename(): string {
		return plugin_basename( $this->plugin_file );
	}

	public function get_version(): string {
		return $this->version;
	}

	public function load_translations(): void {
		load_plugin_textdomain(
			'prosopo-procaptcha',
			false,
			dirname( plugin_basename( $this->plugin_file ) ) . '/lang'
		);
	}

	/**
	 * @return class-string<Plugin_Integration>[]
	 */
	protected function get_integration_classes(): array {
		return array(
			BBPress::class,
			Contact_Form_7::class,
			Elementor_Pro::class,
			Everest_Forms::class,
			Fluent_Forms::class,
			Formidable_Forms::class,
			Gravity_Forms::class,
			JetPack::class,
			Ninja_Forms::class,
			Spectra::class,
			User_Registration::class,
			WPForms::class,
			WooCommerce::class,
			WordPress::class,
		);
	}

	/**
	 * @return Plugin_Integration[]
	 */
	protected function make_plugin_integrations(): array {
		return $this->plugin_integrations->make_plugin_integrations( $this->get_integration_classes(), $this->captcha );
	}

	/**
	 * @return class-string<Settings_Tab>[]
	 */
	protected function get_independent_setting_tabs(): array {
		return array(
			General_Captcha_Settings::class,
			Account_Forms_Captcha_Settings::class,
			Statistics::class,
		);
	}

	protected function get_wp_filesystem(): WP_Filesystem_Base {
		global $wp_filesystem;

		require_once ABSPATH . 'wp-admin/includes/file.php';

		WP_Filesystem();

		return $wp_filesystem;
	}
}
