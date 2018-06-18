<?php

namespace AxelSpringer\WP\Mango;

use AxelSpringer\WP\Mango\Services\Credentials;
use AxelSpringer\WP\Bootstrap\Plugin\Setup;
use AxelSpringer\WP\Mango\Routes\Customizer;
use AxelSpringer\WP\Mango\Routes\Nav;
use AxelSpringer\WP\Mango\Helpers;
use AxelSpringer\WP\Mango\Routes\Posts;
use AxelSpringer\WP\Mango\Routes\Routes;

/**
 * Actions Class
 *
 * @package AxelSpringer\WP\Actions
 */
class Filters
{

    /**
     * Setup
     */
    public $setup;

    /**
     * Post Filters
     */
    public $link_filters = [
	    'attachment_link',
        'page_link',
        'category_link',
        'tag_link',
        'get_the_guid',
        'post_link',
        'post_type_link'
    ];

    /**
     * Category Filters
     */
    public $category_link_filters = [
        'pre_term_link'
    ];

    /**
     * Actions constructor
     *
     */
    public function __construct( Setup &$setup )
    {
        // use setup
        $this->setup = $setup;
        // adding post url filters
        if ( $this->setup->options['wp_mango_rewrite_url'] &&
            ! ( is_admin() || ( defined('DOING_AJAX') && DOING_AJAX ) ) ) {
                $this->add_filters( $this->link_filters, array( &$this, 'dynamic_relative_url' ) );
                $this->add_filters( $this->category_link_filters, array( &$this, 'category_link' ) );
            }
    
        // $this->add_filters( array( 'get_preview_post_link' ), array( &$this, 'get_preview_post_link' ), 10, 4 );
        $this->add_filters( array( 'get_sample_permalink' ), array( &$this, 'get_sample_permalink' ), 99, 4 );
        $this->add_filters( array( 'preview_post_link' ), array( &$this, 'preview_post_link' ) );
        $this->add_filters( array( 'post_link' ), array( &$this, 'post_link' ), 99, 2 );
        $this->add_filters( array( 'query_vars' ), array( &$this, 'add_query_vars' ) );
    }

    /**
     * 
     */
    public function add_query_vars( $query_vars )
    {
        if ( ! empty( $this->setup->options['wp_mango_health_check'] ) ) {
            $query_vars[] = 'health';
        }
        return $query_vars;
    }

    /**
     * Preview post link
     */
    public function post_link( $url, $post )
    {
        if ( ! is_admin() || empty( $this->setup->options['wp_mango_preview_url'] ) )
            return $url; // just return if not preview, or if not admin

        return Helpers::replace_url( $url, $this->setup->options['wp_mango_preview_url'] );
    }

    /**
     * Preview post link
     */
    public function get_sample_permalink( $url, $post, $title, $name )
    {
        if ( empty( $this->setup->options['wp_mango_preview_url'] ) )
            return $url; // just return the link

        $url[0] = Helpers::replace_url( $url[0], $this->setup->options['wp_mango_preview_url'] );

        return $url;
    }

    /**
     * Preview post link
     */
    public function preview_post_link( string $url ): string
    {
        if ( empty( $this->setup->options['wp_mango_preview_url'] ) )
            return $url; // just return the link

        return Helpers::replace_url( $url, $this->setup->options['wp_mango_preview_url'] );
    }

    /**
     * Register filter to relevant hook
     * 
     */
    public function add_filters( $filters, $func, $priority = 10, $args = 2 )
    {
        foreach( $filters as $filter ) {
            add_filter( $filter, $func, $priority, $args );
        }
    }

    /**
     * Category Link Filter
     */
    public function category_link( $url, $cat )
    {
        $category_base = 'category';
        $base = get_option( 'category_base', false );

        if ( $base === false || $base === '' ) {
            return $this->dynamic_relative_url( str_replace( '/' . $category_base, '', $url ), null );
        }

        return $url;
    }

    /**
     * Rewrite url
     */
    public function dynamic_relative_url( $url, $post )
    {
        if ( strpos( $url, get_site_url() ) === false ) {
			return $url;
		}

        $url = parse_url( $url ); // should be replaced
        if ( $url === false ) { // break on wrong url
            return $url;
		}

        $rel_url = $url['path']; // use path
        if ( ! empty( $url['query'] ) ) {
            $rel_url .= '?' . $url['query']; // attach query strings
        }

        return untrailingslashit( $rel_url );
    }

    /**
     * noop
     */
    protected function __clone()
    {

    }
}
