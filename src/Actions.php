<?php

namespace AxelSpringer\WP\Mango;

use AxelSpringer\WP\Mango\Services\Credentials;
use AxelSpringer\WP\Bootstrap\Plugin\Setup;
use AxelSpringer\WP\Mango\Routes\Customizer;
use AxelSpringer\WP\Mango\Routes\Nav;
use AxelSpringer\WP\Mango\Routes\Posts;
use AxelSpringer\WP\Mango\Routes\Routes;

/**
 * Actions Class
 *
 * @package AxelSpringer\WP\Actions
 */
class Actions
{

    /**
     * Setup
     */
    public $setup;

    /**
     * Credentials 
     */
    public $credentials;

    /**
     * Routes
     */
    public $routes;

    /**
     * Actions constructor
     *
     */
    public function __construct( Setup &$setup, Credentials &$credentials )
    {
        // use setup
        $this->setup = $setup;
        $this->credentials = $credentials;

        // init REST API
        add_action( 'rest_api_init', [ &$this, 'rest_api_init' ] );
    }

    /**
     * Rest api init
     */
    public function rest_api_init() {
        if ( ! $this->setup->options['wp_mango_enabled'] ) // if not enabled
            return;

        // configure routes
        $this->routes = new Routes( $this->credentials, $this->setup );

        if ( $this->setup->options['wp_mango_nav'] ) // navigation
            $this->routes->configure( new Nav() );

		if ( $this->setup->options['wp_mango_customizer'] ) // customizer
			$this->routes->configure( new Customizer() );

        if ( $this->setup->options['wp_mango_posts'] ) // posts
		    $this->routes->configure( new Posts() );
	}

    /**
     * noop
     */
    protected function __clone()
    {

    }
}
