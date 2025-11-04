<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Settings;

defined( 'ABSPATH' ) || exit;

interface Account_Form_Settings {
	public function is_login_protected(): bool;

	public function is_registration_protected(): bool;

	public function is_password_recovery_protected(): bool;
}
