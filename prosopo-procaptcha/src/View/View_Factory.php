<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\View;

use Closure;
use Io\Prosopo\Procaptcha\Interfaces\View\Object_Property_Manager_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\Template_Provider_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Factory_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Interface;

class View_Factory implements View_Factory_Interface {
	private Object_Property_Manager_Interface $object_property_manager;
	private Template_Provider_Interface $template_provider;

	public function __construct( Object_Property_Manager_Interface $object_property_manager, Template_Provider_Interface $template_provider ) {
		$this->object_property_manager = $object_property_manager;
		$this->template_provider       = $template_provider;
	}

	public function make_view( string $view_class, ?Closure $setup_callback = null ): View_Interface {
		// todo add optional container support.
		$view_instance = new $view_class( $this->template_provider );

		$this->object_property_manager->set_default_values( $view_instance );

		if ( null !== $setup_callback ) {
			$setup_callback( $view_instance );
		}

		return $view_instance;
	}
}
