<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\View;

use Io\Prosopo\Procaptcha\Interfaces\View\Template_Provider_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Interface;

class View implements View_Interface {
	private Template_Provider_Interface $template_provider;

	public function __construct( Template_Provider_Interface $template_provider ) {
		$this->template_provider = $template_provider;

		$this->set_custom_defaults();
	}

	public function get_template(): string {
		return $this->template_provider->get_template( $this );
	}

	protected function set_custom_defaults(): void {
	}
}
