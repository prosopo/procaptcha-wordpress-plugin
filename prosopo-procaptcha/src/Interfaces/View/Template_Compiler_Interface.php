<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\View;

interface Template_Compiler_Interface {
	public function compile( string $template, string $escape_callback_name ): string;
}
