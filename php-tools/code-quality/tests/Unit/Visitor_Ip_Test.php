<?php

namespace Unit;

use Io\Prosopo\Procaptcha\Settings\Procaptcha_Settings;
use Io\Prosopo\Procaptcha\Widget\Procaptcha_Widget;
use Mockery;
use Tests\TestCase;

class Visitor_Ip_Test extends TestCase {
	const SERVER = array(
		'REMOTE_ADDR'           => '172.68.1.1',
		'HTTP_CF_CONNECTING_IP' => '203.0.113.7',
		'HTTP_X_FORWARDED_FOR'  => '198.51.100.9, 10.0.0.1',
	);

	protected function tearDown(): void {
		$GLOBALS['test_filters'] = array();
		Mockery::close();
	}

	public function testNothingIsSentByDefault() {
		$this->assertSame( '', $this->get_visitor_ip( '' ) );
	}

	public function testCloudflareHeader() {
		$this->assertSame( '203.0.113.7', $this->get_visitor_ip( Procaptcha_Settings::VISITOR_IP_CLOUDFLARE ) );
	}

	public function testCloudflareWithoutHeaderSendsNothing() {
		$this->assertSame( '', $this->get_visitor_ip( Procaptcha_Settings::VISITOR_IP_CLOUDFLARE, array( 'REMOTE_ADDR' => '172.68.1.1' ) ) );
	}

	public function testFirstForwardedForAddress() {
		$this->assertSame( '198.51.100.9', $this->get_visitor_ip( Procaptcha_Settings::VISITOR_IP_X_FORWARDED_FOR ) );
	}

	public function testRemoteAddr() {
		$this->assertSame( '172.68.1.1', $this->get_visitor_ip( Procaptcha_Settings::VISITOR_IP_REMOTE_ADDR ) );
	}

	public function testFilterCanEnableWhenOff() {
		$GLOBALS['test_filters']['prosopo/procaptcha/visitor_ip'] = fn( $ip, $remote_addr ) => $remote_addr;

		$this->assertSame( '172.68.1.1', $this->get_visitor_ip( '' ) );
	}

	public function testInvalidIpIsNotSent() {
		$this->assertSame( '', $this->get_visitor_ip( Procaptcha_Settings::VISITOR_IP_CLOUDFLARE, array( 'HTTP_CF_CONNECTING_IP' => 'not-an-ip' ) ) );
	}

	protected function get_visitor_ip( string $source, array $server = self::SERVER ): string {
		$_SERVER = $server;

		$settings = Mockery::mock( Procaptcha_Settings::class );
		$settings->shouldReceive( 'get_visitor_ip_source' )->andReturn( $source );

		$widget_class = get_class(
			new class() extends Procaptcha_Widget {
				// Skip the parent's constructor: only the settings are needed.
				public function __construct() {}

				public function visitor_ip(): string {
					return $this->get_visitor_ip();
				}
			}
		);
		$widget       = new $widget_class();

		$settings_property = new \ReflectionProperty( Procaptcha_Widget::class, 'procaptcha_settings' );
		$settings_property->setAccessible( true );
		$settings_property->setValue( $widget, $settings );

		return $widget->visitor_ip();
	}
}
