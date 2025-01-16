<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Widget;

defined( 'ABSPATH' ) || exit;

class Widget_Settings {
	public const IS_ERROR_ACTIVE              = 'is_error_visible';
	public const IS_DESIRED_ON_GUESTS         = 'is_with_stub_for_authorized_users';
	public const IS_RETURN_ONLY               = 'is_return';
	public const IS_WITHOUT_CLIENT_VALIDATION = 'is_without_client_validation';
	public const ELEMENT_ATTRIBUTES           = 'element_attributes';
	public const HIDDEN_INPUT_ATTRIBUTES      = 'hidden_input';
}
