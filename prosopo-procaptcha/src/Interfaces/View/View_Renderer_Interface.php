<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\View;

use Closure;

interface View_Renderer_Interface {
	/**
	 * @template T of View_Interface
	 *
	 * @param View_Interface|class-string<T> $view_or_class
	 * @param Closure(T):void|null $setup_callback
	 */
	public function render_view( $view_or_class, Closure $setup_callback = null, bool $do_print = false ): string;
}
