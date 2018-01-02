<?php

final class Mango {

    private $settings_title;
    private $settings_menu_title;

    static $settings_page;
    static $slug = 'mango';
    static $version;
    static $rest_namespace = 'mango/v1'; //

    public $enabled = false;
    public $menus = false;

    function __construct( $plugin_file_path, $version = null, $slug = null ) {
      if ( ! is_null( $slug ) ) {
        self::$slug = $slug;
      }

      if ( ! is_null( $version ) ) {
        self::$version = $version;
      }

      $this->init( $plugin_file_path );
    }
 
    function init( $plugin_file_path ) {
      self::$settings_page         = self::$slug;
      $this->settings_title        = __( 'mango', self::$slug );
      $this->settings_menu_title   = __( 'mango', self::$slug );
    
      $settings = new Mango_Settings();

      if ( ! get_option( 'permalink_structure' ) ) { // REST only available
        add_action( 'admin_notices', array( &$this, 'admin_notice_enable_permalinks') );
        return; // todo: should display error messsage
      }

      $this->get_options();
      $this->add_actions();
      
      global $mango;
      $mango = $this;

      // add_filter( 'upload_dir', array( &$this, 'filter_upload_dir' ) );
      // add_filter( 'pre_option_uploads_use_yearmonth_folders', '__return_null' );
      // // add_filter( 'plupload_init', array( &$this, 'plupload_init' ) );
      // add_filter('wp_handle_upload ', 'custom_upload_filter' );
      // add_filter( 'wp_handle_upload_prefilter', array( &$this, 'filter_upload_prefilter' ) );

      // add_action( 'delete_attachment', array( &$this, 'delete_attachment' ) );
    }

    private function get_options() {
      $this->enabled = get_option( 'mango_enabled' );
      $this->menus = get_option( 'mango_menus' );
    }

    private function add_actions() {
      if ( false === $this->enabled ) // safe
        return;

      // add action hooks
      add_action( 'rest_api_init', array( &$this, 'rest_api_init' ) );
    }

    public function rest_api_init() {
      if ( $this->menus ) { // if menus should be enabled
        $this->register_nav_menu();
        $this->register_nav_location();
        $this->register_nav_locations();
      }
    }

    public function register_nav_menu() {
      register_rest_route(
        self::$rest_namespace,
        '/nav/(?P<id>\d+)',
        array(
            'methods' => 'GET',
            'callback' => array( &$this, 'get_nav_menu' ),
            'id' => array(
              'validate_callback' => function($param, $request, $key) {
                return is_numeric( $param );
              }
            )
        )
      );
    }

    public function register_nav_location() {
      register_rest_route(
        self::$rest_namespace,
        '/locations/(?P<name>[a-zA-Z0-9\_]+)',
        array(
            'methods' => 'GET',
            'callback' => array( &$this, 'get_nav_menu_location' ),
            'name' => array(
              'validate_callback' => function($param, $request, $key) {
                return is_string( $param );
              }
            )
        )
      );
    }

    public function register_nav_locations() {
      register_rest_route(
        self::$rest_namespace,
        '/locations',
        array(
            'methods' => 'GET',
            'callback' => array( &$this, 'get_nav_menu_locations' )
        )
      );
    }

    public function get_nav_menu_locations() {
      return get_nav_menu_locations();
    }

    public function get_nav_menu_location( $data ) {
      $locations = get_nav_menu_locations();

      return ! array_key_exists( $data['name'], $locations ) 
        ? new stdClass()
        : $locations[ $data['name'] ];
    }

    public function get_nav_menu( $data ) {
        $menu = wp_get_nav_menu_object( $data['id'] );
        return $menu;

        return ! $menu ? new stdClass() : $menu ;
    }

    public function admin_notice_enable_permalinks() {
      $message = __( 'Warning! Please, enable permalink structure to use Mango Plugin.' );
      $class = 'notice notice-warning is-dismissible';

      printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

    static function activation() {
      return;
    }

    static function deactivation() {
      return;
    }

    protected function __clone() {
      // noop
    }
}
