<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\View;

use Closure;
use Io\Prosopo\Procaptcha\Interfaces\View\Object_Property_Manager_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Factory_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Renderer_Interface;

class View_Renderer implements View_Renderer_Interface {
	private Template_Renderer $template_renderer;
	private View_Factory_Interface $view_factory;
	private Object_Property_Manager_Interface $object_property_manager;

	public function __construct(
		Template_Renderer $template_renderer,
		View_Factory_Interface $view_factory,
		Object_Property_Manager_Interface $object_property_manager
	) {
		$this->template_renderer       = $template_renderer;
		$this->view_factory            = $view_factory;
		$this->object_property_manager = $object_property_manager;
	}

	public function render_view( $view_or_class, ?Closure $setup_callback = null, bool $do_print = false ): string {
		$component = true === is_string( $view_or_class ) ?
			$this->view_factory->make_view( $view_or_class, $setup_callback ) :
			$view_or_class;

		$template  = $component->get_template();
		$variables = $this->object_property_manager->get_variables( $component );

		$variables = $this->render_nested_views( $variables );

		return $this->template_renderer->render_template( $template, $variables, $do_print );
	}

	/**
	 * @param array<string,mixed> $variables
	 *
	 * @return array<string,mixed>
	 */
	protected function render_nested_views( array $variables ): array {
		return array_map(
			function ( $item ) {
				return $this->render_if_view( $item );
			},
			$variables
		);
	}

	/**
	 * @param mixed $item
	 *
	 * @return mixed
	 */
	protected function render_if_view( $item ) {
		if ( true === ( $item instanceof View_Interface ) ) {
			$item = $this->render_view( $item );
		} elseif ( true === is_array( $item ) &&
					false === is_callable( $item ) ) {
			$item = $this->render_nested_views( $item );
		}

		return $item;
	}
}
