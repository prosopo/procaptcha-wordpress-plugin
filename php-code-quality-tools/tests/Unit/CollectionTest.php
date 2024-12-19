<?php

namespace Unit;

use Io\Prosopo\Procaptcha\Collection;
use Tests\TestCase;

class CollectionTest extends TestCase {
	public function testMergeDoesOverride(): void {
		$collection = new Collection( array( 'item' => 1 ) );

		$collection->merge( array( 'item' => 2 ) );

		$this->assertEquals( array( 'item' => 2 ), $collection->to_array() );
	}

	public function testMergeDoesNotOverrideWhenFlagIsSet(): void {
		$collection = new Collection( array( 'item' => 1 ) );

		$collection->merge( array( 'item' => 2 ), true );

		$this->assertEquals( array( 'item' => 1 ), $collection->to_array() );
	}

	public function testGetSubCollectionStaysLinkedInTheParent(): void {
		$collection = new Collection( array( 'item' => array( 'sub' => 1 ) ) );

		$sub_collection = $collection->get_sub_collection( 'item' );
		$sub_collection->add( 'sub', 2 );

		$this->assertEquals( array( 'item' => array( 'sub' => 2 ) ), $collection->to_array() );
	}

	public function testMergeHtmlAttrsForClass() {
		$first  = new Collection( array( 'class' => 'first' ) );
		$second = new Collection( array( 'class' => 'second' ) );

		$this->assertEquals(
			'first second',
			$first->merge_html_attrs( $second )->get_string( 'class' )
		);
	}

	public function testMergeHtmlAttrsForStyle() {
		$first  = new Collection( array( 'style' => 'color:red' ) );
		$second = new Collection( array( 'style' => 'background:blue' ) );

		$this->assertEquals(
			'color:red;background:blue',
			$first->merge_html_attrs( $second )->get_string( 'style' )
		);
	}
}
