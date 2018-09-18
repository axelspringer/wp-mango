<?php

namespace AxelSpringer\WP\Mango\Routes;

use AxelSpringer\WP\Mango\PostStatus;
use AxelSpringer\WP\Mango\PostType;

/**
 * Class Permalink
 *
 * @package Wp\Mango\Routes
 */
class Permalink implements Route {
	/**
	 * @var string
	 */
	protected $base = 'permalink';

	/**
	 * @var Routes
	 */
	protected $routes;

	/**
	 * Configure routes
	 * 
	 * @param Routes $routes
	 */
	public function configure( Routes $routes ) {
		$this->routes = $routes;

		// specific legacy routes
		$routes->create( $this->base, array( &$this, 'get_by_permalink' ) );
	}

	/**
	 * Discovers content by a provided permalink
	 * 
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_by_permalink( \WP_REST_Request $request ): \WP_REST_Response
	{
		global $polylang;

        // try to resolve a post
		$post_id = 0;
		
		if ( $request->get_param( 'permalink' ) ) {
			$post_id = url_to_postid( parse_url( $request->get_param( 'permalink' ), PHP_URL_PATH ) );
		}

		if ( $post_id !== 0 ) { // if we found a belonging post
			$post = get_post( $post_id );

			$ctrl    = new \WP_REST_Posts_Controller( $post->post_type );
			$request->set_param( 'id', $post->ID );

			return apply_filters( 'wp_mango_rest_permalink', $ctrl->get_item( $request ) );
		}

		// trick polylang
		if ( isset( $polylang ) && $request->get_param( 'lang' ) ) {
			$query = new \WP_Query();                                                                     
        	$query->set( 'lang', $request->get_param( 'lang' ) );                                                                  
        	$polylang->filters->set_tax_query_lang( $query );  
		}

        // try to resolve a category as fallback
		$tax_id = get_category_by_path( $request->get_param( 'permalink' ), false );

		if ( is_null( $tax_id ) ) {
			return $this->routes->response_404();
		}

		$params = $request->get_params();
		$ctrl = new \WP_REST_Terms_Controller( $tax_id->taxonomy );
		$request = new \WP_REST_Request();
		$request->set_param( 'id', $tax_id->term_id );
		// $request->set_param( 'slug', $tax_id->slug );

		return apply_filters( 'wp_mango_rest_permalink', $ctrl->get_item( $request ) );
	}
}
