<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Module;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;

interface Module_Integration extends Hookable {
	public function get_about_integration(): About_Module_Integration;
}
