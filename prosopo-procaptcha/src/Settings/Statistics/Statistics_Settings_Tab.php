<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Statistics;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Settings\General\General_Settings_Tab;
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;
use Io\Prosopo\Procaptcha\Settings\Tab\Procaptcha_Settings_Tab;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Widget\Widget;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class Statistics_Settings_Tab extends Procaptcha_Settings_Tab {
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

	public function get_tab_js_data( Settings_Storage $settings_storage ): array {
		$general_settings = $settings_storage->get( General_Settings_Tab::class )->get_settings();

		return array(
			'accountLabels'         => array(
				'name'  => __( 'Name:', 'prosopo-procaptcha' ),
				'tier'  => __( 'Tier:', 'prosopo-procaptcha' ),
				'title' => __( 'Account Information', 'prosopo-procaptcha' ),
			),
			'captchaSettingsLabels' => array(
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
			'domainLabels'          => array(
				'title' => __( 'Whitelisted Domains', 'prosopo-procaptcha' ),
			),
			'isDebugMode'           => false, // todo move into settings as 'debug mode' option.
			'secretKey'             => string( $general_settings, General_Settings_Tab::SECRET_KEY ),
			'siteKey'               => string( $general_settings, General_Settings_Tab::SITE_KEY ),
			'stateLabels'           => array(
				'failedToLoad'    => __( 'Failed to load. Please try again later.', 'prosopo-procaptcha' ),
				'lastRefreshedAt' => __( 'Successfully loaded at', 'prosopo-procaptcha' ),
				'loading'         => __( 'Loading, please wait.', 'prosopo-procaptcha' ),
				'refreshNow'      => __( 'Refresh now', 'prosopo-procaptcha' ),
			),
			'trafficDataLabels'     => array(
				'chartTitle'       => __( 'Traffic Data Over Time', 'prosopo-procaptcha' ),
				'imageSubmissions' => __( 'Image Submissions', 'prosopo-procaptcha' ),
				'powSubmissions'   => __( 'Proof of Work Submissions', 'prosopo-procaptcha' ),
				'submissionsCount' => __( 'Submissions Count', 'prosopo-procaptcha' ),
				'time'             => __( 'Time', 'prosopo-procaptcha' ),
				'title'            => __( 'Traffic Analytics', 'prosopo-procaptcha' ),
			),
			'usageLabels'           => array(
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
