<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Form_Integration\Hookable;

use Io\Prosopo\Procaptcha\Form_Integration\Form_Integration;
use Io\Prosopo\Procaptcha\Hookable;

defined( 'ABSPATH' ) || exit;

interface Hookable_Form_Integration extends Form_Integration, Hookable {
	public static function make_instance(): self;
}
