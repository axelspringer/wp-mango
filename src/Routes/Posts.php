<?php

namespace AxelSpringer\WP\Mango\Routes;

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
	 * @var Routes
	 */
	protected $routes;

	/**
	 * @param Routes $routes
	 */
	public function configure( Routes $routes ) {
		$this->routes = $routes;

		$routes->get( $this->base . '/post-by-permalink', [ $this, 'post_by_permalink' ] );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function post_by_permalink( \WP_REST_Request $request ): \WP_REST_Response {
		$post_id = url_to_postid( $request->get_param( 'permalink' ) );

		if ( $post_id === 0 ) {
			return $this->routes->response_404();
		}

		$post = get_post( $post_id );

		$ctrl    = new \WP_REST_Posts_Controller( $post->post_type );
		$request = new \WP_REST_Request();
		//$_GET['_embed'] = true;
		$request->set_param( 'id', $post->ID );

		return apply_filters( 'wp_mango_routes_posts_post_by_permalink', $ctrl->get_item( $request ) );
	}
}
