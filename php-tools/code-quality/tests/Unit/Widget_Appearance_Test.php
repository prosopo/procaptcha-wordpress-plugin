<?php

namespace Unit;

use Io\Prosopo\Procaptcha\Settings\General\Tab\General_Settings_Tab;
use Io\Prosopo\Procaptcha\Utils\Assets\Assets_Loader;
use Io\Prosopo\Procaptcha\Utils\Assets\Assets_Resolver;
use Io\Prosopo\Procaptcha\Widget\Widget_Assets_Loader;
use Tests\TestCase;

class Widget_Appearance_Test extends TestCase {
	const BASE_SETTINGS = array(
		General_Settings_Tab::SITE_KEY        => 'site-key',
		General_Settings_Tab::THEME           => 'light',
		General_Settings_Tab::WIDGET_LANGUAGE => General_Settings_Tab::LANGUAGE_VISITOR,
	);

	protected function tearDown(): void {
		$_POST                             = array();
		$GLOBALS['test_localized_scripts'] = array();
	}

	public function testValidColorsAreKept() {
		$this->assertSame( '#abc', General_Settings_Tab::sanitize_color( '#abc' ) );
		$this->assertSame( '#0A7D3B', General_Settings_Tab::sanitize_color( '#0A7D3B' ) );
		$this->assertSame( '#0a7d3b', General_Settings_Tab::sanitize_color( ' #0a7d3b ' ) );
	}

	public function testInvalidColorsAreDropped() {
		$invalid_colors = array( '', 'red', 'abc', '#abcd', '#gggggg', '#0a7d3b0', 'rgb(0,0,0)', '#abc;background:url(x)' );

		foreach ( $invalid_colors as $color ) {
			$this->assertSame( '', General_Settings_Tab::sanitize_color( $color ), $color );
		}
	}

	public function testValidFontsAreKept() {
		$this->assertSame( '"Inter", Arial, sans-serif', General_Settings_Tab::sanitize_font( '"Inter", Arial, sans-serif' ) );
		$this->assertSame( "'Open Sans', sans-serif", General_Settings_Tab::sanitize_font( " 'Open Sans', sans-serif " ) );
	}

	public function testInvalidFontsAreDropped() {
		$invalid_fonts = array( '', 'Arial; color: red', 'url(x)', 'Arial</style>', 'Arial{}' );

		foreach ( $invalid_fonts as $font ) {
			$this->assertSame( '', General_Settings_Tab::sanitize_font( $font ), $font );
		}
	}

	public function testSavingKeepsValidValuesAndEmptiesInvalidOnes() {
		$_POST = array(
			General_Settings_Tab::FONT                   => 'Arial; color: red',
			General_Settings_Tab::PALETTE_BORDER         => 'red',
			General_Settings_Tab::PALETTE_PRIMARY        => '#0a7d3b',
			General_Settings_Tab::PALETTE_TEXT_SECONDARY => '#555',
			General_Settings_Tab::SHAPE                  => 'square',
		);

		$fields = $this->validate( $_POST );

		$this->assertSame( '', $fields[ General_Settings_Tab::FONT ] );
		$this->assertSame( '', $fields[ General_Settings_Tab::PALETTE_BORDER ] );
		$this->assertSame( '', $fields[ General_Settings_Tab::PALETTE_SURFACE ] );
		$this->assertSame( '#0a7d3b', $fields[ General_Settings_Tab::PALETTE_PRIMARY ] );
		$this->assertSame( '#555', $fields[ General_Settings_Tab::PALETTE_TEXT_SECONDARY ] );
		$this->assertSame( 'square', $fields[ General_Settings_Tab::SHAPE ] );
	}

	public function testSavingKeepsAValidFont() {
		$fields = $this->validate( array( General_Settings_Tab::FONT => '"Inter", Arial, sans-serif' ) );

		$this->assertSame( '"Inter", Arial, sans-serif', $fields[ General_Settings_Tab::FONT ] );
	}

	public function testSavingIgnoresAnUnknownShape() {
		$fields = $this->validate( array( General_Settings_Tab::SHAPE => 'circle' ) );

		$this->assertArrayNotHasKey( General_Settings_Tab::SHAPE, $fields );
	}

	public function testDefaultsLeaveTheWidgetUnchanged() {
		$attributes = $this->get_widget_attributes( array() );

		$this->assertSame(
			array(
				'shape'   => 'rounded',
				'siteKey' => 'site-key',
				'theme'   => 'light',
			),
			$attributes
		);
	}

	public function testAppearanceReachesTheWidget() {
		$attributes = $this->get_widget_attributes(
			array(
				General_Settings_Tab::FONT                   => '"Inter", Arial, sans-serif',
				General_Settings_Tab::PALETTE_ERROR          => '',
				General_Settings_Tab::PALETTE_PRIMARY        => '#0a7d3b',
				General_Settings_Tab::PALETTE_TEXT_SECONDARY => '#555555',
				General_Settings_Tab::SHAPE                  => 'square',
			)
		);

		$this->assertSame( 'square', $attributes['shape'] );
		$this->assertSame( '"Inter", Arial, sans-serif', $attributes['font'] );
		$this->assertSame(
			array(
				'primary'       => '#0a7d3b',
				'textSecondary' => '#555555',
			),
			$attributes['palette']
		);
	}

	public function testInvalidStoredValuesDoNotReachTheWidget() {
		$attributes = $this->get_widget_attributes(
			array(
				General_Settings_Tab::FONT            => 'url(x)',
				General_Settings_Tab::PALETTE_PRIMARY => 'red',
				General_Settings_Tab::SHAPE           => 'circle',
			)
		);

		$this->assertSame( 'rounded', $attributes['shape'] );
		$this->assertArrayNotHasKey( 'font', $attributes );
		$this->assertArrayNotHasKey( 'palette', $attributes );
	}

	/**
	 * @param array<string,string> $post
	 *
	 * @return array<string,mixed>
	 */
	protected function validate( array $post ): array {
		$_POST = $post;

		$tab = new General_Settings_Tab();

		$validate = new \ReflectionMethod( General_Settings_Tab::class, 'validate_settings' );
		$validate->setAccessible( true );
		$validate->invoke( $tab );

		$fields = new \ReflectionProperty( General_Settings_Tab::class, 'validated_fields' );
		$fields->setAccessible( true );

		return $fields->getValue( $tab );
	}

	/**
	 * @param array<string,string> $settings
	 *
	 * @return array<string,mixed>
	 */
	protected function get_widget_attributes( array $settings ): array {
		$tab = new General_Settings_Tab();

		$settings_property = new \ReflectionProperty( General_Settings_Tab::class, 'settings' );
		$settings_property->setAccessible( true );
		$settings_property->setValue( $tab, array_merge( self::BASE_SETTINGS, $settings ) );

		$loader = new Widget_Assets_Loader(
			'https://example.com/procaptcha.bundle.js',
			'https://example.com/procaptcha.bundle.iife.js',
			'prosopo-procaptcha',
			new Assets_Loader( new Assets_Resolver( 'https://example.com/dist' ) ),
			$tab
		);

		$loader->load_widget();
		$loader->enqueue_assets_when_in_use();

		return $GLOBALS['test_localized_scripts']['procaptchaWpAttributes'];
	}
}
