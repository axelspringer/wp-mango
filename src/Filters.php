<?php

namespace AxelSpringer\WP\Mango;

use AxelSpringer\WP\Mango\Services\Credentials;
use AxelSpringer\WP\Bootstrap\Plugin\Setup;
use AxelSpringer\WP\Mango\Routes\Customizer;
use AxelSpringer\WP\Mango\Routes\Nav;
use AxelSpringer\WP\Mango\Helpers;
use AxelSpringer\WP\Mango\Routes\Posts;
use AxelSpringer\WP\Mango\Routes\Routes;

use function AxelSpringer\WP\Mango\leadingslashit;
use function AxelSpringer\WP\Mango\unparse_url;
use function AxelSpringer\WP\Mango\wp_mango_generate_token;

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
        'term_link',
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
        $this->add_filters( array( 'preview_post_link' ), array( &$this, 'preview_post_link' ), 99, 2 );
        $this->add_filters( array( 'post_link' ), array( &$this, 'post_link' ), 99, 2 );
        $this->add_filters( array( 'query_vars' ), array( &$this, 'add_query_vars' ) );
            
        // filter page links
        $this->add_filters( array( 'page_link' ), array( &$this, 'flatten_page_link' ), 99, 2 );
        
        // filter post enter data
		$this->add_filters( array( 'wp_insert_post_data' ), array( &$this, 'remove_site_url_from_href' ), 99, 2 );
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
     * Flatten page links
     * 
     */
    public function flatten_page_link( $url, $page )
    {
        if ( empty( $this->setup->options['wp_mango_filters_page_link'] ) )
            return $url; // just return if not preview, or if not admin
        
        $url = parse_url( $url ); // should be replaced
        if ( $url === false ) { // break on wrong url
            return $url;
        }

        // we try to elimnate the path of the url
        $parts = array_filter( explode( '/', $url['path'] ) );
        $url['path'] = end( $parts );

        // if function exists
        if ( function_exists( '\pll_current_language' ) ) { // if there is polylang
            $url['path'] = implode( "/", array( array_shift( $parts ), $url['path'] ) );
        }

        // return url wuth
        return leadingslashit( unparse_url( $url ) );
    }
	
	/**
     * Removes the site URL from the href properties inside the post content
     */
    public function remove_site_url_from_href( $data , $postarr ) {
        if ( isset( $data ) && isset( $data['post_content'] ) ) {
            $regex = 'href=\"' . get_site_url();

            $data['post_content'] = str_replace( $regex, 'href=\"', $data['post_content'] );
        }

        return $data;
    }

    /**
     * Preview post link
     */
    public function post_link( $url, $post )
    {
        if ( ! is_admin() || empty( $this->setup->options['wp_mango_sample_url'] ) )
            return $url; // just return if not preview, or if not admin

        return Helpers::replace_url( $url, $this->setup->options['wp_mango_sample_url'] );
    }

    /**
     * Preview post link
     */
    public function get_sample_permalink( $url, $post, $title, $name )
    {
        if ( empty( $this->setup->options['wp_mango_preview_sample'] )
            || empty( $this->setup->options['wp_mango_preview_sample_url'] ) ) {
            return $url; // just return the link
        }

        $url[0] = Helpers::replace_url( $url[0], $this->setup->options['wp_mango_preview_sample_url'] );

        return $url;
    }

    /**
     * Preview post link
     */
    public function preview_post_link( string $url, $post ): string
    {
        if ( empty( $this->setup->options['wp_mango_preview'] )
            ||  empty( $this->setup->options['wp_mango_preview_url'] ) ) {
                return $url; // just return the link
            }
        
        // rules to replace
        $rewrite_rules = [
            '%post_type%'   => $post->post_type,
            '%id%'          => $post->ID
        ];
        $url = $this->setup->options['wp_mango_preview_url'];
        $current_user = wp_get_current_user();

        // replace in preview
        foreach( $rewrite_rules as $rule => $replace ) {
            $url = str_replace( $rule, $replace, $url );
        }

        if ( ! empty( $this->setup->options['wp_mango_jwt'] )
            &&  ! empty( $this->setup->options['wp_mango_jwt_secret_key'] )
            && $current_user !== 0 ) {
                $token = wp_mango_generate_token( time(), $this->setup->options['wp_mango_jwt_secret_key'], $current_user->ID );
                return $url . '?token=' . $token;
        }
 
        return $url; // just return url
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

        if ( ! empty( $this->setup->options['wp_mango_filters_category_parent'] ) ) {
            global $wp_rewrite;
            $category_nicename = $cat->slug;
            $url = str_replace( '%category%', $category_nicename, $url );
        }        

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
