<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Widget_Container;

abstract class Hookable_Form_Integration_Base implements Hookable_Form_Integration {
	use Widget_Container;

	public static function make_instance(): Hookable_Form_Integration_Base {
		return new static();
	}

	final public function __construct() {
		$this->construct();
	}

	public function construct(): void {
	}
}
