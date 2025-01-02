<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Definition\Integration\Form;

use Io\Prosopo\Procaptcha\Definition\Hookable;

defined( 'ABSPATH' ) || exit;

interface Hookable_Form_Integration extends Form_Integration, Hookable {
	public static function make_instance(): self;
}
