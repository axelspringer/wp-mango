<?php
namespace AxelSpringer\WP\Mango\Routes;

/**
 * Class Nav
 * @package Wp\Mango\Routes
 */
class Nav implements Route {

	/**
	 * @var string
	 */
	protected $base = 'nav';

	/**
	 * @param Routes $routes
	 */
	public function configure( Routes $routes ) {
		$routes->get( $this->base . '/menus/(?P<id>\d+)', [ $this, 'get_nav_menu' ] );
		$routes->get( $this->base . '/items/(?P<id>\d+)', [ $this, 'get_nav_menu_items' ] );
		$routes->get( $this->base . '/locations', [ $this, 'get_nav_menu_locations' ] );
		$routes->get( $this->base . '/locations/(?P<name>[a-zA-Z0-9\_]+)', [ $this, 'get_nav_menu_location' ] );
	}

	/**
	 * @return array
	 */
	public function get_nav_menu_locations() {
		$locations = get_nav_menu_locations();

		$locations = apply_filters( 'wp_mango_nav_get_nav_menu_locations', $locations );

		return $locations;
	}

	/**
	 * @param $data
	 *
	 * @return array|false|\WP_Error
	 */
	public function get_nav_menu_items( $data ) {
		$items = wp_get_nav_menu_items( $data[ 'id' ] );

		if ( ! $items ) {
			return new \WP_Error( 'no_menu', 'Invalid menu', array( 'status' => 404 ) );
		}

		return $items;
	}

	/**
	 * @param $data
	 *
	 * @return \WP_Error|\WP_Term
	 */
	public function get_nav_menu_location( $data ) {
		$locations = $this->get_nav_menu_locations();

		if ( ! array_key_exists( $data[ 'name' ], $locations ) ) {
			return new \WP_Error( 'no_menu_location', 'Invalid menu Location', array( 'status' => 404 ) );
		}

		return $locations[ $data[ 'name' ] ];
	}

	/**
	 * @param $data
	 *
	 * @return false|\WP_Error|\WP_Term
	 */
	public function get_nav_menu( $data ) {
		$menu = wp_get_nav_menu_object( $data[ 'id' ] );

		if ( ! $menu ) {
			return new \WP_Error( 'no_menu', 'Invalid menu', array( 'status' => 404 ) );
		}

		return $menu;
	}
}
