<?php

namespace AxelSpringer\WP\Mango\Services;

use AxelSpringer\WP\Bootstrap\Plugin\Setup;

/**
 * Class Credentials
 *
 * @package Wp\Mango\Services
 */
class Credentials {
	/**
	 * @var Setup
	 */
	protected $setup;

	/**
	 * Credentials constructor.
	 *
	 * @param Credentials $credentials
	 */
	public function __construct( Setup &$setup ) {
		$this->setup = $setup;
	}

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
	 * @param string $token
	 *
	 * @return bool
	 */
	public function is_valid_token( $token ): bool
	{
		return $token === $this->setup->options[ 'wp_mango_credentials_token' ];
	}

	/**
	 * @param string $secret
	 *
	 * @return bool
	 */
	public function is_valid_secret( $secret ): bool
	{
		return $secret === $this->setup->options[ 'wp_mango_credentials_secret' ];
	}

	/**
	 *
	 */
	public function clear_credentials()
	{
		delete_option( 'wp_mango_credentials_secret' );
		delete_option( 'wp_mango_credentials_token' );
	}

	/**
	 *
	 */
	protected function generate_token()
	{
		if ( $this->setup->options[ 'wp_mango_credentials_token' ] )
			return;
		
		update_option( 'wp_mango_credentials_token' , uniqid( '', true ) );
	}

	/**
	 *
	 */
	protected function generate_secret()
	{
		if ( $this->setup->options[ 'wp_mango_credentials_secret' ] )
			return;
		
		update_option( 'wp_mango_credentials_secret' , bin2hex( random_bytes( 23 ) ) );
	}
}
