<?php

namespace Wp\Mango\Routes;

use Wp\Mango\Services\Credentials;

/**
 * Class Routes
 *
 * @package Wp\Mango\Routes
 */
class Routes {
	const REST_NAMESPACE = 'mango/v1';

	/**
	 * @var Credentials
	 */
	protected $credentials;

	/**
	 * Routes constructor.
	 *
	 * @param Credentials $credentials
	 */
	public function __construct( Credentials $credentials ) {
		$this->credentials = $credentials;

		add_filter( 'rest_authentication_errors', [ $this, 'permissions_check' ] );
	}

	/**
	 * @param Route $route
	 */
	public function configure( Route $route ) {
		$route->configure( $this );
	}

	/**
	 * @param string $route
	 * @param callable $callback
	 *
	 * @return bool
	 */
	public function get( string $route, callable $callback ): bool {
		return register_rest_route(
			Routes::REST_NAMESPACE,
			$route,
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => $callback,
				'permission_callback' => [ $this, 'permissions_check' ]
			]
		);
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function response_404() {
		return new \WP_REST_Response( null, \WP_Http::NOT_FOUND );
	}

	/**
	 * @param null $data
	 * @param int $status
	 * @param array $headers
	 *
	 * @return \WP_REST_Response
	 */
	public function response( $data = null, $status = 200, $headers = [] ) {
		return new \WP_REST_Response( $data, $status, $headers );
	}

	/**
	 * @return bool|\WP_Error
	 */
	public function permissions_check() {
		$nonce  = $_SERVER['HTTP_X_WP_NONCE'] ?? null;
		$token  = $_SERVER['HTTP_X_MANGO_TOKEN'] ?? null;
		$secret = $_SERVER['HTTP_X_MANGO_SECRET'] ?? null;

		// by pass if nonce is set
		if ( $nonce !== null ) {
			return true;
		}

		$current_user = wp_get_current_user();

		// bypass if logged in user
		if ( ! is_null( $current_user ) && $current_user->ID !== 0 ) {
			return true;
		}

		if ( !$this->credentials->is_valid_token( $token )
		     || !$this->credentials->is_valid_secret( $secret ) ) {
			return new \WP_Error( 'invalid_credentials', 'Invalid Credentials', array( 'status' => 403 ) );
		}

		return true;
	}

}
