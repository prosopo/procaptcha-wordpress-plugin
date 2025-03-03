<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

class Frontend_Assets {
	public function is_script_tag_with_module_attribute( string $javascript_tag ): bool {
		return false !== strpos( $javascript_tag, 'type="module"' ) ||
			false !== strpos( $javascript_tag, "type='module'" );
	}

	public function add_module_script_tag_attribute( string $javascript_tag ): string {
		// for old WP versions.
		$javascript_tag = str_replace( ' type="text/javascript"', '', $javascript_tag );

		return str_replace( 'src', 'type="module" src', $javascript_tag );
	}
}
