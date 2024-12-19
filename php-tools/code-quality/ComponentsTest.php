<?php

use Io\Prosopo\Procaptcha\Components\Common\Blade_Templates;
use Io\Prosopo\Procaptcha\Components\Common\Component;
use Io\Prosopo\Procaptcha\Components\Common\Component_Renderer;
use Mockery;
use Tests\TestCase;

class ComponentsTest extends TestCase {

	public function testMakeAutomaticallySetsDefaultValues(): void {
		$component_class = new class() extends Component {
			public string $string;
			public int $int;
			public bool $bool;
			public array $array;
		};

		$component = $this->getComponents()->make( get_class( $component_class ) );

		$this->assertEquals( '', $component->string );
		$this->assertEquals( 0, $component->int );
		$this->assertEquals( false, $component->bool );
		$this->assertEquals( array(), $component->array );
	}

	public function testRenderRemoveComments(): void {
		$this->assertEquals(
			'Some text',
			$this->render(
				'{{--Comment--}}Some text'
			)
		);
	}

	public function testRenderSupportsPrintBrackets(): void {
		$this->assertEquals(
			'Some text',
			$this->render(
				'Some {{ $variable }}',
				array(
					'variable' => 'text',
				)
			)
		);
	}

	public function testRenderEscapesOutputInBrackets(): void {
		$this->assertEquals(
			'Some te&quot;xt',
			$this->render(
				'Some {{ $variable }}',
				array(
					'variable' => 'te"xt',
				)
			)
		);
	}
	public function testRenderSupportsUnescapedPrintBrackets(): void {
		$this->assertEquals(
			'Some text',
			$this->render(
				'Some {!! $variable !!}',
				array(
					'variable' => 'text',
				)
			)
		);
	}

	public function testRenderNotEscapeOutputInBrackets(): void {
		$this->assertEquals(
			'Some te"xt',
			$this->render(
				'Some {!! $variable !!}',
				array(
					'variable' => 'te"xt',
				)
			)
		);
	}

	public function testRenderSupportsFor(): void {
		$this->assertEquals(
			'Hi!Hi!',
			$this->render(
				"@for (\$i = 0; \$i < 2; \$i++)\nHi!@endfor"
			)
		);
	}

	public function testRenderSupportsForeach(): void {
		$this->assertEquals(
			'Hi!Hi!',
			$this->render(
				"@foreach (\$items as \$item)\n{{ \$item }}@endforeach",
				array(
					'items' => array( 'Hi!', 'Hi!' ),
				)
			)
		);
	}

	public function testRenderSupportsIf(): void {
		$this->assertEquals(
			'Hi',
			$this->render(
				"@if (\$variable)\nHi@endif",
				array(
					'variable' => true,
				)
			)
		);
	}

	public function testRenderSupportsElse(): void {
		$this->assertEquals(
			' Second ',
			$this->render(
				'@if ($variable)
                First @else Second @endif',
				array(
					'variable' => false,
				)
			)
		);
	}

	public function testRenderSupportsPhp(): void {
		$this->assertEquals(
			'Hi!',
			$this->render(
				'@php echo "Hi!"; @endphp'
			)
		);
	}

	// @selected.

	public function testRenderSupportsSelectedDirectiveWithTrue(): void {
		$this->assertEquals(
			'selected=""',
			$this->render(
				'@selected($variable)',
				array(
					'variable' => true,
				)
			)
		);
	}

	public function testRenderSupportsSelectedDirectiveWithFalse(): void {
		$this->assertEquals(
			'',
			$this->render(
				'@selected($variable)',
				array(
					'variable' => false,
				)
			)
		);
	}

	public function testRenderSupportsSelectedDirectiveWithMethod(): void {
		$component = new class() extends Component{
			public function selected(): bool {
				return true;
			}
		};

		$this->assertEquals(
			'selected=""',
			$this->render(
				'@selected($selected())',
				array(),
				$component
			)
		);
	}

	// @checked.

	public function testRenderSupportsCheckedDirectiveWithTrue(): void {
		$this->assertEquals(
			'checked=""',
			$this->render(
				'@checked($variable)',
				array(
					'variable' => true,
				)
			)
		);
	}

	public function testRenderSupportsCheckedDirectiveWithFalse(): void {
		$this->assertEquals(
			'',
			$this->render(
				'@checked($variable)',
				array(
					'variable' => false,
				)
			)
		);
	}

	public function testRenderSupportsCheckedDirectiveWithMethod(): void {
		$component = new class() extends Component{
			public function checked(): bool {
				return true;
			}
		};

		$this->assertEquals(
			'checked=""',
			$this->render(
				'@checked($checked())',
				array(),
				$component
			)
		);
	}

	// methods.

	public function testRenderSupportsComponentMethods(): void {
		$component = new class() extends Component{
			public function print(): string {
				return 'hello';
			}
		};

		$this->assertEquals(
			'hello',
			$this->render( '{{ $print() }}', array(), $component )
		);
	}

	public function testRenderSupportsComponentMethodsWithinIf(): void {
		$component = new class() extends Component{
			public function checked(): bool {
				return true;
			}
		};

		$this->assertEquals(
			'ok',
			$this->render(
				"@if(\$checked())\nok@endif",
				array(),
				$component
			)
		);
	}

	// @switch.

	public function testRenderSupportsSwitch(): void {
		$this->assertEquals(
			"First\n",
			$this->render(
				"@switch(\$variable)\n@case(1)\nFirst\n@break\n@case(2)\nSecond\n@break\n@endswitch",
				array(
					'variable' => 1,
				)
			)
		);
	}

	public function testRenderSupportsSwitchWithSpaceBeforeFirstCase(): void {
		$this->assertEquals(
			"First\n",
			$this->render(
				"@switch(\$variable)\n @case(1)\nFirst\n@break\n@case(2)\nSecond\n@break\n@endswitch",
				array(
					'variable' => 1,
				)
			)
		);
	}

	// @class.

	public function testRenderSupportsClassDirectiveWithTrue(): void {
		$this->assertEquals(
			'class="first third last"',
			$this->render(
				'@class(["first"=>true,"second"=>false,"third"=>true, "last"])'
			)
		);
	}

	// Errors;

	public function testRenderDoesNotThrowErrorIfVariableIsMissing(): void {
		$this->assertEquals(
			'',
			$this->render(
				'{{ $variable }}'
			)
		);
	}

	public function testRenderDoesNotThrowErrorIfVariableHasWrongCharacters(): void {
		$this->assertEquals(
			'',
			$this->render(
				'{{ $41 }}'
			)
		);
	}

	public function testRenderDoesNotThrowErrorIfWrongFunctionIsCalled(): void {
		$this->assertEquals(
			'',
			$this->render(
				'{{ some_function() }}'
			)
		);
	}



	protected function getComponents() {
		$components = Mockery::mock( Component_Renderer::class )->makePartial();

		$components->shouldAllowMockingProtectedMethods();
		$components->shouldReceive( 'get_cache_key' )
					->andReturn( null );

		return $components;
	}

	protected function render( string $rough_template, array $variables = array(), $component = null ): string {
		$bladeTemplates = new Blade_Templates();
		$template       = $bladeTemplates->compile( $rough_template );

		$components = $this->getComponents();
		$components->shouldReceive( 'get_template' )
					->andReturn( $template );
		$components->shouldReceive( 'get_cache_key' )
					->andReturn( null );

		if ( null === $component ) {
			$component = Mockery::mock( Component::class )->makePartial();

			$component->shouldReceive( 'get_variable_values' )
						->andReturn( $variables );
		}

		return $components->render(
			$component,
			null,
			true
		);
	}//end render()
}
