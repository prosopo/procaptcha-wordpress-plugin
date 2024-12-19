<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\View;

interface Template_Renderer_Interface {
	/**
	 * @param array<string,mixed> $variables
	 */
	public function render_template( string $template, array $variables, bool $do_print = false ): string;
}
