<?php

namespace AxelSpringer\WP\Mango\Routes;

use AxelSpringer\WP\Mango\PostType;
use AxelSpringer\WP\Mango\PostStatus;

/**
 * Class Media
 *
 * @package Wp\Mango\Routes
 */
class Media implements Route {
	/**
	 * @var string
	 */
	protected $base = 'media';

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
		$routes->create( $this->base . '/(?P<id>\d+)', [ $this, 'get_item' ] );
	}

	/**
	 * Get media
	 * 
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_item( \WP_REST_Request $request )
	{
		$query_args = array(
			'post__in'			=> array( $request->get_param( 'id' ) ),
			'post_status' 		=> PostStatus::Inherit,
            'post_type'			=> PostType::Attachment,
            'post_mime_type' 	=> 'image/jpeg,image/gif,image/jpg,image/png'
		);

		$query = new \WP_Query( $query_args ); // query

		if ( empty ( $query->posts ) || ! sizeof( $query->posts ) > 1 ) {
			return $this->routes->response_404(); // this will return null
		}

		$ctrl    = new \WP_REST_Attachments_Controller( $query->post->post_type );
		$request = new \WP_REST_Request();
		$request->set_param( 'id', $query->post->ID );		

		// allow to filter mango post
		return apply_filters( 'wp_mango_rest_post', $ctrl->prepare_item_for_response( $query->post, $request ) );
	}
}
