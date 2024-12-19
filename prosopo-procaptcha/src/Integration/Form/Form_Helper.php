<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Form;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Captcha\Captcha_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Integration\Form\Form_Helper_Interface;
use Io\Prosopo\Procaptcha\Query_Arguments;

class Form_Helper implements Form_Helper_Interface {
	private Captcha_Interface $captcha;
	private Query_Arguments $query_arguments;

	public function __construct( Captcha_Interface $captcha, Query_Arguments $query_arguments ) {
		$this->captcha         = $captcha;
		$this->query_arguments = $query_arguments;
	}

	public function get_captcha(): Captcha_Interface {
		return $this->captcha;
	}

	public function get_query_arguments(): Query_Arguments {
		return $this->query_arguments;
	}
}
