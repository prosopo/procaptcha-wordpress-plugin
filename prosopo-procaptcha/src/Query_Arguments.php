<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\int;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

defined( 'ABSPATH' ) || exit;

class Query_Arguments {
	const GET    = 'get';
	const POST   = 'post';
	const SERVER = 'server';

	public function get_string_for_non_action( string $arg_name, string $from = self::GET ): string {
		$source = $this->get_source( $from );

		return $this->sanitize_string( string( $source, $arg_name ) );
	}

	public function get_int_for_non_action( string $arg_name, string $from = self::GET ): int {
		$source = $this->get_source( $from );

		return int( $source, $arg_name );
	}

	public function get_bool_for_non_action( string $arg_name, string $from = self::GET ): bool {
		$source = $this->get_source( $from );

		return bool( $source, $arg_name );
	}

	public function get_string_for_admin_action(
		string $arg_name,
		string $nonce_action_name,
		string $from = 'get'
	): string {
		$source = $this->get_source( $from );

		// separately check for presence, otherwise check_admin_referer will fail the request.
		if ( false === key_exists( $arg_name, $source ) ||
			false === check_admin_referer( $nonce_action_name ) ) {
			return '';
		}

		return $this->get_string_for_non_action( $arg_name, $from );
	}

	public function get_bool_for_admin_action(
		string $arg_name,
		string $nonce_action_name,
		string $from = 'get'
	): bool {
		$source = $this->get_source( $from );

		// separately check for presence, otherwise check_admin_referer will fail the request.
		if ( false === key_exists( $arg_name, $source ) ||
			false === check_admin_referer( $nonce_action_name ) ) {
			return false;
		}

		return $this->get_bool_for_non_action( $arg_name, $from );
	}

	/**
	 * @return array<string,mixed>
	 */
	protected function get_source( string $from ): array {
		switch ( $from ) {
			case self::GET:
					// phpcs:ignore WordPress.Security.NonceVerification
				return $_GET;
			case self::POST:
					// phpcs:ignore WordPress.Security.NonceVerification
				return $_POST;
			case self::SERVER:
					// phpcs:ignore WordPress.Security.NonceVerification
				return $_SERVER;
			default:
				return array();
		}
	}

	protected function sanitize_string( string $value ): string {
		$value = wp_unslash( $value );
		$value = sanitize_text_field( $value );

		return trim( $value );
	}
}
