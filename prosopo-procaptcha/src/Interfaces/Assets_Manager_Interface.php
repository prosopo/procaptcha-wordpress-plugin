<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces;

defined( 'ABSPATH' ) || exit;

interface Assets_Manager_Interface {
	public function get_asset_url( string $asset ): string;

	public function get_asset_content( string $asset ): string;

	public function get_assets_version(): string;
}
