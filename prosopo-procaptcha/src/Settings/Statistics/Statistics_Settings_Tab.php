<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Statistics;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Procaptcha_Plugin;
use Io\Prosopo\Procaptcha\Settings\General\Upgrade_Tier\Upgrade_Tier_Banner;
use Io\Prosopo\Procaptcha\Settings\Procaptcha_Settings;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab_Base;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Widget\Widget;

class Statistics_Settings_Tab extends Settings_Tab_Base {
	private Procaptcha_Settings $procaptcha_settings;
	private ModelRendererInterface $model_renderer;

	public function __construct( Procaptcha_Settings $procaptcha_settings, ModelRendererInterface $model_renderer ) {
		parent::__construct();

		$this->procaptcha_settings = $procaptcha_settings;
		$this->model_renderer      = $model_renderer;
	}

	public function get_tab_title(): string {
		return __( 'Statistics', 'prosopo-procaptcha' );
	}

	public function get_tab_name(): string {
		return 'statistics';
	}

	public function make_tab_component( ModelFactoryInterface $factory, Widget $widget ): TemplateModelInterface {
		return $factory->createModel(
			Statistics_Settings::class,
			function ( Statistics_Settings $statistics ) use ( $widget ) {
				$statistics->is_available = $widget->is_available();
			}
		);
	}

	public function get_tab_script_asset(): string {
		return 'settings/statistics/statistics.min.js';
	}

	public function get_style_asset(): string {
		return 'settings/statistics/statistics-styles.min.css';
	}

	public function get_tab_js_data(): array {
		$secret_key = $this->procaptcha_settings->get_secret_key();
		$site_key   = $this->procaptcha_settings->get_site_key();

		$call_to_upgrade_element_markup = $this->model_renderer->renderModel( Upgrade_Tier_Banner::class );

		return array(
			'aboutLabels'                => array(
				'buttonLabel' => __( 'Open the Portal', 'prosopo-procaptcha' ),
				'message'     => __( 'Usage and settings for your site key. Manage your account and site settings in the Prosopo portal.', 'prosopo-procaptcha' ),
				'title'       => __( 'Your Site Key', 'prosopo-procaptcha' ),
			),
			'accountApiEndpoint'         => Procaptcha_Plugin::ACCOUNT_API_ENDPOINT_URL,
			'callToUpgradeElementMarkup' => $call_to_upgrade_element_markup,
			'challengeLabels'            => array(
				'buttonLabel'      => __( 'Change in the portal', 'prosopo-procaptcha' ),
				'changeInPortal'   => __( 'Your plan lets you choose the challenge type for this site key in the Prosopo portal.', 'prosopo-procaptcha' ),
				'changeOnPaidPlan' => __( 'Procaptcha shows a challenge only to visitors who look like bots, and picks a Proof of Work, image or jigsaw puzzle challenge for them. Choosing a single challenge type is part of the Professional plan.', 'prosopo-procaptcha' ),
				'current'          => __( 'Challenge type:', 'prosopo-procaptcha' ),
				'title'            => __( 'Your Challenges', 'prosopo-procaptcha' ),
				'types'            => array(
					'audio'        => __( 'Audio', 'prosopo-procaptcha' ),
					'frictionless' => __( 'Frictionless (challenge only when needed)', 'prosopo-procaptcha' ),
					'iconOrder'    => __( 'Icon order', 'prosopo-procaptcha' ),
					'image'        => __( 'Image', 'prosopo-procaptcha' ),
					'pow'          => __( 'Proof of Work', 'prosopo-procaptcha' ),
					'puzzle'       => __( 'Jigsaw puzzle', 'prosopo-procaptcha' ),
				),
			),
			'isDebugMode'                => false, // todo move into settings as 'debug mode' option.
			'secretKey'                  => $secret_key,
			'siteKey'                    => $site_key,
			'stateLabels'                => array(
				'failedToLoad'    => __( 'Your statistics could not be loaded. Check that the Site Key and Secret Key on the General tab are correct and saved, then try again.', 'prosopo-procaptcha' ),
				'lastRefreshedAt' => __( 'Successfully loaded at', 'prosopo-procaptcha' ),
				'loading'         => __( 'Loading, please wait.', 'prosopo-procaptcha' ),
				'refreshNow'      => __( 'Refresh now', 'prosopo-procaptcha' ),
				'tryAgain'        => __( 'Try again', 'prosopo-procaptcha' ),
			),
			'trafficDataLabels'          => array(
				'buttonLabel' => __( 'View traffic analytics', 'prosopo-procaptcha' ),
				'message'     => __( 'Traffic analytics are included on every plan. See the charts in the Prosopo portal.', 'prosopo-procaptcha' ),
				'title'       => __( 'Traffic Analytics', 'prosopo-procaptcha' ),
			),
			'usageLabels'                => array(
				'audio'       => __( 'Audio:', 'prosopo-procaptcha' ),
				'iconOrder'   => __( 'Icon order:', 'prosopo-procaptcha' ),
				'image'       => __( 'Image:', 'prosopo-procaptcha' ),
				'proofOfWork' => __( 'Proof of Work:', 'prosopo-procaptcha' ),
				'puzzle'      => __( 'Jigsaw puzzle:', 'prosopo-procaptcha' ),
				'title'       => __( 'Monthly Usage', 'prosopo-procaptcha' ),
				'total'       => __( 'Total Verification Requests:', 'prosopo-procaptcha' ),
			),
		);
	}

	protected function get_option_name(): string {
		return '';
	}
}
