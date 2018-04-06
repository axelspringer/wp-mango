<?php

namespace Wp\Mango;
use Wp\Mango\Routes\Customizer;
use Wp\Mango\Routes\Posts;
use Wp\Mango\Routes\Routes;

/**
 * Class Mango
 */
class Mango
{
    private $settings_title;
    private $settings_menu_title;
    private $current_user;

    static $settings_page;
    static $slug = 'mango';
    static $version;
    static $rest_namespace = 'mango/v1';

    public $enabled = false;
    public $nav = false;

    /**
     * Mango constructor.
     *
     * @param $plugin_file_path
     * @param null $version
     * @param null $slug
     */
    public function __construct($plugin_file_path, $version = null, $slug = null)
    {
        if (!is_null($slug)) {
            self::$slug = $slug;
        }

        if (!is_null($version)) {
            self::$version = $version;
        }

        $this->init($plugin_file_path);
    }

    /**
     * Initializing the plugin
     *
     * @param [type] $plugin_file_path
     * @return void
     */
    public function init($plugin_file_path)
    {
        self::$settings_page = self::$slug;
        $this->settings_title = __('Mango', self::$slug);
        $this->settings_menu_title = __('Mango', self::$slug);

        $settings = new MangoSettings();

        if (false === get_option('permalink_structure') || empty(get_option('permalink_structure'))) { // REST only available
            add_action('admin_notices', array(&$this, 'admin_notice_enable_permalinks'));
            return; // todo: should display error message
        }

        // check for credentials
        $this->generate_credentials();

        $this->get_options();
        $this->add_actions();
        $this->add_filters();
    }

    /**
     * Add a surrogate user
     *
     * @return void
     */
    public function add_user()
    {
        $this->current_user = wp_get_current_user();

        if (!is_null($this->current_user) && $this->current_user->ID !== 0) {
            return;
        }

        $this->current_user = wp_set_current_user(9999, 'mango');
        $this->current_user->add_cap('manage_options');
    }

    /**
     * Hook into WP Rest API initialization
     *
     * @return void
     */
    public function rest_api_init()
    {
        $this->add_user(); // try adding user

        $routes = new Routes();

        if ($this->nav) { // if menus are enabled
            $this->register_nav_menu();
            $this->register_nav_menu_items();
            $this->register_nav_location();
            $this->register_nav_locations();
        }

        if ($this->customizer) { //if customizer is enabled
            $routes->configure(new Customizer());
        }

        $routes->configure(new Posts());
    }

    /**
     * @return bool
     */
    public function register_nav_menu():bool
    {
        return register_rest_route(
            self::$rest_namespace,
            '/nav/menus/(?P<id>\d+)',
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'get_nav_menu'],
                'permission_callback' => [$this, 'permissions_check']
            )
        );
    }

    /**
     * @return bool
     */
    public function register_nav_menu_items():bool
    {
        return register_rest_route(
            self::$rest_namespace,
            '/nav/items/(?P<id>\d+)',
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'get_nav_menu_items'],
                'permission_callback' => [$this, 'permissions_check']
            )
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function register_nav_location()
    {
        register_rest_route(
            self::$rest_namespace,
            '/nav/locations/(?P<name>[a-zA-Z0-9\_]+)',
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'get_nav_menu_location'],
                'permission_callback' => [$this, 'permissions_check']
            )
        );
    }

    /**
     * @return bool|\WP_Error
     */
    public function permissions_check()
    {
        $nonce = $_SERVER['HTTP_X_WP_NONCE'] ?? null;
        $token = $_SERVER['HTTP_X_MANGO_TOKEN'] ?? null;
        $secret = $_SERVER['HTTP_X_MANGO_SECRET'] ?? null;

        if ($nonce !== null) // by pass if nonce is set
            return false;

        if (!is_null($this->current_user) && $this->current_user->ID !== 0) //bypass if logged in user
            return false;

        if ($token !== get_option('mango_credentials_token')
            || $secret !== get_option('mango_credentials_secret')) // if not credentials
            return new \WP_Error('invalid_credentials', 'Invalid Credentials', array('status' => 403));

        return true;
    }

    /**
     * @return bool
     */
    public function register_nav_locations():bool
    {
        return register_rest_route(
            self::$rest_namespace,
            '/nav/locations',
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array(&$this, 'get_nav_menu_locations'),
                'permission_callback' => array(&$this, 'permissions_check')
            )
        );
    }

    /**
     * @return array
     */
    public function get_nav_menu_locations()
    {
        $locations = get_nav_menu_locations();

        $locations = apply_filters('wp_mango_get_nav_menu_locations', $locations);

        return $locations;
    }

    /**
     * @param $data
     *
     * @return array|false|\WP_Error
     */
    public function get_nav_menu_items($data)
    {
        $items = wp_get_nav_menu_items($data['id']);

        if (!$items) {
            return new \WP_Error('no_menu', 'Invalid menu', array('status' => 404));
        }

        return $items;
    }

    /**
     * @param $data
     *
     * @return \WP_Error|\WP_Term
     */
    public function get_nav_menu_location($data)
    {
        $locations = $this->get_nav_menu_locations();

        if (!array_key_exists($data['name'], $locations)) {
            return new \WP_Error('no_menu_location', 'Invalid menu Location', array('status' => 404));
        }

        return $locations[$data['name']];
    }

    /**
     * @param $data
     *
     * @return false|\WP_Error|\WP_Term
     */
    public function get_nav_menu($data)
    {
        $menu = wp_get_nav_menu_object($data['id']);

        if (!$menu) {
            return new \WP_Error('no_menu', 'Invalid menu', array('status' => 404));
        }

        return $menu;
    }

    public function admin_notice_enable_permalinks()
    {
        $message = __('Warning! Please, enable permalink structure to use Mango Plugin.');
        $class = 'notice notice-warning is-dismissible';

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    private function generate_credentials()
    {
        // create user
        if (get_option('mango_credentials_token') === false) {
            update_option('mango_credentials_token', uniqid('', true));
        }

        // create token
        if (get_option('mango_credentials_secret') === false) {
            update_option('mango_credentials_secret', bin2hex(random_bytes(23)));
        }
    }

    static public function activation()
    {
        // generate credentials upon activation
        //self::generate_credentials();

        return;
    }

    static public function deactivation()
    {
        delete_option('mango_credentials_secret'); // be ambigious here
        delete_option('mango_credentials_token');

        return;
    }

    /**
     * Get the relevant WP Options
     *
     * @return void
     */
    private function get_options()
    {
        $this->enabled = get_option('mango_enabled');
        $this->nav = get_option('mango_nav');
        $this->customizer = get_option('mango_customizer');
    }

    /**
     * Add WP Actions
     *
     * @return void
     */
    private function add_actions()
    {
        if (false === $this->enabled) {
            return;
        }

        // add action hooks
        add_action('rest_api_init', [$this, 'rest_api_init']);
    }

    /**
     * Add WP Filters
     *
     * @return void
     */
    private function add_filters()
    {
        if (false === $this->enabled) {
            return;
        }

        // add filters
        add_filter('rest_authentication_errors', [$this, 'permissions_check']);
    }
}
