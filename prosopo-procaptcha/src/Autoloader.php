<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

class Autoloader {
	private string $namespace;
	private string $root_dir;

	public function __construct( string $name_space, string $root_dir ) {
		$this->namespace = $name_space;
		$this->root_dir  = $root_dir;

		spl_autoload_register( array( $this, 'maybe_load_class' ) );
	}

	public function maybe_load_class( string $class_name ): void {
		if ( 0 !== strpos( $class_name, $this->namespace ) ) {
			return;
		}

		$relative_path = substr( $class_name, strlen( $this->namespace ) );
		$relative_path = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_path );
		$full_path     = $this->root_dir . $relative_path . '.php';

		if ( false === file_exists( $full_path ) ) {
			return;
		}

		require_once $full_path;
	}
}
