<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\View;

interface Template_Provider_Interface {
	public function get_template( View_Interface $view ): string;
}
