<?php

namespace AxelSpringer\WP\Mango\Routes;

use AxelSpringer\WP\Mango\PostStatus;

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

		// most general route
		$routes->get( $this->base, [ $this, 'get_items' ], $this->get_collection_params() );

		// specific legacy routes
		$routes->get( $this->base . '/post-by-permalink', [ $this, 'post_by_permalink' ] );
		$routes->get( $this->base . '/post/(?P<id>\d+)', [ $this, 'get_item' ] );
	}

	/**
	 * Get collection params
	 * 
	 */
	public function get_collection_params()
	{
		$query_params = array();

		$query_params['preview'] = array(
			'description'       => __( 'Query for posts with a different status then publish' ),
			'type'              => 'array',
			'items'             => array( 
				'type' => 'boolean',
			),
			'sanitize_callback' => 'AxelSpringer\WP\Mango\wp_parse_preview',
		);

		$query_params['slug'] = array(
			'description'       => __( 'Limit result set to posts with one or more specific slugs.' ),
			'type'              => 'array',
			'items'             => array( 
				'type' => 'string',
			),
			'sanitize_callback' => '\wp_parse_slug_list',
		);

		$query_params['type'] = array(
			'description'       => __( 'Type of the post to limit the request to.' ),
			'type'              => 'array',
			'items'             => array( 
				'type' => 'string',
			)
		);

		return apply_filters( 'wp_mango_rest_posts_collection_params', $query_params, $post_type );
	}

	/**
	 * Get items
	 * 
	 */
	public function get_items( \WP_REST_Request $request )
	{
		$query_args = array(
			'post_status' 	=> array( PostStatus::Publish ), // default is only published
			'post_type'		=> 'any'
		);

		$registered = $this->get_collection_params();

		$parameter_mappings = array(
			'type' 	 	=> 'post_type',
			'slug'      => 'post_name__in',
		);

		// map request parameters to query args
		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$query_args[ $wp_param ] = $request[ $api_param ];
			}
		}

		if ( $request['preview'] == 'true' ) { // merge preview
			$query_args['post_status'] = array_merge(
				$query_args['post_status'],
				array( 'draft', 'pending', 'future' )
			);
		}

		$query = new \WP_Query( $query_args );

		if ( empty ( $query->posts ) || sizeof( $query->posts ) > 1 ) {
			return $this->routes->response_404(); // if there is no post, or the post is not singular
		}

		// $post = array_shift( $query->posts );
		$response = new \WP_REST_Response();
		$response = rest_ensure_response( array_map( function( $post ) {
			return $this->get_post( $post->ID );
		}, $query->posts ) );

		return $response;

		$ctrl    = new \WP_REST_Posts_Controller( $query->post->post_type );
		$request = new \WP_REST_Request();
		$request->set_param( 'id', $query->post->ID );

		// allow to filter mango post
		return apply_filters( 'wp_mango_posts', $ctrl->get_item( $request ) );
	}

	/**
	 * Get all posts to an id
	 * 
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_item( \WP_REST_Request $request ): \WP_REST_Response
	{
		$post_status = array( 'publish' ); // by default only show publish

		if ( $request['preview'] == 'true' ) { // use query parameter to indicate preview
			$post_status = array_merge( $post_status, array( 'draft', 'pending', 'future' ) );
		}

		$query_args = array(
			'p'         => $request->get_param( 'id' ), // ID of a page, post, or custom type
			'post_type' => 'any', // find all posts
			'post_status' => $post_status
		);

		$query = new \WP_Query( $query_args );

		if ( empty ( $query->posts ) || ! $query->is_singular )
			return $this->routes->response_404(); // this will return null

		$ctrl    = new \WP_REST_Posts_Controller( $query->post->post_type );
		$request = new \WP_REST_Request();
		$request->set_param( 'id', $query->post->ID );

		// allow to filter mango post
		return apply_filters( 'wp_mango_post', $ctrl->get_item( $request ) );
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
		//$_GET['_embed'] = true;
		$request->set_param( 'id', $post->ID );

		return apply_filters( 'wp_mango_routes_posts_post_by_permalink', $ctrl->get_item( $request ) );
	}

	/**
	 * Get the post, if the ID is valid.
	 *
	 * @since 4.7.2
	 *
	 * @param int $id Supplied ID.
	 * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
	 */
	public function get_post( $id )
	{
		$error = new WP_Error( 'rest_post_invalid_id', __( 'Invalid post ID.' ), array( 'status' => 404 ) );
		if ( (int) $id <= 0 ) {
			return $error;
		}
		$post = get_post( (int) $id );
		if ( empty( $post ) || empty( $post->ID ) || $this->post_type !== $post->post_type ) {
			return $error;
		}
		return $post;
	}
}
