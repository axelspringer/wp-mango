<?php

namespace AxelSpringer\WP\Mango\Services;

/**
 * Class Credentials
 *
 * @package Wp\Mango\Services
 */
class Credentials {

	const OPTION_TOKEN 			= 'wp_mango_credentials_token';
	const OPTION_SECRET_KEY 	= 'wp_mango_credentials_secret'; 

	/**
	 * Generate credentials
	 * 
	 */
	public function generate_credentials()
	{
		$this->generate_token();
		$this->generate_secret();
	}

	/**
	 * @return string|false
	 */
	public function get_token()
	{
		return get_option( Credentials::OPTION_TOKEN );
	}

	/**
	 * @return string|false
	 */
	public function get_secret()
	{
		return get_option( Credentials::OPTION_SECRET_KEY );
	}

	/**
	 * @param string $token
	 *
	 * @return bool
	 */
	public function is_valid_token( $token ): bool
	{
		return $token === $this->get_token();
	}

	/**
	 * @param string $secret
	 *
	 * @return bool
	 */
	public function is_valid_secret( $secret ): bool
	{
		return $secret === $this->get_secret();
	}

	/**
	 *
	 */
	public function clear_credentials()
	{
		delete_option( Credentials::OPTION_TOKEN );
		delete_option( Credentials::OPTION_SECRET_KEY );
	}

	/**
	 *
	 */
	protected function generate_token()
	{
		if ( $this->get_token() )
			return;

		update_option( Credentials::OPTION_TOKEN , uniqid( '', true ) );
	}

	/**
	 *
	 */
	protected function generate_secret()
	{
		if ( $this->get_secret() )
			return;

		update_option( Credentials::OPTION_SECRET_KEY , bin2hex( random_bytes( 23 ) ) );
	}
}
