<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Utils\Assets;

defined( 'ABSPATH' ) || exit;

final class Assets_Resolver {
	private string $base_url;
	/**
	 * @var array<string,string> [ .min.js => .ts ]
	 */
	private array $url_extensions_map;

	public function __construct( string $base_url ) {
		$this->base_url           = $base_url;
		$this->url_extensions_map = array();
	}

	/**
	 * @param array<string, string> $url_extensions_map
	 */
	public function set_url_extensions_map( array $url_extensions_map ): self {
		$this->url_extensions_map = $url_extensions_map;

		return $this;
	}

	public function resolve_asset_url( string $relative_asset_path ): string {
		$asset_extension        = $this->get_file_extension( $relative_asset_path );
		$asset_extension_length = strlen( $asset_extension );
		$asset_name             = substr( $relative_asset_path, 0, -$asset_extension_length );

		$url_asset_extension = $this->url_extensions_map[ $asset_extension ] ?? $asset_extension;

		return sprintf( '%s/%s', $this->base_url, $asset_name . $url_asset_extension );
	}

	/**
	 * Unlike pathinfo(), supports '.min.css'.
	 */
	protected function get_file_extension( string $file_name ): string {
		$first_dot_position = strpos( $file_name, '.' );

		return is_int( $first_dot_position ) ?
			substr( $file_name, $first_dot_position ) :
			'';
	}
}
