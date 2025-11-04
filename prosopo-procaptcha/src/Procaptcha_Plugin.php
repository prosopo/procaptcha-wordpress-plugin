<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Integrations\Integrations_Loader;
use Io\Prosopo\Procaptcha\Integrations\Plugins\BBPress\BBPress_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Beaver_Builder_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Elementor_Pro\Elementor_Pro_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Everest_Forms\Everest_Forms_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Fluent_Forms\Fluent_Forms_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Formidable_Forms\Formidable_Forms_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Gravity_Forms\Gravity_Forms_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\JetPack\JetPack_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress\Memberpress_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Ninja_Forms\Ninja_Forms_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership\Simple_Membership_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Spectra\Spectra_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration\User_Registration_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\WooCommerce_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WPForms\WPForms_Integration;
use Io\Prosopo\Procaptcha\Integrations\WordPress\WordPress_Integration;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Assets_Loader;
use Io\Prosopo\Procaptcha\Widget\Procaptcha_Widget;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Integrations\Plugins\{Contact_Form_7_Integration};
use Io\Prosopo\Procaptcha\Plugin_Integration\Plugin_Integrations;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceConfig;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewTemplateRenderer;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\ViewsManager;
use Io\Prosopo\Procaptcha\Settings\{Account_Form_Settings,
	Compatible_Plugins\Compatible_Plugins_Tab,
	General\General_Settings_Tab,
	Settings_Page,
	Statistics\Statistics_Settings_Tab,
	Storage\Procaptcha_Settings_Storage};

final class Procaptcha_Plugin implements Hookable {

	const SLUG                     = 'prosopo-procaptcha';
	const SERVICE_SCRIPT_URL       = 'https://js.prosopo.io/js/procaptcha.bundle.js';
	const ACCOUNT_API_ENDPOINT_URL = 'https://api.prosopo.io/sites/wp-details';
	const DOCS_URL_BASE            = 'https://docs.prosopo.io/en/wordpress-plugin';

	private string $plugin_file;
	private Widget $widget;
	private Widget_Assets_Loader $widget_assets_manager;
	private Integrations_Loader $integrations_loader;
	private Settings_Page $settings_page;
	private Plugin_Integrations $plugin_integrations;
	private Procaptcha_Plugin_Assets $plugin_assets;
	private Account_Form_Settings $account_form_settings;

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

		$this->widget_assets_manager = new Widget_Assets_Loader(
			self::SERVICE_SCRIPT_URL,
			'prosopo-procaptcha',
			$this->plugin_assets->get_loader(),
			$this->settings_storage->get( General_Settings_Tab::class )
		);

		$this->widget        = new Procaptcha_Widget(
			$this->settings_storage,
			$this->widget_assets_manager,
			$views_manager
		);
		$this->settings_page = new Settings_Page(
			$this,
			$this->settings_storage,
			$this->widget,
			$views_manager,
			$views_manager,
			$this->plugin_assets->get_resolver(),
			$this->plugin_assets->get_loader()
		);
		foreach ( $this->get_standalone_settings_tabs() as $standalone_settings_tab ) {
			$this->settings_page->add_tab( $standalone_settings_tab );
		}

		$wordpress_integration       = new WordPress_Integration( $this->widget );
		$this->account_form_settings = $wordpress_integration->get_account_form_settings();

		$this->integrations_loader = new Integrations_Loader( $this->settings_page );
		$this->integrations_loader->set_module_integrations( array( $wordpress_integration ) );
		$this->integrations_loader->set_plugin_integrations( $this->get_plugin_integrations() );
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action( 'init', array( $this, 'load_translations' ) );

		$this->settings_page->set_hooks( $screen_detector );
		$this->widget_assets_manager->set_hooks( $screen_detector );
		$this->plugin_assets->set_hooks( $screen_detector );
		$this->integrations_loader->set_hooks( $screen_detector );
	}

	public function clear_data(): void {
		$settings_tabs = $this->integrations_loader->get_all_settings_tabs();

		foreach ( $settings_tabs as $settings_tab ) {
			$settings_tab->clear_data();
		}
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
	 * @return Plugin_Integration[]
	 */
	protected function get_plugin_integrations(): array {
		return array(
			new BBPress_Integration( $this->widget ),
			new Contact_Form_7_Integration( $this->widget ),
			new Elementor_Pro_Integration( $this->widget, $this->account_form_settings ),
			new Everest_Forms_Integration( $this->widget ),
			new Fluent_Forms_Integration( $this->widget ),
			new Formidable_Forms_Integration( $this->widget ),
			new Gravity_Forms_Integration( $this->widget ),
			new JetPack_Integration( $this->widget ),
			new Ninja_Forms_Integration( $this->widget ),
			new Spectra_Integration( $this->widget ),
			new User_Registration_Integration( $this->widget, $this->account_form_settings ),
			new WPForms_Integration( $this->widget ),
			new WooCommerce_Integration( $this->widget, $this->account_form_settings ),
			new Simple_Membership_Integration( $this->widget, $this->account_form_settings ),
			new Beaver_Builder_Integration( $this->widget, $this->account_form_settings ),
			new Memberpress_Integration( $this->widget, $this->account_form_settings ),
		);
	}

	/**
	 * @return Settings_Tab[]
	 */
	protected function get_standalone_settings_tabs(): array {
		return array(
			new General_Settings_Tab(),
			new Statistics_Settings_Tab(),
			new Compatible_Plugins_Tab(),
		);
	}
}
