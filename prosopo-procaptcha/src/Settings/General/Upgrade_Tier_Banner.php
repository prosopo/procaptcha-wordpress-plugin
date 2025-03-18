<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\General;

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

		$this->title = __( 'Unlock more with Pro tier', 'prosopo-procaptcha' );

		$this->benefits = array(
			__( 'Up to 1M monthly requests', 'prosopo-procaptcha' ),
			__( 'Rapid technical support', 'prosopo-procaptcha' ),
			__( 'Unlimited number of sites', 'prosopo-procaptcha' ),
			__( 'Advanced user management', 'prosopo-procaptcha' ),
			__( 'Traffic analytics and statistics', 'prosopo-procaptcha' ),
		);

		$this->button_label = __( 'Upgrade', 'prosopo-procaptcha' );
		$this->button_url   = 'https://portal.prosopo.io/';
	}
}
