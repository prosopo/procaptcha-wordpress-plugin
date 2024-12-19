<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Form;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Integration\Form\Hookable_Form_Integration_Interface;

abstract class Hookable_Form_Integration implements Hookable_Form_Integration_Interface {
	use Form_Integration;

	public static function make_instance(): Hookable_Form_Integration_Interface {
		return new static();
	}

	final public function __construct() {
	}
}
