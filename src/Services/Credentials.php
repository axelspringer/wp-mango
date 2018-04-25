<?php

namespace AxelSpringer\WP\Mango\Services;

/**
 * Class Credentials
 *
 * @package Wp\Mango\Services
 */
class Credentials {

	/**
	 * @var string
	 */
	protected $token_option_key = 'wp_mango_credentials_token';

	/**
	 * @var string
	 */
	protected $secret_option_key = 'wp_mango_credentials_secret';

	/**
	 *
	 */
	public function generate_credentials() {
		$this->generate_token();
		$this->generate_secret();
	}

	/**
	 * @return string|false
	 */
	public function get_token() {
		return get_option( $this->token_option_key );
	}

	/**
	 * @return string|false
	 */
	public function get_secret() {
		return get_option( $this->secret_option_key );
	}

	/**
	 * @param string $token
	 *
	 * @return bool
	 */
	public function is_valid_token( $token ): bool {
		return $token === $this->get_token();
	}

	/**
	 * @param string $secret
	 *
	 * @return bool
	 */
	public function is_valid_secret( $secret ): bool {
		return $secret === $this->get_secret();
	}

	/**
	 *
	 */
	public function clear_credentials() {
		delete_option( $this->token_option_key );
		delete_option( $this->secret_option_key );
	}

	/**
	 *
	 */
	protected function generate_token() {
		if ( $this->get_token() )
			return;

		update_option( $this->token_option_key, uniqid( '', true ) );
	}

	/**
	 *
	 */
	protected function generate_secret() {
		if ( $this->get_secret() )
			return;

		update_option( $this->secret_option_key, bin2hex( random_bytes( 23 ) ) );
	}
}
