<?php

namespace AxelSpringer\WP\Mango\Routes;

use AxelSpringer\WP\Mango\PostStatus;
use AxelSpringer\WP\Mango\PostType;

/**
 * Class Slugs
 *
 * @package Wp\Mango\Routes
 */
class Slugs implements Route {
	/**
	 * @var string
	 */
	protected $base = 'slugs';

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
		$routes->create( $this->base, [ $this, 'get_items' ], $this->get_collection_params() );
	}

	/**
	 * Get collection params
	 * 
	 */
	public function get_collection_params()
	{
		$query_params = array();

		$query_params['preview'] = array(
			'description'       => __( 'Query for posts that have a different status then publish.' ),
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

		return apply_filters( 'wp_mango_rest_posts_collection_params', $query_params, $post_type );
	}

	/**
	 * Get items
	 * 
	 */
	public function get_items( \WP_REST_Request $request )
	{
		// parameters to allows for request
		$args = array(
			'preview',
			'slug'
		);

		// map parameters
		$parameter_mappings = array(
			'author'         => 'author__in',
			'author_exclude' => 'author__not_in',
			'exclude'        => 'post__not_in',
			'include'        => 'post__in',
			'menu_order'     => 'menu_order',
			'offset'         => 'offset',
			'order'          => 'order',
			'orderby'        => 'orderby',
			'page'           => 'paged',
			'parent'         => 'post_parent__in',
			'parent_exclude' => 'post_parent__not_in',
			'search'         => 's',
			'slug'           => 'post_name__in',
			'status'         => 'post_status',
		);

		$status = array( // extended status
			PostStatus::Draft,
			PostStatus::AutoDraft,
			PostStatus::Future
		);

		$query_args = [
			'post_type' => 'any', // fetch for all
			'status'	=> array( PostStatus::Publish )
		];

		// map available parameters
		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			$query_args[ $wp_param ] = $request[ $api_param ];
		}

		// if there is a preview requested, extend visibility
		if ( $request['preview'] == 'true' ) { // merge preview
			$query_args['status'] = array_merge( array( $query_args['status'] ), $status );
		}

		$query 	= new \WP_Query( $query_args ); // query
		$posts 	= [];

		// mimic post fetch
		foreach ( $query->posts as $post ) {
			$ctrl		= new \WP_REST_Posts_Controller( $post->post_type );
			$data    	= $ctrl->prepare_item_for_response( $post, $request );
			$posts[] 	= $ctrl->prepare_response_for_collection( $data );
		}

		// response
		$response	= new \WP_REST_Response();
		$response->set_data( $posts );

		// filter mango rest posts
		return apply_filters( 'wp_mango_rest_posts', $response );
	}
}
