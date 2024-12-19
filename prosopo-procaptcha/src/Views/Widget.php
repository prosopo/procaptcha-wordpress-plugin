<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Views;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Collection;
use Io\Prosopo\Procaptcha\View\View;
use function Io\Prosopo\Procaptcha\make_collection;

class Widget extends View {
	public Collection $attributes;
	public Collection $hidden_input_attrs;
	public bool $is_stub;
	public bool $no_client_validation;
	public bool $is_error_visible;
	public string $error_message;

	public function get_error_visibility(): string {
		return true === $this->is_error_visible ?
			'visible' :
			'hidden';
	}

	protected function set_custom_defaults(): void {
		parent::set_custom_defaults();

		$this->attributes         = make_collection( array() );
		$this->hidden_input_attrs = make_collection( array() );
	}
}
