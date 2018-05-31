<?php

namespace AxelSpringer\WP\Mango;

/**
 * Helpers Class
 *
 * @package AxelSpringer\WP\Helpers
 */
abstract class Helpers
{
    public function replace_url( string $url, string $new_url )
    {
        $url = parse_url( $url );
        if ( $url === false ) {
            return $url;
        }

        return untrailingslashit( $new_url )
            . ( isset($url['path']) ? "{$url['path']}" : '' )
            . ( isset($url['query']) ? "?{$url['query']}" : '' )
            . ( isset($url['fragment']) ? "#{$url['fragment']}" : '' );
    }
}
