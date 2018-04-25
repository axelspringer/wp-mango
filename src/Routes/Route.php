<?php

namespace AxelSpringer\WP\Mango\Routes;

/**
 * Interface Route
 *
 * @package Wp\Mango\Routes
 */
interface Route {

	/**
	 * @param Routes $routes
	 */
	public function configure( Routes $routes );
}
