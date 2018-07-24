<?php

namespace AxelSpringer\WP\Mango\Routes;

/**
 * Class Posts
 *
 * @package Wp\Mango\Routes
 */
class Customizer implements Route {
	/**
	 * @var string
	 */
	protected $base = 'customizer';

	/**
	 * Posts constructor.
	 */
	public function __construct() {
	}

	/**
	 * @param Routes $routes
	 */
	public function configure( Routes $routes ) {
		$routes->create( $this->base, [ $this, 'get_settings' ] );
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function get_settings() {
		$settings = get_theme_mods();

        $settings = apply_filters( 'wp_mango_customizer_get_settings', $settings );

		return new \WP_REST_Response( $settings );
	}
}
