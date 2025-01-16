<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Form\Helper;

use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Widget\Widget;

defined( 'ABSPATH' ) || exit;

interface Form_Integration_Helper {
	public function get_widget(): Widget;

	public function get_query_arguments(): Query_Arguments;
}
