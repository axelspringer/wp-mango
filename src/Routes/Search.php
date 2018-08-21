<?php

namespace AxelSpringer\WP\Mango\Routes;

use AxelSpringer\WP\Mango\PostStatus;
use AxelSpringer\WP\Mango\PostType;
use AxelSpringer\WP\Mango\Search\Result;

/**
 * Class Search
 *
 * @package Wp\Mango\Routes
 */
class Search implements Route {
	/**
	 * @var string
	 */
	protected $base = 'search';

	/**
	 * @var Routes
	 */
	protected $routes;

	/**
     * Setup
	 * 
	 * @var Setup
     */
	protected $setup;
	
	/**
	 * Results per page
	 * 
	 * @var int
	 */
	protected $results_per_page = 1;

	/**
	 * Posts constructor.
	 */
	public function __construct( &$setup )
	{
		$this->setup = $setup;
	}

	/**
	 * Configure routes
	 * 
	 * @param Routes $routes
	 */
	public function configure( Routes $routes ) {
		$this->routes = $routes;

		// specify search route
		$routes->create( $this->base . '/(?P<search>[a-zA-Z0-9\%+]*)[/]*(?P<page>\d*)', array( &$this, 'get_search' ) );
	}

	/**
	 * Search Wordpress content by provided query 
	 * 
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_search( \WP_REST_Request $request ): \WP_REST_Response
	{
		// create new search result
		$result = new Result();

		// extract parameters
		$search = $request->get_param ( 'search' );
		$page = $request->get_param ( 'page' );

		// curate search
		$search = implode( ' ', explode( '+', trim( urldecode( $search ) ) ) );

		// results per page
		$page = intval( $page );
		$page = !$page ? 1 : $page;
		$results_per_page = (int) $this->setup->options['wp_mango_search_results_per_page'];
		$results_per_page = ! $results_per_page ? $this->results_per_page : $results_per_page;

		// set query
		$result->per_page = $results_per_page;
		$result->page = $page;

		// prepare query
		$query  = new \WP_Query();
        $items = $query->query ( array (
            'paged'          => $page,
            'post_type'      => 'any',
            'posts_per_page' => $results_per_page,
            's'              => $search
		) );

		// prepare the items
		foreach( $items as $item ) {
			$controller = new \WP_REST_Posts_Controller( $item->post_type );

			if ( ! $controller->check_read_permission( $item ) ) {
				continue;
			}

			$data = $controller->prepare_item_for_response( $item, $request );
			$result->result[] = $controller->prepare_response_for_collection( $data );
		}

		// return results
        return new \WP_REST_Response( $result, 200 );
	}
}
