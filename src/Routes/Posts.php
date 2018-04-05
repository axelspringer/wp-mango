<?php

namespace Wp\Mango\Routes;

/**
 * Class Posts
 *
 * @package Wp\Mango\Routes
 */
class Posts implements Route {
	/**
	 * @var string
	 */
	protected $base = 'posts';

	/**
	 * Posts constructor.
	 */
	public function __construct() {
	}

	/**
	 * @param Routes $routes
	 */
	public function configure( Routes $routes ) {
		$routes->get( $this->base . '/url-to-post', [ $this, 'url_to_post' ] );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function url_to_post( \WP_REST_Request $request ): \WP_REST_Response {
		$post_id = url_to_postid( $request->get_param( 'url' ) );

		if ( $post_id === 0 ) {
			return new \WP_REST_Response( null, 404 );
		}

		$post             = get_post( $post_id );
		$post->categories = wp_get_post_categories( $post->ID, [ 'fields' => 'all' ] );
		$post->tags       = wp_get_post_tags( $post->ID );

		$post = apply_filters( 'wp_mango_routes_posts_url_to_post', $post );

		return new \WP_REST_Response( $post );
	}
}
