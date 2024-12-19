<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Assets_Manager_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Captcha\Captcha_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Hooks_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Tab_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Factory_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Renderer_Interface;
use Io\Prosopo\Procaptcha\Plugin;
use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Views\Settings\Settings;
use function Io\Prosopo\Procaptcha\make_collection;

class Settings_Page implements Hooks_Interface {
	const FORM_NONCE  = 'prosopo-captcha__settings';
	const TAB_NAME    = 'tab';
	const MENU_SLUG   = 'prosopo-procaptcha';
	const DEFAULT_TAB = 'general';

	private Plugin $plugin;
	private Settings_Storage $settings_storage;
	private Captcha_Interface $captcha;
	private Query_Arguments $query_arguments;
	private View_Factory_Interface $component_creator;
	private View_Renderer_Interface $renderer;
	private Assets_Manager_Interface $assets_manager;
	/**
	 * @var array<string,Settings_Tab_Interface>
	 */
	private array $setting_tabs;

	public function __construct(
		Plugin $plugin,
		Settings_Storage $settings_storage,
		Captcha_Interface $captcha,
		Query_Arguments $query_arguments,
		View_Factory_Interface $component_creator,
		View_Renderer_Interface $component_renderer,
		Assets_Manager_Interface $assets_manager
	) {
		$this->plugin            = $plugin;
		$this->settings_storage  = $settings_storage;
		$this->captcha           = $captcha;
		$this->query_arguments   = $query_arguments;
		$this->component_creator = $component_creator;
		$this->renderer          = $component_renderer;
		$this->assets_manager    = $assets_manager;
		$this->setting_tabs      = array();
	}

	public function set_hooks( bool $is_admin_area ): void {
		if ( false === $is_admin_area ) {
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

	public function make_component(): ?View_Interface {
		$current_tab   = $this->maybe_process_form();
		$is_just_saved = '' !== $current_tab;

		if ( '' === $current_tab ) {
			$current_tab = $this->query_arguments->get_string_for_non_action(
				self::TAB_NAME,
				Query_Arguments::GET
			);
		}

		if ( '' === $current_tab ) {
			$current_tab = self::DEFAULT_TAB;
		}

		if ( false === key_exists( $current_tab, $this->setting_tabs ) ) {
			return null;
		}

		$tabs = array();
		foreach ( $this->setting_tabs as $settings_tab ) {
			$tabs[] = make_collection(
				array(
					'is_active' => $settings_tab->get_tab_name() === $current_tab,
					'title'     => $settings_tab->get_tab_title(),
					'url'       => $this->get_tab_url( $settings_tab->get_tab_name() ),
				)
			);
		}

		$tab = $this->setting_tabs[ $current_tab ];

		return $this->component_creator->make_view(
			Settings::class,
			function ( Settings $settings ) use ( $is_just_saved, $tabs, $tab, $current_tab ) {
				$js_file  = $tab->get_tab_js_file();
				$css_file = $tab->get_tab_css_file();

				// Manually, instead of WP assets, because the settings page is a WebComponenet with Shadow DOM,
				// and we need to inject assets directly.
				$settings->css  = $this->assets_manager->get_asset_content( 'settings.min.css' );
				$settings->css .= '' !== $css_file ?
					$this->assets_manager->get_asset_content( $css_file ) :
					'';

				$settings->js_file = '' !== $js_file ?
					$this->assets_manager->get_asset_url( $js_file ) :
					'';

				$settings->js_data       = $tab->get_tab_js_data( $this->settings_storage );
				$settings->is_just_saved = $is_just_saved;
				$settings->tabs          = $tabs;
				$settings->current_tab   = $current_tab;
				$settings->tab_content   = $tab->make_tab_component( $this->component_creator, $this->captcha );
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
					$this->renderer->render_view( $component, null, true );
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
	 * @param array<class-string<Settings_Tab_Interface>> $classes
	 */
	public function add_setting_tabs( array $classes ): void {
		foreach ( $classes as $tab_class ) {
			$settings_tab = $this->settings_storage->get( $tab_class );

			$this->setting_tabs[ $settings_tab->get_tab_name() ] = $settings_tab;
		}
	}

	protected function maybe_process_form(): string {
		if ( false === current_user_can( 'manage_options' ) ) {
			return '';
		}

		$tab_name = $this->get_active_tab( Query_Arguments::POST );

		if ( '' === $tab_name ||
				false === key_exists( $tab_name, $this->setting_tabs ) ) {
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
