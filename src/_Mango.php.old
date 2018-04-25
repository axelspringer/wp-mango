<?php

namespace Wp\Mango;

use Wp\Mango\Admin\MangoSettings;
use Wp\Mango\Routes\Customizer;
use Wp\Mango\Routes\Nav;
use Wp\Mango\Routes\Posts;
use Wp\Mango\Routes\Routes;
use Wp\Mango\Services\Credentials;

/**
 * Class Mango
 */
class Mango {
	static $settings_page;
	static $slug = 'mango';
	static $version;

	/**
	 * @var bool
	 */
	public $enabled = false;

	/**
	 * @var bool
	 */
	public $nav = false;

	/**
	 * @var bool
	 */
	public $customizer = false;

	/**
	 * @var Credentials
	 */
	protected $credentials_service;

	/**
	 * Mango constructor.
	 *
	 * @param $plugin_file_path
	 * @param null $version
	 * @param null $slug
	 */
	public function __construct( $plugin_file_path, $version = null, $slug = null ) {
		if ( ! is_null( $slug ) ) {
			self::$slug = $slug;
		}

		if ( ! is_null( $version ) ) {
			self::$version = $version;
		}

		$this->init( $plugin_file_path );
	}

	/**
	 * Initializing the plugin
	 *
	 * @param [type] $plugin_file_path
	 *
	 * @return void
	 */
	public function init( $plugin_file_path ) {
		self::$settings_page       = self::$slug;
		$this->settings_title      = __( 'Mango', self::$slug );
		$this->settings_menu_title = __( 'Mango', self::$slug );

		$settings = new MangoSettings();

		if ( false === get_option( 'permalink_structure' ) || empty( get_option( 'permalink_structure' ) ) ) { // REST only available
			add_action( 'admin_notices', [ $this, 'admin_notice_enable_permalinks' ] );

			return; // todo: should display error message
		}

		$this->credentials_service = new Credentials();
		$this->credentials_service->generate_credentials();

		$this->get_options();
		$this->add_actions();
	}

	/**
	 * Hook into WP Rest API initialization
	 *
	 * @return void
	 */
	public function rest_api_init() {
		$routes = new Routes( $this->credentials_service );

		if ( $this->nav ) { // if menus are enabled
			$routes->configure( new Nav() );
		}

		if ( $this->customizer ) { //if customizer is enabled
			$routes->configure( new Customizer() );
		}

		$routes->configure( new Posts() );
	}

	public function admin_notice_enable_permalinks() {
		$message = __( 'Warning! Please, enable permalink structure to use Mango Plugin.' );
		$class   = 'notice notice-warning is-dismissible';

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

	static public function activation() {

	}

	static public function deactivation() {

	}

	/**
	 * Get the relevant WP Options
	 *
	 * @return void
	 */
	private function get_options() {
		$this->enabled    = get_option( 'mango_enabled' );
		$this->nav        = get_option( 'mango_nav' );
		$this->customizer = get_option( 'mango_customizer' );
	}

	/**
	 * Add WP Actions
	 *
	 * @return void
	 */
	private function add_actions() {
		if ( false === $this->enabled ) {
			return;
		}

		// add action hooks
		add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );
	}
}
