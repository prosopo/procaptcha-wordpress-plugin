<?php

namespace Unit;

use Io\Prosopo\Procaptcha\Html_Attributes_Collection;
use Tests\TestCase;

class Html_Attributes_Collection_Test extends TestCase {
	public function testMergeHtmlAttrsForClass() {
		$first  = new Html_Attributes_Collection( array( 'class' => 'first' ) );
		$second = new Html_Attributes_Collection( array( 'class' => 'second' ) );

		$this->assertEquals(
			'first second',
			$first->merge( $second )->get_items()['class']
		);
	}

	public function testMergeHtmlAttrsForStyle() {
		$first  = new Html_Attributes_Collection( array( 'style' => 'color:red' ) );
		$second = new Html_Attributes_Collection( array( 'style' => 'background:blue' ) );

		$this->assertEquals(
			'color:red;background:blue',
			$first->merge( $second )->get_items()['style']
		);
	}
}
