<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\View;

interface Object_Property_Manager_Interface {
	public function set_default_values( object $instance ): void;

	/**
	 * @return array<string,mixed> name => value (or callback)
	 */
	public function get_variables( object $instance ): array;
}
