<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Assets\Assets_Loader;
use Io\Prosopo\Procaptcha\Assets\Assets_Resolver;
use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Procaptcha_Plugin;
use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Settings\General\Settings;
use Io\Prosopo\Procaptcha\Settings\Storage\Procaptcha_Settings_Storage;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class Settings_Page implements Hookable {

	const FORM_NONCE  = 'prosopo-captcha__settings';
	const TAB_NAME    = 'tab';
	const MENU_SLUG   = 'prosopo-procaptcha';
	const DEFAULT_TAB = 'general';

	private Procaptcha_Plugin $plugin;
	private Procaptcha_Settings_Storage $settings_storage;
	private Widget $widget;
	private Query_Arguments $query_arguments;
	private ModelFactoryInterface $component_creator;
	private ModelRendererInterface $renderer;
	private Assets_Resolver $assets_resolver;
	private Assets_Loader $assets_loader;
	/**
	 * @var array<string,Settings_Tab>
	 */
	private array $setting_tabs;

	public function __construct(
		Procaptcha_Plugin $plugin,
		Procaptcha_Settings_Storage $settings_storage,
		Widget $widget,
		Query_Arguments $query_arguments,
		ModelFactoryInterface $component_creator,
		ModelRendererInterface $component_renderer,
		Assets_Resolver $assets_resolver,
		Assets_Loader $assets_loader
	) {
		$this->plugin            = $plugin;
		$this->settings_storage  = $settings_storage;
		$this->widget            = $widget;
		$this->query_arguments   = $query_arguments;
		$this->component_creator = $component_creator;
		$this->renderer          = $component_renderer;
		$this->assets_resolver   = $assets_resolver;
		$this->assets_loader     = $assets_loader;
		$this->setting_tabs      = array();
	}

	public function set_hooks( bool $is_admin_area ): void {
		if ( ! $is_admin_area ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );

		add_filter(
			sprintf( 'plugin_action_links_%s', $this->plugin->get_basename() ),
			array( $this, 'add_settings_link_to_plugin_list' )
		);
	}

	public function get_active_tab( string $from = Query_Arguments::GET ): string {
		return $this->query_arguments->get_string_for_non_action(
			self::TAB_NAME,
			$from
		);
	}

	public function make_component(): ?TemplateModelInterface {
		$current_tab   = $this->maybe_process_form();
		$is_just_saved = '' !== $current_tab;

		if ( '' === $current_tab ) {
			$current_tab = $this->query_arguments->get_string_for_non_action( self::TAB_NAME );
		}

		if ( '' === $current_tab ) {
			$current_tab = self::DEFAULT_TAB;
		}

		if ( ! key_exists( $current_tab, $this->setting_tabs ) ) {
			return null;
		}

		$tabs = array();
		foreach ( $this->setting_tabs as $settings_tab ) {
			$tabs[] = array(
				'is_active' => $settings_tab->get_tab_name() === $current_tab,
				'title'     => $settings_tab->get_tab_title(),
				'url'       => $this->get_tab_url( $settings_tab->get_tab_name() ),
			);
		}

		$tab = $this->setting_tabs[ $current_tab ];

		$this->load_tab_script_asset( $tab );

		return $this->component_creator->createModel(
			Settings::class,
			function ( Settings $settings ) use ( $is_just_saved, $tabs, $tab, $current_tab ) {
				$tab_style_asset        = $tab->get_style_asset();
				$is_tab_style_asset_set = '' !== $tab_style_asset;

				// Manually, instead of WP assets, because the settings page is a WebComponenet with Shadow DOM,
				// and we need to inject styles directly.

				$settings_style_asset = 'settings/general/general-settings-styles.min.css';

				$settings->style_asset_urls[] = $this->assets_resolver->resolve_asset_url( $settings_style_asset );
				$this->assets_loader->mark_asset_as_loaded( $settings_style_asset );

				if ( $is_tab_style_asset_set ) {
					$settings->style_asset_urls[] = $this->assets_resolver->resolve_asset_url( $tab_style_asset );
					$this->assets_loader->mark_asset_as_loaded( $tab_style_asset );
				}

				$settings->is_just_saved = $is_just_saved;
				$settings->tabs          = $tabs;
				$settings->current_tab   = $current_tab;
				$settings->tab_content   = $tab->make_tab_component( $this->component_creator, $this->widget );
			}
		);
	}

	public function register_settings_page(): void {
		add_options_page(
			__( 'Procaptcha Settings', 'prosopo-procaptcha' ),
			__( 'Procaptcha', 'prosopo-procaptcha' ),
			'manage_options',
			self::MENU_SLUG,
			function () {
				$component = $this->make_component();

				if ( null !== $component ) {
                    // @phpcs:ignore
                    echo $this->renderer->renderModel($component);
				}
			}
		);
	}

	/**
	 * @param string[] $links
	 *
	 * @return string[]
	 */
	public function add_settings_link_to_plugin_list( array $links ): array {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			$this->get_tab_url( self::DEFAULT_TAB ),
			esc_html__( 'Settings', 'prosopo-procaptcha' ),
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * @param array<class-string<Settings_Tab>> $classes
	 */
	public function add_setting_tabs( array $classes ): void {
		foreach ( $classes as $tab_class ) {
			$settings_tab = $this->settings_storage->get( $tab_class );

			$this->setting_tabs[ $settings_tab->get_tab_name() ] = $settings_tab;
		}
	}

	protected function load_tab_script_asset( Settings_Tab $tab ): void {
		$tab_script_asset  = $tab->get_tab_script_asset();
		$is_tab_script_set = '' !== $tab_script_asset;

		if ( $is_tab_script_set ) {
			$this->assets_loader->load_script_asset(
				$tab_script_asset,
				array(),
				'prosopoProcaptchaWpSettings',
				$tab->get_tab_js_data( $this->settings_storage )
			);
		}
	}

	protected function maybe_process_form(): string {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}

		$tab_name = $this->get_active_tab( Query_Arguments::POST );

		if ( '' === $tab_name ||
			! key_exists( $tab_name, $this->setting_tabs ) ) {
			return '';
		}

		$settings_tab = $this->setting_tabs[ $tab_name ];
		$settings_tab->process_form( $this->query_arguments );

		return $tab_name;
	}

	protected function get_tab_url( string $tab_name ): string {
		return admin_url( sprintf( 'options-general.php?page=%s&tab=%s', self::MENU_SLUG, $tab_name ) );
	}
}
