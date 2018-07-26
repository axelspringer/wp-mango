<?php

namespace AxelSpringer\WP\Mango\Routes;
use AxelSpringer\WP\Bootstrap\Plugin\Setup;

use function AxelSpringer\WP\Mango\wp_mango_validate_token;

/**
 * Class JWT
 * 
 * Defines the customizer REST Endpoint in Mango.
 * This allows to retrieve customizer settings by
 * an authorized user.
 *
 * @package Wp\Mango\Routes
 */
class JWT implements Route {

	/**
	 * Anonymous
	 */
	const ANONYMOUS = 'anonymous';
	
	/**
	 * @var string
	 */
	protected $token = 'jwt/token';

	/**
	 * @var string
	 */
	protected $validate = 'jwt/validate';

	/**
	 * @var string
	 */
	protected $secret_key = null;

	/**
	 * Posts constructor.
	 */
	public function __construct( Setup &$setup )
	{
		if ( $setup->options['wp_mango_jwt_secret_key'] ) {
			$this->secret_key = $setup->options['wp_mango_jwt_secret_key'];
		}
		
		// set auth secret by env variable
		if ( defined( 'WP_MANGO_JWT_SECRET_KEY' ) ) {
			$this->secret_key = WP_MANGO_JWT_SECRET_KEY;
		}
	}

	/**
	 * @param Routes $routes
	 */
	public function configure( Routes $routes )
	{
		$routes->create( $this->token, array( &$this, 'generate_token' ), [], \WP_REST_Server::CREATABLE, false );
		$routes->create( $this->validate, array( &$this, 'validate_token' ), [], \WP_REST_Server::CREATABLE, false );
	}

	/**
	 * Create a token via REST
	 * 
	 * @return \WP_REST_Response
	 */
	public function generate_token( $request )
	{
		// if there is no secret key
        if ( ! $this->secret_key ) {
            return new \WP_Error(
                'wp_mango_bad_config',
                __( 'Mango is not configured properly', 'wp-mango' ),
                array(
                    'status' => 403,
                )
            );
		}

		// if there is there is username, password
        $username = $request->get_param( 'username' );
		$password = $request->get_param( 'password' );
		
        // try to authenticate user
        $user = wp_authenticate( $username, $password );
		
		// if the user could not be authenticated
        if ( is_wp_error( $user ) ) {
            $error_code = $user->get_error_code();
            return new \WP_Error(
                $error_code,
                $user->get_error_message( $error_code) ,
                array(
                    'status' => 403,
                )
            );
		}
		
		// use current time for issuing
		$issued = time();

		// generate token for user
		$token = wp_mango_generate_token( $issued, $this->secret_key, $user->data->ID );

		// construct JSON response data
        $data = array(
            'token' => $token,
            'user_email' => $user->data->user_email,
            'user_nicename' => $user->data->user_nicename,
            'user_display_name' => $user->data->display_name,
		);
		
		return new \WP_REST_Response( apply_filters( 'wp_mango_token_response', $data, $user ) );
	}

	public function validate_token()
	{
		// if there is no secret key
        if ( ! $this->secret_key ) {
            return new \WP_Error(
                'wp_mango_bad_config',
                __( 'Mango is not configured properly', 'wp-mango' ),
                array(
                    'status' => 403,
                )
            );
		}

		// use authorization header
		$auth = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? $_SERVER['HTTP_AUTHORIZATION'] : false;

		// check for redirect bearer
		$auth = ! $auth && isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : $auth;

		// check for auth
		if ( ! $auth ) {
            return new \WP_Error(
                'wp_mango_no_auth_header',
                __( 'Authorization header not found.', 'wp-mango' ),
                array(
                    'status' => 403,
                )
            );
		}
		
		// verify the Bearer format
		list( $token ) = sscanf( $auth, 'Bearer %s' );
        if ( ! $token ) {
            return new \WP_Error(
                'wp_mango_bad_auth_header',
                __( 'Authorization header malformed.', 'wp-mango' ),
                array(
                    'status' => 400,
                )
            );
		}

		// valid a token
		$valid = wp_mango_validate_token( $token, $this->secret_key );	
		
		// if this is not valid
		if ( is_wp_error( $valid ) ) {
            $error_code = $valid->get_error_code();
            return new \WP_Error(
                $error_code,
                $valid->get_error_message( $error_code ) ,
                array(
                    'status' => 403,
                )
            );
		}

		if ( $valid ) {
			return new \WP_REST_Response( array( 'code' => 'wp_mango_valid_token', 'status' => 200 ) ); 
		}

		return new WP_Error(
			'wp_mango_bad_request',
			__( 'Could not authenticate.', 'wp-mango' ),
			array(
				'status' => 400,
			)
		);

	}
}
