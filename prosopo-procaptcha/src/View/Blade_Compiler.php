<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\View;

use Io\Prosopo\Procaptcha\Interfaces\View\Template_Compiler_Interface;

class Blade_Compiler implements Template_Compiler_Interface {
	public function compile( string $template, string $escape_callback_name ): string {
		$template = $this->remove_comments( $template );

		$template = $this->replace_opening_echo( $template, $escape_callback_name );
		$template = $this->replace_closing_echo( $template );

		$template = $this->replace_opening_tag_with_brackets( 'for', $template );
		$template = $this->replace_opening_tag_with_brackets( 'foreach', $template );
		$template = $this->replace_closing_loops( $template );

		$template = $this->replace_opening_tag_with_brackets( 'if', $template );
		$template = $this->replace_opening_tag_with_brackets( 'elseif', $template );
		$template = $this->replace_closing_if( $template );
		$template = $this->replace_else( $template );

		$template = $this->replace_opening_php( $template );
		$template = $this->replace_closing_php( $template );

		$template = $this->replace_use_directive( $template );
		$template = $this->replace_selected_directive( $template );
		$template = $this->replace_checked_directive( $template );
		$template = $this->replace_class_directive( $template, $escape_callback_name );
		$template = $this->replace_switch_directives( $template );

		return $template;
	}

	// Removes all parts like: "{{--Comment--}}".
	protected function remove_comments( string $template ): string {
		return (string) preg_replace( '/{{--[\s\S]*?--}}/', '', $template );
	}

	protected function replace_opening_echo( string $template, string $escape_callback_name ): string {
		$template = str_replace( '{{', sprintf( '<?php echo $%s(', $escape_callback_name ), $template );

		return str_replace( '{!!', '<?php echo(', $template );
	}

	protected function replace_closing_echo( string $template ): string {
		$template = str_replace( '}}', '); ?>', $template );

		return str_replace( '!!}', '); ?>', $template );
	}

	protected function get_regex_for_tag_with_brackets( string $tag ): string {
		// Without 's' flag, .* means everything, but not the new line.
		// It's necessary to separate the new line,
		// otherwise we'll have big troubles with nested things, like "for(test()->new()) {{ new() }} @endforeach".
		return sprintf( '/@%s\s*\((.*)\)/', preg_quote( $tag, '/' ) );
	}

	protected function replace_opening_tag_with_brackets( string $tag, string $template ): string {
		$regex       = $this->get_regex_for_tag_with_brackets( $tag );
		$replacement = sprintf( '<?php %s( $1 ): ?>', $tag );

		return (string) preg_replace( $regex, $replacement, $template );
	}

	protected function replace_closing_loops( string $template ): string {
		// It's important to put 'endforeach' first, because 'endfor' is a part of 'endforeach'.
		$template = str_replace( '@endforeach', '<?php endforeach; ?>', $template );

		return str_replace( '@endfor', '<?php endfor; ?>', $template );
	}

	protected function replace_closing_if( string $template ): string {
		return str_replace( '@endif', '<?php endif; ?>', $template );
	}

	protected function replace_else( string $template ): string {
		return str_replace( '@else', '<?php else: ?>', $template );
	}

	protected function replace_opening_php( string $template ): string {
		return str_replace( '@php', '<?php', $template );
	}

	protected function replace_closing_php( string $template ): string {
		return str_replace( '@endphp', '?>', $template );
	}

	protected function replace_use_directive( string $template ): string {
		return (string) preg_replace( '/@use\s*\((["\'])(.*?)\1\)/s', '<?php use $2; ?>', $template );
	}

	protected function replace_selected_directive( string $template ): string {
		$regex = $this->get_regex_for_tag_with_brackets( 'selected' );

		$replacement = '<?php if ( $1 ) echo "selected=\"\""; ?>';

		return (string) preg_replace( $regex, $replacement, $template );
	}

	protected function replace_checked_directive( string $template ): string {
		$regex = $this->get_regex_for_tag_with_brackets( 'checked' );

		$replacement = '<?php if ( $1 ) echo "checked=\"\""; ?>';

		return (string) preg_replace( $regex, $replacement, $template );
	}

	protected function replace_switch_directives( string $template ): string {
		$template = $this->replace_opening_tag_with_brackets( 'case', $template );

		// 1. remove space between @switch and the first "case", otherwise it'll case an error (spaces are threat as unexpected HTML).
		$regex    = '/@switch\s*\((.*)\)\s*<\?php/';
		$template = (string) preg_replace( $regex, '<?php switch($1): ?><?php', $template );

		$template = str_replace( '@break', '<?php break; ?>', $template );
		$template = str_replace( '@default', '<?php default: ?>', $template );
		$template = str_replace( '@endswitch', '<?php endswitch; ?>', $template );

		return $template;
	}

	// "@class(['name', 'name2' => $condition])" to 'class="name name2"'.
	protected function replace_class_directive( string $template, string $escape_callback_name ): string {
		$regex       = '/@class\s*\((\[.*])\)/s';
		$replacement = $this->get_php_for_condition_classes( $escape_callback_name );

		return (string) preg_replace( $regex, $replacement, $template );
	}

	protected function get_php_for_condition_classes( string $escape_callback_name ): string {
		$php_code = array();

		$php_code[] = '<?php echo "class=\"";';
		$php_code[] = 'ob_start();';
		$php_code[] = 'foreach ( $1 as \$key => \$value ) {';
		$php_code[] = sprintf( 'if ( true === is_int( \$key ) ) { echo $%s(\$value) . " "; }', $escape_callback_name );
		$php_code[] = sprintf( 'else { if ( true === \$value ) { echo $%s(\$key) . " "; } }', $escape_callback_name );
		$php_code[] = '}';// foreach.
		$php_code[] = 'echo trim( (string)ob_get_clean() );';
		$php_code[] = 'echo "\""; ?>';

		return implode( "\n", $php_code );
	}
}
