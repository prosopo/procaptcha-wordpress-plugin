<?php

define( 'ABSPATH', 'pest' );

function esc_html( string $var ): string {
	// emulate WordPress esc_html.
	return htmlentities( $var, ENT_QUOTES );
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/../../../prosopo-procaptcha/prefixed-vendors/vendor/scoper-autoload.php';
