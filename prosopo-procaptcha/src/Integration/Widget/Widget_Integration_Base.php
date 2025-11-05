<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Widget;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Utils\Hookable;
use Io\Prosopo\Procaptcha\Widget\Widget;

abstract class Widget_Integration_Base implements Hookable {

	protected Widget $widget;

	public function __construct( Widget $widget ) {
		$this->widget = $widget;
	}
}
