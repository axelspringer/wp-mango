<?php

namespace AxelSpringer\WP\Mango;

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
