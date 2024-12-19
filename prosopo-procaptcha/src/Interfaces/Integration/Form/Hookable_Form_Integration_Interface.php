<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\Integration\Form;

use Io\Prosopo\Procaptcha\Interfaces\Hooks_Interface;

defined( 'ABSPATH' ) || exit;

interface Hookable_Form_Integration_Interface extends Form_Integration_Interface, Hooks_Interface {
	public static function make_instance(): self;
}
