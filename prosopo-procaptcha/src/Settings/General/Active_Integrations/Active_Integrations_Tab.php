<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Settings\General\Active_Integrations;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Module\Module_Integration;
use Io\Prosopo\Procaptcha\Integrations\Integrations_Loader;
use Io\Prosopo\Procaptcha\Procaptcha_Plugin;
use Io\Prosopo\Procaptcha\Settings\General\Active_Integrations\Active_Integrations_List;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab_Base;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class Active_Integrations_Tab extends Settings_Tab_Base {
	private Integrations_Loader $integrations_loader;

	/**
	 * @param Module_Integration[] $integrations
	 *
	 * @return About_Module_Integration[]
	 */
	protected static function get_about_integrations( array $integrations ): array {
		$about_integrations = array_map(
			fn( Module_Integration $integration ) => $integration->get_about_integration(),
			$integrations
		);

		usort(
			$about_integrations,
			fn( About_Module_Integration $first, About_Module_Integration $second ) =>
			strcasecmp( $first->name, $second->name )
		);

		return $about_integrations;
	}

	public function __construct( Integrations_Loader $integrations_loader ) {
		parent::__construct();

		$this->integrations_loader = $integrations_loader;
	}

	public function get_tab_title(): string {
		return __( 'Active Integrations', 'prosopo-procaptcha' );
	}

	public function get_tab_name(): string {
		return 'active-integrations';
	}

	public function make_tab_component( ModelFactoryInterface $factory, Widget $widget ): TemplateModelInterface {
		return $factory->createModel(
			Active_Integrations_List::class,
			fn ( Active_Integrations_List $compatible_list ) => $this->load_integrations_list( $compatible_list )
		);
	}

	protected function get_option_name(): string {
		return '';
	}

	protected function load_integrations_list( Active_Integrations_List $integrations_list ): void {
		$integrations_list->label = __( 'Active Integrations', 'prosopo-procaptcha' );

		$integrations_list->description = __(
			'The Procaptcha plugin automatically detects compatible plugins on your site and enables their integrations. Click any item below to open a corresponding Docs page.',
			'prosopo-procaptcha'
		);

		$integrations_list->details = __( 'Note: Inactive plugins, even if compatible, are excluded.', 'prosopo-procaptcha' );

		$opening_link                   = sprintf( '<a href="%s" target="_blank" style="text-decoration: underline;">', Procaptcha_Plugin::SUPPORT_FORUM_URL );
		$integrations_list->request_new = sprintf(
		// translators: %1$s: opening link tag, %2$s: closing link tag.
			esc_html__( 'Your favourite plugin is not listed? %1$s Start a thread %2$s in our support forum and we will consider adding the new integration.', 'prosopo-procaptcha' ),
			$opening_link,
			'</a>'
		);

		$active_integrations                    = $this->integrations_loader->get_loaded_integrations();
		$integrations_list->active_integrations = self::get_about_integrations( $active_integrations );
	}
}
