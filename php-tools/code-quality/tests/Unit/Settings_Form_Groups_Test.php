<?php

namespace Unit;

use Io\Prosopo\Procaptcha\Settings\General\Tab\General_Settings_Tab;
use ReflectionMethod;
use Tests\TestCase;

class Settings_Form_Groups_Test extends TestCase {
	public function testGeneralTabSplitsFieldsIntoCardsAndColumns() {
		$columns = $this->arrange(
			array( General_Settings_Tab::SECRET_KEY, General_Settings_Tab::SITE_KEY, General_Settings_Tab::THEME, General_Settings_Tab::PALETTE_PRIMARY, General_Settings_Tab::WIDGET_LANGUAGE ),
			array( General_Settings_Tab::IS_ENABLED_FOR_AUTHORIZED )
		);

		$this->assertSame(
			array(
				array(
					'Connection' => array( General_Settings_Tab::SECRET_KEY, General_Settings_Tab::SITE_KEY ),
					'Behavior'   => array( General_Settings_Tab::WIDGET_LANGUAGE, General_Settings_Tab::IS_ENABLED_FOR_AUTHORIZED ),
				),
				array(
					'Appearance' => array( General_Settings_Tab::THEME, General_Settings_Tab::PALETTE_PRIMARY ),
				),
			),
			$this->summarise( $columns )
		);
	}

	public function testFieldInNoGroupStillGetsACard() {
		$columns = $this->arrange( array( General_Settings_Tab::SITE_KEY, 'new_field' ), array() );

		$this->assertSame(
			array(
				array(
					'Connection' => array( General_Settings_Tab::SITE_KEY ),
					''           => array( 'new_field' ),
				),
			),
			$this->summarise( $columns )
		);
	}

	/**
	 * @param string[] $input_names
	 * @param string[] $checkbox_names
	 *
	 * @return array<int,array<int,array<string,mixed>>>
	 */
	protected function arrange( array $input_names, array $checkbox_names ): array {
		$to_fields = fn ( array $names ): array => array_map( fn ( string $name ): array => array( 'name' => $name ), $names );

		$arrange = new ReflectionMethod( General_Settings_Tab::class, 'arrange_field_groups' );
		$arrange->setAccessible( true );

		return $arrange->invoke( new General_Settings_Tab(), $to_fields( $input_names ), $to_fields( $checkbox_names ) );
	}

	/**
	 * @param array<int,array<int,array<string,mixed>>> $columns
	 *
	 * @return array<int,array<string,string[]>> card title => field names, per column
	 */
	protected function summarise( array $columns ): array {
		return array_map(
			fn ( array $groups ): array => array_combine(
				array_column( $groups, 'title' ),
				array_map(
					fn ( array $group ): array => array_merge( array_column( $group['inputs'], 'name' ), array_column( $group['checkboxes'], 'name' ) ),
					$groups
				)
			),
			$columns
		);
	}
}
