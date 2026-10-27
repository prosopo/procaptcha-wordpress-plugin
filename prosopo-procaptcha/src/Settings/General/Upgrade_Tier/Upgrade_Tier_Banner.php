<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\General\Upgrade_Tier;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;

class Upgrade_Tier_Banner extends BaseTemplateModel {
	public string $title;
	/**
	 * @var array<int, string>
	 */
	public array $benefits;
	public string $button_label;
	public string $button_url;

	protected function setCustomDefaults(): void {
		parent::setCustomDefaults();

		$this->title        = __( 'Upgrade to Professional', 'prosopo-procaptcha' );
		$this->benefits     = array(
			__( 'Spam Filter: blocks disposable and throwaway email addresses', 'prosopo-procaptcha' ),
			__( 'VPN, proxy and datacenter filtering', 'prosopo-procaptcha' ),
			__( 'Choose your challenge type, plus icon-order and audio challenges', 'prosopo-procaptcha' ),
			__( 'A risk score and block reason for every decision', 'prosopo-procaptcha' ),
			__( 'Up to 1M requests a month and 50 sites', 'prosopo-procaptcha' ),
			__( 'Email and Discord support, 99.9% uptime', 'prosopo-procaptcha' ),
		);
		$this->button_label = __( 'Start a 30-day free trial', 'prosopo-procaptcha' );
		$this->button_url   = 'https://portal.prosopo.io/?utm_source=procaptcha-wp&utm_medium=plugin&utm_campaign=settings&utm_content=upgrade-banner';
	}
}
