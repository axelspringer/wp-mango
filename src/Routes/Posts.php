<?php

namespace AxelSpringer\WP\Mango\Routes;

use AxelSpringer\WP\Mango\PostStatus;
use AxelSpringer\WP\Mango\PostType;

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
	 * Configure routes
	 * 
	 * @param Routes $routes
	 */
	public function configure( Routes $routes ) {
		$this->routes = $routes;

		// specific legacy routes
		$routes->get( $this->base . '/post-by-permalink', [ $this, 'post_by_permalink' ] );
		$routes->get( $this->base . '/post/(?P<id>\d+)', [ $this, 'get_item' ] );
	}

	/**
	 * Get all posts to an id
	 * 
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_item( \WP_REST_Request $request )
	{
		$query_args = array(
			// 'post_name__in'	=> array( 'test' ),
			'p'         	=> $request->get_param( 'id' ), // ID of a page, post, or custom type
			'post_type' 	=> 'any', // find all posts
			'post_status' 	=> array( PostStatus::Publish )
		);

		$status = array( // extended status
			PostStatus::Draft,
			PostStatus::AutoDraft,
			PostStatus::Future
		);

		if ( $request['preview'] == 'true' ) { // use query parameter to indicate preview
			$query_args['post_status'] = array_merge( $query_args['post_status'], $status );
		}

		$query = new \WP_Query( $query_args ); // query

		if ( empty ( $query->posts ) || ! sizeof( $query->posts ) > 1 ) {
			return $this->routes->response_404(); // this will return null
		}

		$ctrl    = new \WP_REST_Posts_Controller( $query->post->post_type );
		$request = new \WP_REST_Request();
		$request->set_param( 'id', $query->post->ID );

		// allow to filter mango post
		return apply_filters( 'wp_mango_rest_post', $ctrl->get_item( $request ) );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function post_by_permalink( \WP_REST_Request $request ): \WP_REST_Response
	{
		$post_id = url_to_postid( $request->get_param( 'permalink' ) );

		if ( $post_id === 0 ) {
			return $this->routes->response_404();
		}

		$post = get_post( $post_id );

		$ctrl    = new \WP_REST_Posts_Controller( $post->post_type );
		$request = new \WP_REST_Request();
		$request->set_param( 'id', $post->ID );

		return apply_filters( 'wp_mango_routes_posts_post_by_permalink', $ctrl->get_item( $request ) );
	}
}
