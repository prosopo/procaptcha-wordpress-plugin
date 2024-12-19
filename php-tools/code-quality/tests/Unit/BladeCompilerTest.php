<?php

namespace Unit;

use Io\Prosopo\Procaptcha\Interfaces\View\Template_Compiler_Interface;
use Io\Prosopo\Procaptcha\View\Blade_Compiler;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BladeCompilerTest extends TestCase {
	public static function getTemplatesHelper(): Templates_Helper {
		return new Templates_Helper();
	}

	public static function templateNamesProvider(): array {
		$templatesHelper = self::getTemplatesHelper();
		$templates       = $templatesHelper->getTemplatesByExtension( '.blade.php' );

		$testArguments = array_map(
			fn( string $template ) => array( $template, 'e' ),
			$templates
		);

		$testNames = array_map(
			function ( string $template ) {
				$fileName     = pathinfo( $template, PATHINFO_FILENAME );
				$pureFileName = str_replace( '.blade', '', $fileName );

				$shortDirName = basename( dirname( $template ) );

				return $shortDirName . '/' . $pureFileName;
			},
			$templates
		);

		return array_combine( $testNames, $testArguments );
	}

	#[DataProvider( 'templateNamesProvider' )]
	public function testTemplateCompilation( string $template, string $escape_callback_name ): void {
		$compiler = $this->getCompiler();

		$templatesHelper = self::getTemplatesHelper();
		$phpTemplate     = $templatesHelper->getTemplate( $template . '.php' );
		$bladeTemplate   = $templatesHelper->getTemplate( $template . '.blade.php' );

		$compiledPhp = $compiler->compile( $bladeTemplate, $escape_callback_name );

		$this->assertEquals( $phpTemplate, $compiledPhp, 'Failed to compile template: ' . pathinfo( $template, PATHINFO_FILENAME ) );
	}

	protected function getCompiler(): Template_Compiler_Interface {
		return new Blade_Compiler();
	}
}
