<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Widget;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Widget\Widget;

abstract class Widget_Integration implements Hookable {

	protected Widget $widget;

	public function __construct( Widget $widget ) {
		$this->widget = $widget;
	}
}
