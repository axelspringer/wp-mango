<?php

final class Mango {

    private $settings_title;
    private $settings_menu_title;

    static $settings_page;
    static $slug = 'mango';
    static $version;
    static $rest_namespace = 'mango/v1';

    public $enabled = false;
    public $nav = false;

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
      $this->settings_title        = __( 'Mango', self::$slug );
      $this->settings_menu_title   = __( 'Mango', self::$slug );
    
      $settings = new Mango_Settings();

      if ( ! get_option( 'permalink_structure' ) ) { // REST only available
        add_action( 'admin_notices', array( &$this, 'admin_notice_enable_permalinks') );
        return; // todo: should display error messsage
      }

      $this->get_options();
      $this->add_actions();
      
      global $mango;
      $mango = $this;
    }

    private function get_options() {
      $this->enabled = get_option( 'mango_enabled' );
      $this->nav = get_option( 'mango_nav' );
    }

    private function add_actions() {
      if ( false === $this->enabled ) // safe
        return;

      // add action hooks
      add_action( 'rest_api_init', array( &$this, 'rest_api_init' ) );
    }

    public function rest_api_init() {
      if ( $this->nav ) { // if menus are enabled
        $this->register_nav_menu();
        $this->register_nav_menu_items();
        $this->register_nav_location();
        $this->register_nav_locations();
      }
    }

    public function register_nav_menu() {
      register_rest_route(
        self::$rest_namespace,
        '/nav/menus/(?P<id>\d+)',
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

    public function register_nav_menu_items() {
      register_rest_route(
        self::$rest_namespace,
        '/nav/items/(?P<id>\d+)',
        array(
          'methods' => 'GET',
          'callback' => array( &$this, 'get_nav_menu_items' ),
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
        '/nav/locations/(?P<name>[a-zA-Z0-9\_]+)',
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
        '/nav/locations',
        array(
            'methods' => 'GET',
            'callback' => array( &$this, 'get_nav_menu_locations' )
        )
      );
    }

    public function get_nav_menu_locations() {
      return get_nav_menu_locations();
    }

    public function get_nav_menu_items( $data ) {
      $items = wp_get_nav_menu_items( $data['id'] );
      
      if ( ! $items ) {
        return new WP_Error( 'no_menu', 'Invalid menu', array( 'status' => 404 ) );
      }

      return $items;
    }

    public function get_nav_menu_location( $data ) {
      $locations = get_nav_menu_locations();

      if ( ! array_key_exists( $data['name'], $locations ) ) {
        return new WP_Error( 'no_menu_location', 'Invalid menu Location', array( 'status' => 404 ) );
      }

      return $locations[ $data['name'] ];
    }

    public function get_nav_menu( $data ) {
        $menu = wp_get_nav_menu_object( $data['id'] );

        if ( ! $menu ) {
          return new WP_Error( 'no_menu', 'Invalid menu', array( 'status' => 404 ) );
        }

        return $menu ;
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
