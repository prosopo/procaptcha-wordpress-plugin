<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Definition\Integration\Form;

use Io\Prosopo\Procaptcha\Definition\Captcha\Captcha;
use Io\Prosopo\Procaptcha\Query_Arguments;

defined( 'ABSPATH' ) || exit;

interface Form_Integration_Helpers {
	public function get_captcha(): Captcha;

	public function get_query_arguments(): Query_Arguments;
}
