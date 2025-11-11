<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

use Io\Prosopo\Procaptcha\Utils\Html_Attributes_Collection;

defined( 'ABSPATH' ) || exit;

/**
 * @param array<string|int, mixed> $items
 */
function html_attrs_collection( array $items ): Html_Attributes_Collection {
	return new Html_Attributes_Collection( $items );
}
