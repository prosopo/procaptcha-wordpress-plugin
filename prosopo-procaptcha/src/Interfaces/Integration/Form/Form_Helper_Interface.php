<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\Integration\Form;

use Io\Prosopo\Procaptcha\Interfaces\Captcha\Captcha_Interface;
use Io\Prosopo\Procaptcha\Query_Arguments;

defined( 'ABSPATH' ) || exit;

interface Form_Helper_Interface {
	public function get_captcha(): Captcha_Interface;

	public function get_query_arguments(): Query_Arguments;
}
