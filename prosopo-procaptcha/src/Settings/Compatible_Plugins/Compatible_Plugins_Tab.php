<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Compatible_Plugins;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Settings\Tab\Procaptcha_Settings_Tab;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Widget\Widget;

final class Compatible_Plugins_Tab extends Procaptcha_Settings_Tab {
	public function get_tab_title(): string {
		return __( 'Compatible Plugins', 'prosopo-procaptcha' );
	}

	public function get_tab_name(): string {
		return 'compatible-plugins';
	}

	public function make_tab_component( ModelFactoryInterface $factory, Widget $widget ): TemplateModelInterface {
		return $factory->createModel(
			Compatible_Plugins_List::class,
			function ( Compatible_Plugins_List $compatible_list ) {
			}
		);
	}

	protected function get_option_name(): string {
		return '';
	}
}
