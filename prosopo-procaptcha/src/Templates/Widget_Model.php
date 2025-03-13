<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Templates;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Html_Attributes_Collection;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;
use function Io\Prosopo\Procaptcha\html_attrs_collection;

class Widget_Model extends BaseTemplateModel {
	public Html_Attributes_Collection $attributes;
	public Html_Attributes_Collection $hidden_input_attrs;
	public bool $is_stub;
	public bool $no_client_validation;
	public bool $is_error_visible;
	public string $error_message;

	public function get_error_visibility(): string {
		return $this->is_error_visible ?
			'visible' :
			'hidden';
	}

	protected function setCustomDefaults(): void {
		$this->attributes         = html_attrs_collection( array() );
		$this->hidden_input_attrs = html_attrs_collection( array() );
	}
}
