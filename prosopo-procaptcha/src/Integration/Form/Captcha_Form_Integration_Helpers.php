<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Form;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Definition\Captcha\Captcha;
use Io\Prosopo\Procaptcha\Definition\Integration\Form\Form_Integration_Helpers;
use Io\Prosopo\Procaptcha\Query_Arguments;

class Captcha_Form_Integration_Helpers implements Form_Integration_Helpers {
	private Captcha $captcha;
	private Query_Arguments $query_arguments;

	public function __construct( Captcha $captcha, Query_Arguments $query_arguments ) {
		$this->captcha         = $captcha;
		$this->query_arguments = $query_arguments;
	}

	public function get_captcha(): Captcha {
		return $this->captcha;
	}

	public function get_query_arguments(): Query_Arguments {
		return $this->query_arguments;
	}
}
