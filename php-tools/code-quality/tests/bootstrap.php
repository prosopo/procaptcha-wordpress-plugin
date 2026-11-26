<?php

define( 'ABSPATH', 'pest' );

function esc_html( string $var ): string {
	// emulate WordPress esc_html.
	return htmlentities( $var, ENT_QUOTES );
}

function wp_unslash( $value ) {
	return $value;
}

function sanitize_text_field( $value ) {
	return $value;
}

// Runs a callback set in $GLOBALS['test_filters'][ $hook_name ], if any.
function apply_filters( string $hook_name, $value, ...$args ) {
	$callback = $GLOBALS['test_filters'][ $hook_name ] ?? null;

	return null !== $callback ?
		$callback( $value, ...$args ) :
		$value;
}

function __( string $text, string $domain = 'default' ): string {
	return $text;
}

function check_admin_referer( $action ) {
	return 1;
}

function wp_enqueue_script( string $handle, string $src = '', array $deps = array(), $ver = false, $args = array() ): void {
}

// Records the data in $GLOBALS['test_localized_scripts'][ $object_name ].
function wp_localize_script( string $handle, string $object_name, array $l10n ): bool {
	$GLOBALS['test_localized_scripts'][ $object_name ] = $l10n;

	return true;
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/../../../prosopo-procaptcha/prefixed-vendors/vendor/scoper-autoload.php';
