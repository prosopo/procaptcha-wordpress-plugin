<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Form;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Definition\Integration\Form\Hookable_Form_Integration;

abstract class Hookable_Form_Integration_Base implements Hookable_Form_Integration {
	use Form_Integration_Helpers_Container;

	public static function make_instance(): Hookable_Form_Integration_Base {
		return new static();
	}

	final public function __construct() {
	}
}
