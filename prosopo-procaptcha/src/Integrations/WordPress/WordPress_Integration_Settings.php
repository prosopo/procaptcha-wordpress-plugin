<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

use Io\Prosopo\Procaptcha\Settings\Tab\Procaptcha_Settings_Tab;

defined( 'ABSPATH' ) || exit;

final class WordPress_Integration_Settings extends Procaptcha_Settings_Tab {
	const IS_ON_WP_LOGIN_FORM         = 'is_on_wp_login_form';
	const IS_ON_WP_REGISTER_FORM      = 'is_on_wp_register_form';
	const IS_ON_WP_LOST_PASSWORD_FORM = 'is_on_wp_lost_password_form';
	const IS_ON_WP_COMMENT_FORM       = 'is_on_wp_comment_form';
	const IS_ON_WP_POST_FORM          = 'is_on_wp_post_form';

	public function get_tab_title(): string {
		return __( 'Account Forms', 'prosopo-procaptcha' );
	}

	public function get_tab_name(): string {
		return 'account-forms';
	}

	protected function get_option_name(): string {
		// For back compatibility.
		return self::OPTION_BASE;
	}

	protected function get_bool_settings(): array {
		return array(
			self::IS_ON_WP_COMMENT_FORM       => __( 'Protect the comment form:', 'prosopo-procaptcha' ),
			self::IS_ON_WP_LOGIN_FORM         => __( 'Protect the login form:', 'prosopo-procaptcha' ),
			self::IS_ON_WP_LOST_PASSWORD_FORM => __( 'Protect the lost password form:', 'prosopo-procaptcha' ),
			self::IS_ON_WP_POST_FORM          => __( 'Protect the post/page password form:', 'prosopo-procaptcha' ),
			self::IS_ON_WP_REGISTER_FORM      => __( 'Protect the register form:', 'prosopo-procaptcha' ),
		);
	}
}
