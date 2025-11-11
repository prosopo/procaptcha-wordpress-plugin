<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Utils;

use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\boolExtended;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\int;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

defined( 'ABSPATH' ) || exit;

final class Query_Arguments {
	const GET    = 'get';
	const POST   = 'post';
	const SERVER = 'server';

	public static function get_non_action_string( string $arg_name, string $from = self::GET ): string {
		$source = self::get_source( $from );

		return self::sanitize_string( string( $source, $arg_name ) );
	}

	public static function get_non_action_int( string $arg_name, string $from = self::GET ): int {
		$source = self::get_source( $from );

		return int( $source, $arg_name );
	}

	public static function get_non_action_bool( string $arg_name, string $from = self::GET ): bool {
		$source = self::get_source( $from );

		return boolExtended( $source, $arg_name );
	}

	public static function get_admin_action_string(
		string $arg_name,
		string $nonce_action_name,
		string $from = self::GET
	): string {
		$source = self::get_source( $from );

		// separately check for presence, otherwise check_admin_referer will fail the request.
		if ( ! key_exists( $arg_name, $source ) ||
			false === check_admin_referer( $nonce_action_name ) ) {
			return '';
		}

		return self::get_non_action_string( $arg_name, $from );
	}

	public static function get_admin_action_bool(
		string $arg_name,
		string $nonce_action_name,
		string $from = 'get'
	): bool {
		$source = self::get_source( $from );

		// separately check for presence, otherwise check_admin_referer will fail the request.
		if ( ! key_exists( $arg_name, $source ) ||
			false === check_admin_referer( $nonce_action_name ) ) {
			return false;
		}

		return self::get_non_action_bool( $arg_name, $from );
	}

	/**
	 * @return array<string,mixed>
	 */
	protected static function get_source( string $from ): array {
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

	protected static function sanitize_string( string $value ): string {
		$value = wp_unslash( $value );
		$value = sanitize_text_field( $value );

		return trim( $value );
	}
}
