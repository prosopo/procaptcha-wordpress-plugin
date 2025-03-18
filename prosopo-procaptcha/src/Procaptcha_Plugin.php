<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Widget\Widget_Assets_Loader;
use Io\Prosopo\Procaptcha\Widget\Procaptcha_Widget;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Plugin_Integrations\{BBPress\BBPress_Integration,
	Contact_Form_7_Integration,
	Elementor_Pro\Elementor_Pro_Integration,
	Everest_Forms\Everest_Forms_Integration,
	Fluent_Forms\Fluent_Forms_Integration,
	Formidable_Forms\Formidable_Forms_Integration,
	Gravity_Forms\Gravity_Forms_Integration,
	JetPack\JetPack_Integration,
	Ninja_Forms\Ninja_Forms_Integration,
	Simple_Membership\Simple_Membership_Integration,
	Spectra\Spectra_Integration,
	User_Registration\User_Registration_Integration,
	WooCommerce\WooCommerce_Integration,
	WordPress\WordPress_Integration,
	WPForms\WPForms_Integration
};
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Helper\Procaptcha_Form_Integration_Helper;
use Io\Prosopo\Procaptcha\Plugin_Integration\Plugin_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integration\Plugin_Integrations;
use Io\Prosopo\Procaptcha\Plugin_Integration\Plugin_Integrator;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceConfig;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewTemplateRenderer;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\ViewsManager;
use Io\Prosopo\Procaptcha\Settings\{Account_Forms_Settings_Tab,
	General\General_Settings_Tab,
	Settings_Page,
	Statistics\Statistics_Settings_Tab,
	Storage\Procaptcha_Settings_Storage
};

final class Procaptcha_Plugin implements Hookable {

	const SLUG                     = 'prosopo-procaptcha';
	const SERVICE_SCRIPT_URL       = 'https://js.prosopo.io/js/procaptcha.bundle.js';
	const ACCOUNT_API_ENDPOINT_URL = 'https://api.prosopo.io/sites/wp-details';

	private string $plugin_file;
	private Widget $widget;
	private Widget_Assets_Loader $widget_assets_manager;
	private Query_Arguments $query_arguments;
	private Procaptcha_Settings_Storage $settings_storage;
	private Settings_Page $settings_page;
	private Plugin_Integrations $plugin_integrations;
	private Procaptcha_Plugin_Assets $plugin_assets;

	/**
	 * @param string $plugin_file Optional, empty if called from the uninstall.php
	 */
	public function __construct( string $plugin_file = '', bool $is_dev_mode = false ) {
		$this->plugin_file = $plugin_file;

		$view_template_renderer = new ViewTemplateRenderer();

		$namespace_config = ( new ViewNamespaceConfig( $view_template_renderer ) )
			->setTemplatesRootPath( __DIR__ )
			->setTemplateFileExtension( '.blade.php' )
			->setTemplateErrorHandler(
				function ( array $event_details ) {
					// todo log.
				}
			);

		$namespace_config
			->getModules()
			->setEventDispatcher( $view_template_renderer->getModules()->getEventDispatcher() );

		$views_manager = new ViewsManager();
		$views_manager->registerNamespace( 'Io\\Prosopo\\Procaptcha', $namespace_config );

		$this->plugin_assets = new Procaptcha_Plugin_Assets( $this->plugin_file, $this->detect_current_version_number(), $is_dev_mode );

		$this->settings_storage      = new Procaptcha_Settings_Storage();
		$this->widget_assets_manager = new Widget_Assets_Loader(
			self::SERVICE_SCRIPT_URL,
			'prosopo-procaptcha',
			$this->plugin_assets->get_loader(),
			$this->settings_storage->get( General_Settings_Tab::class )
		);

		$this->query_arguments = new Query_Arguments();

		$this->widget        = new Procaptcha_Widget(
			$this->settings_storage,
			$this->widget_assets_manager,
			$this->query_arguments,
			$views_manager
		);
		$this->settings_page = new Settings_Page(
			$this,
			$this->settings_storage,
			$this->widget,
			$this->query_arguments,
			$views_manager,
			$views_manager,
			$this->plugin_assets->get_resolver(),
			$this->plugin_assets->get_loader()
		);

		$this->plugin_integrations = new Plugin_Integrations(
			new Plugin_Integrator(),
			$this->settings_storage,
			new Procaptcha_Form_Integration_Helper( $this->widget, $this->query_arguments ),
			$this->settings_page,
			is_admin()
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'init', array( $this, 'load_translations' ) );

		$this->settings_page->set_hooks( $is_admin_area );
		$this->widget_assets_manager->set_hooks( $is_admin_area );

		$plugin_integrations = $this->make_plugin_integrations();
		$this->plugin_integrations->initialize_integrations( $plugin_integrations );

		$this->settings_page->add_setting_tabs( $this->get_independent_setting_tabs() );

		$this->plugin_assets->set_hooks( $is_admin_area );
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

	public function load_translations(): void {
		load_plugin_textdomain(
			'prosopo-procaptcha',
			false,
			dirname( plugin_basename( $this->plugin_file ) ) . '/lang'
		);
	}

	protected function detect_current_version_number(): string {
        // @phpcs:ignore
        $plugin_file_content = (string)file_get_contents($this->plugin_file);

		preg_match( '/Version:(.*)/', $plugin_file_content, $matches );

		$current_version_number = $matches[1] ?? '1.0.0';

		return trim( $current_version_number );
	}

	/**
	 * @return class-string<Plugin_Integration>[]
	 */
	protected function get_integration_classes(): array {
		return array(
			BBPress_Integration::class,
			Contact_Form_7_Integration::class,
			Elementor_Pro_Integration::class,
			Everest_Forms_Integration::class,
			Fluent_Forms_Integration::class,
			Formidable_Forms_Integration::class,
			Gravity_Forms_Integration::class,
			JetPack_Integration::class,
			Ninja_Forms_Integration::class,
			Spectra_Integration::class,
			User_Registration_Integration::class,
			WPForms_Integration::class,
			WooCommerce_Integration::class,
			WordPress_Integration::class,
			Simple_Membership_Integration::class,
		);
	}

	/**
	 * @return Plugin_Integration[]
	 */
	protected function make_plugin_integrations(): array {
		return $this->plugin_integrations->make_plugin_integrations( $this->get_integration_classes(), $this->widget );
	}

	/**
	 * @return class-string<Settings_Tab>[]
	 */
	protected function get_independent_setting_tabs(): array {
		return array(
			General_Settings_Tab::class,
			Account_Forms_Settings_Tab::class,
			Statistics_Settings_Tab::class,
		);
	}
}
