<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Form_Integration\Hookable;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Form_Integration\Helper\Form_Integration_Helper_Container;

abstract class Hookable_Form_Integration_Base implements Hookable_Form_Integration {
	use Form_Integration_Helper_Container;

	public static function make_instance(): Hookable_Form_Integration_Base {
		return new static();
	}

	final public function __construct() {
		$this->construct();
	}

	public function construct(): void {
	}
}
