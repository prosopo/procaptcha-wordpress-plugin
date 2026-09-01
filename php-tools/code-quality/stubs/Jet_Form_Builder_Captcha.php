<?php

namespace JFB_Modules\Captcha {

	class Module {

		const SPAM_EXCEPTION = 'captcha_failed';
	}
}

namespace JFB_Modules\Captcha\Abstract_Captcha {

	abstract class Base_Captcha {

		const FIELD       = '_captcha_token';
		const FIELD_CLASS = 'captcha-token';

		/**
		 * @var array<string,mixed>
		 */
		protected $options;

		abstract public function get_id(): string;

		abstract public function get_title(): string;

		/**
		 * @param array<string,mixed> $request
		 *
		 * @return void
		 */
		abstract public function verify( array $request );

		abstract protected function render(): string;

		public function get_output(): string {
			return $this->render();
		}

		/**
		 * @param array<string,mixed> $options
		 */
		public function sanitize_options( array $options ): Base_Captcha {
			return $this;
		}

		/**
		 * @return string
		 */
		public function rep_item_id() {
			return $this->get_id();
		}

		/**
		 * @return array<string,string>
		 */
		public function to_array(): array {
			return array(
				'label' => $this->get_title(),
				'value' => $this->get_id(),
			);
		}
	}

	interface Captcha_Settings_From_Options {

		/**
		 * @param array<string,mixed> $post_request
		 *
		 * @return array<string,mixed>
		 */
		public function on_save_options( array $post_request ): array;

		/**
		 * @return array<string,mixed>
		 */
		public function on_load_options(): array;
	}
}

namespace JFB_Modules\Security\Exceptions {

	class Spam_Exception extends \Exception {

		/**
		 * @param string $message
		 * @param mixed  ...$additional_data
		 */
		public function __construct( $message = '', ...$additional_data ) {
			parent::__construct( (string) $message );
		}
	}
}
