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

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/../../../prosopo-procaptcha/prefixed-vendors/vendor/scoper-autoload.php';
