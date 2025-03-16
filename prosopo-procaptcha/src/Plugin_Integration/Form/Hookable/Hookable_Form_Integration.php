<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Form_Integration;

defined( 'ABSPATH' ) || exit;

interface Hookable_Form_Integration extends Form_Integration, Hookable {
	public static function make_instance(): self;
}
