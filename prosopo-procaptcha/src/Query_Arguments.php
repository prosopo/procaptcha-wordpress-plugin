<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

class Query_Arguments {
	const GET    = 'get';
	const POST   = 'post';
	const SERVER = 'server';

	private ?Collection $post;
	private ?Collection $get;
	private ?Collection $server;

	public function __construct() {
		$this->post   = null;
		$this->get    = null;
		$this->server = null;
	}

	public function get_string_for_non_action( string $arg_name, string $from = self::GET ): string {
		$source = $this->get_source( $from );

		return $this->sanitize_string( $source->get_string( $arg_name ) );
	}

	public function get_int_for_non_action( string $arg_name, string $from = self::GET ): int {
		$source = $this->get_source( $from );

		return $source->get_int( $arg_name );
	}

	public function get_bool_for_non_action( string $arg_name, string $from = self::GET ): bool {
		$source = $this->get_source( $from );

		return $source->get_bool( $arg_name );
	}

	public function get_string_for_admin_action(
		string $arg_name,
		string $nonce_action_name,
		string $from = 'get'
	): string {
		$source = $this->get_source( $from );

		// separately check for presence, otherwise check_admin_referer will fail the request.
		if ( false === $source->exists( $arg_name ) ||
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
		if ( false === $source->exists( $arg_name ) ||
			false === check_admin_referer( $nonce_action_name ) ) {
			return false;
		}

		return $this->get_bool_for_non_action( $arg_name, $from );
	}

	protected function get_source( string $from ): Collection {
		switch ( $from ) {
			case self::GET:
				if ( null === $this->get ) {
					// phpcs:ignore WordPress.Security.NonceVerification
					$this->get = make_collection( $_GET );
				}

				return $this->get;
			case self::POST:
				if ( null === $this->post ) {
					// phpcs:ignore WordPress.Security.NonceVerification
					$this->post = make_collection( $_POST );
				}

				return $this->post;
			case self::SERVER:
				if ( null === $this->server ) {
					// phpcs:ignore WordPress.Security.NonceVerification
					$this->server = make_collection( $_SERVER );
				}

				return $this->server;
			default:
				return make_collection( array() );
		}
	}

	protected function sanitize_string( string $value ): string {
		$value = wp_unslash( $value );
		$value = sanitize_text_field( $value );

		return trim( $value );
	}
}
