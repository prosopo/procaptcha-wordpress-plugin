<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces;

defined( 'ABSPATH' ) || exit;

interface Assets_Manager_Interface {
	public function get_asset_url( string $asset ): string;

	public function get_asset_content( string $asset ): string;

	public function get_assets_version(): string;

	/**
	 * @param string[] $dependency_assets
	 * @param array<string,mixed> $data
	 */
	public function enqueue_module_js_asset(
		string $asset,
		array $dependency_assets = array(),
		string $data_object_name = '',
		array $data = array()
	): void;
}
