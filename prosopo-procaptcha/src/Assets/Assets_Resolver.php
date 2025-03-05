<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Assets;

defined( 'ABSPATH' ) || exit;

final class Assets_Resolver {
	private string $base_url;
	/**
	 * @var array<string,string> [ ts => min.js ]
	 */
	private array $url_extensions_map;
	private ?string $version;

	/**
	 * @param array<string,string> $url_extensions_map
	 */
	public function __construct( string $base_url, array $url_extensions_map, ?string $version ) {
		$this->base_url           = $base_url;
		$this->url_extensions_map = $url_extensions_map;
		$this->version            = $version;
	}

	public function resolve_asset_url( string $relative_asset_path ): string {
		$asset_extension     = pathinfo( $relative_asset_path, PATHINFO_EXTENSION );
		$asset_url_extension = $this->url_extensions_map[ $asset_extension ] ?? $asset_extension;

		$extension_with_dot_length = strlen( $asset_extension ) + 1;
		$asset_name                = substr( $relative_asset_path, 0, -$extension_with_dot_length );

		$asset_url = sprintf( '%s/%s.%s', $this->base_url, $asset_name, $asset_url_extension );

		return $this->add_version_to_url( $asset_url );
	}

	protected function add_version_to_url( string $url ): string {
		if ( $this->version ) {
			$url = add_query_arg( array( 'ver' => $this->version ), $url );
		}

		return $url;
	}
}
