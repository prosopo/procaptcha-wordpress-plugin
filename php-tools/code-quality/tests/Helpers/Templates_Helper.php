<?php

namespace Unit;

use _PHPStan_62c6a0a8b\Nette\Neon\Exception;

class Templates_Helper {
	/**
	 * @return string[]
	 */
	public function getTemplatesByExtension( string $extension ): array {
		$all       = $this->getFilesByExtension( __DIR__ . '/../../templates', $extension );
		$inline    = $this->getFilesByExtension( __DIR__ . '/../../templates/inline', $extension );
		$multiline = $this->getFilesByExtension( __DIR__ . '/../../templates/multiline', $extension );

		return array_merge( $all, $inline, $multiline );
	}

	public function getTemplate( string $file ): string {
		if ( false === file_exists( $file ) ) {
			throw new Exception( 'Template is not found: ' . $file );
		}

		return (string) file_get_contents( $file );
	}

	/**
	 * @return string[]
	 */
	protected function getFilesByExtension( string $directory, string $fileExtension ): array {
		if ( false === is_dir( $directory ) ||
			false === is_readable( $directory ) ) {
			return array();
		}

		$files = scandir( $directory );

		if ( false === $files ) {
			return array();
		}

		$fileNames = array_filter(
			$files,
			function ( string $file ) use ( $directory, $fileExtension ) {
				$filePath = $directory . DIRECTORY_SEPARATOR . $file;

				return true === is_file( $filePath ) &&
						substr( $file, -strlen( $fileExtension ) ) === $fileExtension;
			}
		);

		return array_map(
			fn( string $fileName ) => $directory . DIRECTORY_SEPARATOR . str_replace( $fileExtension, '', $fileName ),
			$fileNames
		);
	}
}
