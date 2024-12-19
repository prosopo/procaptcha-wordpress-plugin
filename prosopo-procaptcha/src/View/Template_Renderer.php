<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\View;

use Error;
use Exception;
use Io\Prosopo\Procaptcha\Interfaces\View\Template_Compiler_Interface;
use Io\Prosopo\Procaptcha\Interfaces\View\Template_Renderer_Interface;

class Template_Renderer implements Template_Renderer_Interface {
	private Template_Compiler_Interface $template_compiler;
	/**
	 * @var callable(string $message, int $line, string $php_template): void|null
	 */
	private $error_handler;
	/**
	 * @var callable(mixed $value): string
	 */
	private $escape_callback;
	private string $escape_callback_name;

	/**
	 * @param callable(mixed $value): string|null $escape_callback
	 * @param callable(string $message, int $line, string $php_template): void|null $error_handler
	 */
	public function __construct(
		Template_Compiler_Interface $template_engine,
		callable $error_handler = null,
		callable $escape_callback = null,
		string $escape_callback_name = 'escape'
	) {
		$this->template_compiler = $template_engine;

		$this->escape_callback = null === $escape_callback ?
			array( $this, 'escape' ) :
			$escape_callback;

		$this->escape_callback_name = $escape_callback_name;
		$this->error_handler        = $error_handler;
	}

	public function render_template( string $template, array $variables, bool $do_print = false ): string {
		$php_template = $this->template_compiler->compile( $template, $this->escape_callback_name );

		$variables[ $this->escape_callback_name ] = $this->escape_callback;

		// @phpcs:ignore
		extract( $variables );

		ob_start();

		try {
			// Catch all level-errors and turn into the generic error.
			// @phpcs:ignore
			set_error_handler(
				function ( $errno, $errstr ) {
					// @phpcs:ignore
					throw new Error( $errstr, $errno );
				}
			);

			// @phpcs:ignore
			eval( '?>' . $php_template );
		} catch ( Error $error ) {
			$this->dispatch_template_error( $error->getMessage(), $error->getLine(), $php_template );
		} catch ( Exception $error ) {
			// Separate catch handlers to handle all the error types, cause some errors do not inherit Error.
			$this->dispatch_template_error( $error->getMessage(), $error->getLine(), $php_template );
		} finally {
			restore_error_handler();
		}

		$html = (string) ob_get_clean();

		if ( true === $do_print ) {
			// @phpcs:ignore
			echo $html;
		}

		return $html;
	}

	/**
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function escape( $value ): string {
		$string_value = $this->cast_to_string( $value );

		return htmlspecialchars( $string_value, ENT_QUOTES, 'UTF-8', false );
	}

	/**
	 * @param mixed $value
	 */
	protected function cast_to_string( $value ): string {
		if ( true === is_string( $value ) ||
		true === is_numeric( $value ) ) {
			return (string) $value;
		}

		if ( true === is_object( $value ) &&
			method_exists( $value, '__toString' ) ) {
			return (string) $value;
		}

		return '';
	}

	protected function dispatch_template_error( string $message, int $line, string $php_template ): void {
		if ( null === $this->error_handler ) {
			return;
		}

		$handler = $this->error_handler;
		$handler( $message, $line, $php_template );
	}
}
