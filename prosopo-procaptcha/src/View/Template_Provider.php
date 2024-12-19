<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\View;

use Io\Prosopo\Procaptcha\Interfaces\View\Template_Provider_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Interface;

class Template_Provider implements Template_Provider_Interface {
	private string $views_path;
	private string $root_namespace;
	private string $template_extension;

	public function __construct( string $views_path, string $root_namespace, string $template_extension = '.blade.php' ) {
		$this->views_path         = $views_path;
		$this->root_namespace     = $root_namespace;
		$this->template_extension = $template_extension;
	}

	public function get_template( View_Interface $view ): string {
		$view_name = $this->get_view_filename( get_class( $view ), $this->root_namespace );

		$path_to_view = $this->get_path_to_view( $view_name );

		return $this->get_file_content_safely( $path_to_view );
	}

	protected function get_file_content_safely( string $file ): string {
		if ( false === file_exists( $file ) ) {
			// todo log.
			return '';
		}

		// @phpcs:ignore
		return (string) file_get_contents( $file );
	}

	protected function get_path_to_view( string $view_name ): string {
		return $this->views_path . DIRECTORY_SEPARATOR . $view_name . $this->template_extension;
	}

	protected function get_view_filename( string $component_class, string $root_namespace ): string {
		$relative_namespace = str_replace( $root_namespace, '', $component_class );
		$relative_namespace = ltrim( $relative_namespace, '\\' );

		$short_class_name = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_namespace );

		return strtolower( $short_class_name );
	}
}
