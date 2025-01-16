<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Form\Helper;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Form\Helper\Form_Integration_Helper;
use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Widget\Widget;

class Procaptcha_Form_Integration_Helper implements Form_Integration_Helper {
	private Widget $widget;
	private Query_Arguments $query_arguments;

	public function __construct( Widget $widget, Query_Arguments $query_arguments ) {
		$this->widget          = $widget;
		$this->query_arguments = $query_arguments;
	}

	public function get_widget(): Widget {
		return $this->widget;
	}

	public function get_query_arguments(): Query_Arguments {
		return $this->query_arguments;
	}
}
