<?php

namespace AxelSpringer\WP\Mango;

use AxelSpringer\WP\Mango\Services\Credentials;
use AxelSpringer\WP\Bootstrap\Plugin\Setup;
use AxelSpringer\WP\Mango\Routes\Customizer;
use AxelSpringer\WP\Mango\Routes\Nav;
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
        'post_link',
	    'page_link',
	    'attachment_link',
        'post_type_link',
        'get_the_guid'
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
            }
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
     * Rewrite url
     */
    public function dynamic_relative_url( $url, $post )
    {
        $url = parse_url( $url ); // should be replaced
        if ( $url === false ) { // break on wrong url
            return $url;
        }

        $rel_url = $url['path']; // use path

        if ( $url['query'] !== '' ) {
            $rel_url .= '?' . $url['query']; // attach query strings
        }

        return $rel_url;
    }

    /**
     * noop
     */
    protected function __clone()
    {

    }
}
