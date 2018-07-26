<?php

namespace AxelSpringer\WP\Mango;

use \Firebase\JWT\JWT as FJWT;

/**
 * Validate a token
 * 
 * @return \WP_Error | bool
 */
function wp_mango_validate_token( $token, $secret_key )
{
	// try to decode the token
	try {
		$token = FJWT::decode( $token, $secret_key, array( 'HS256' ) );

		// validate token
		if ( $token->iss != get_bloginfo('url') ) {
			/** The iss do not match, return error */
			return new WP_Error(
				'wp_mango_bad_iss',
				__( 'The iss do not match with this server', 'wp-mango' ),
				array(
					'status' => 403,
				)
			);
		}

		if ( !isset( $token->data->user->id ) ) {
			return new WP_Error(
				'wp_mango_bad_request',
				__( 'User ID not found in the token', 'wp-mango' ),
				array(
					'status' => 400,
				)
			);
		}

		// resolve authentication
		return true;
	} catch( \Exception $e ) {
		// catch execption
		return new \WP_Error(
			'wp_mango_token_invalid',
			$e->getMessage(),
			array(
				'status' => 403,
			)
		);
	}

	// answer with not valid by default
	return false;
}

/**
 * Generate a token
 * 
 * @return string
 */
function wp_mango_generate_token( $issued, $secret_key, $id = JWT::ANONYMOUS )
{
	// apply filters
	$expire = apply_filters( 'wp_mango_token_expire', $issued + (MINUTE_IN_SECONDS * 5), $issued );
	$before = apply_filters( 'wp_mango_token_before', $issued, $issued );

	// construct token
	$token = array(
		'iss' => get_bloginfo( 'url' ),
		'iat' => $issued,
		'nbf' => $before,
		'exp' => $expire,
		'data' => array(
			'user' => array(
				'id' => $id
			),
		),
	);

	// create an return token
	return FJWT::encode( apply_filters( 'wp_mango_token_before_sign', $token, $user), $secret_key );
}

/**
 * Prepends a leading slash.
 *
 * Will remove leading forward and backslashes if it exists already before adding
 * a leading forward slash. This prevents double slashing a string or path.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * Opposite of {@see WordPress\trailingslashit()}.
 *
 * @param string $string What to add the leading slash to.
 * @return string String with leading slash added.
 */
function leadingslashit( $string )
{
	return '/' . unleadingslashit( $string );
}

/**
 * Removes leading forward slashes and backslashes if they exist.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * Opposite of {@see WordPress\untrailingslashit()}.
 *
 * @param string $string What to remove the leading slashes from.
 * @return string String without the leading slashes.
 */
function unleadingslashit( $string )
{
	return ltrim( $string, '/\\' );
}

/**
 * Unparses a parsed url
 *
 *
 * @param string $string The url object that should be unparsed.
 * @return string A string that represents the full url.
 */
function unparse_url( $parsed_url )
{ 
    $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
    $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
    $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
    $user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
    $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
    $pass     = ($user || $pass) ? "$pass@" : ''; 
    $path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
    $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
    
    return "$scheme$user$pass$host$port$path$query$fragment"; 
} 

/**
 * Sanitize preview argument
 * 
 */
function wp_parse_preview( $preview ) {
    return $preview === 'true';
}
