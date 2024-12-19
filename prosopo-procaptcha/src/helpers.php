<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

/**
 * @param array<string|int, mixed> $items
 */
function make_collection( array $items ): Collection {
	return new Collection( $items );
}
