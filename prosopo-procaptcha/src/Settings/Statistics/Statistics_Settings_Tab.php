<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Statistics;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Procaptcha_Plugin;
use Io\Prosopo\Procaptcha\Settings\General\Upgrade_Tier_Banner;
use Io\Prosopo\Procaptcha\Settings\Procaptcha_Settings;
use Io\Prosopo\Procaptcha\Settings\Tab\Procaptcha_Settings_Tab;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Widget\Widget;

class Statistics_Settings_Tab extends Procaptcha_Settings_Tab {
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

		$call_to_upgrade_element_markup = $this->model_renderer->renderModel(
			Upgrade_Tier_Banner::class,
			function ( Upgrade_Tier_Banner $model ) {
				$model->title = __( 'Unlock Analytics with Pro tier', 'prosopo-procaptcha' );
			}
		);

		return array(
			'accountApiEndpoint'         => Procaptcha_Plugin::ACCOUNT_API_ENDPOINT_URL,
			'accountLabels'              => array(
				'name'  => __( 'Name:', 'prosopo-procaptcha' ),
				'tier'  => __( 'Tier:', 'prosopo-procaptcha' ),
				'title' => __( 'Account Information', 'prosopo-procaptcha' ),
			),
			'callToUpgradeElementMarkup' => $call_to_upgrade_element_markup,
			'captchaSettingsLabels'      => array(
				'frictionlessThreshold' => __( 'Frictionless Threshold:', 'prosopo-procaptcha' ),
				'level'                 => array(
					'high'   => __( 'High', 'prosopo-procaptcha' ),
					'low'    => __( 'Low', 'prosopo-procaptcha' ),
					'normal' => __( 'Normal', 'prosopo-procaptcha' ),
				),
				'powDifficulty'         => __( 'Proof of Work Difficulty:', 'prosopo-procaptcha' ),
				'title'                 => __( 'Captcha Settings', 'prosopo-procaptcha' ),
				'type'                  => __( 'Type:', 'prosopo-procaptcha' ),
				'types'                 => array(
					'frictionless' => __( 'Frictionless', 'prosopo-procaptcha' ),
					'image'        => __( 'Image', 'prosopo-procaptcha' ),
					'proofOfWork'  => __( 'Proof of Work', 'prosopo-procaptcha' ),
				),
			),
			'domainLabels'               => array(
				'title' => __( 'Whitelisted Domains', 'prosopo-procaptcha' ),
			),
			'isDebugMode'                => false, // todo move into settings as 'debug mode' option.
			'secretKey'                  => $secret_key,
			'siteKey'                    => $site_key,
			'stateLabels'                => array(
				'failedToLoad'    => __( 'Failed to load. Please try again later.', 'prosopo-procaptcha' ),
				'lastRefreshedAt' => __( 'Successfully loaded at', 'prosopo-procaptcha' ),
				'loading'         => __( 'Loading, please wait.', 'prosopo-procaptcha' ),
				'refreshNow'      => __( 'Refresh now', 'prosopo-procaptcha' ),
			),
			'trafficDataLabels'          => array(
				'chartTitle'       => __( 'Traffic Data Over Time', 'prosopo-procaptcha' ),
				'imageSubmissions' => __( 'Image Submissions', 'prosopo-procaptcha' ),
				'powSubmissions'   => __( 'Proof of Work Submissions', 'prosopo-procaptcha' ),
				'submissionsCount' => __( 'Submissions Count', 'prosopo-procaptcha' ),
				'time'             => __( 'Time', 'prosopo-procaptcha' ),
				'title'            => __( 'Traffic Analytics', 'prosopo-procaptcha' ),
			),
			'usageLabels'                => array(
				'image'       => __( 'Image:', 'prosopo-procaptcha' ),
				'proofOfWork' => __( 'Proof of Work:', 'prosopo-procaptcha' ),
				'title'       => __( 'Monthly Usage', 'prosopo-procaptcha' ),
				'total'       => __( 'Total Verification Requests:', 'prosopo-procaptcha' ),
			),
		);
	}

	protected function get_option_name(): string {
		return '';
	}
}
