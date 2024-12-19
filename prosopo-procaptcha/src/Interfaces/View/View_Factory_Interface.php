<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\View;

use Closure;

interface View_Factory_Interface {
	/**
	 * @template T of View_Interface
	 *
	 * @param class-string<T> $view_class
	 * @param Closure(T):void|null $setup_callback
	 */
	public function make_view( string $view_class, ?Closure $setup_callback = null ): View_Interface;
}
