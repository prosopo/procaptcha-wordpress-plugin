<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Active_Integrations;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\Module_Integration;
use Io\Prosopo\Procaptcha\Integrations\Integrations_Loader;
use Io\Prosopo\Procaptcha\Settings\Tab\Procaptcha_Settings_Tab;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class Active_Integrations_Tab extends Procaptcha_Settings_Tab {
	private Integrations_Loader $integrations_loader;

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
			function ( Active_Integrations_List $compatible_list ) {
				$loaded_integrations = $this->integrations_loader->get_loaded_integrations();

				$compatible_list->active_integrations = array_map(
					fn( Module_Integration $integration ) => $integration->get_about_integration(),
					$loaded_integrations
				);
			}
		);
	}

	protected function get_option_name(): string {
		return '';
	}
}
