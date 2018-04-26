<?php

namespace AxelSpringer\WP\Mango;

use AxelSpringer\WP\Mango\Services\Credentials;
use AxelSpringer\WP\Bootstrap\Plugin\AbstractPlugin;
use Aws\Credentials\CredentialProvider;

/**
 * Class Plugin
 *
 * @package AxelSpringer\WP\Mango
 */
class Plugin extends AbstractPlugin
{
    /**
     * Credentials
     *
     * @var Credentials
     */
    public $credentials;

    /**
     * Filter
     *
     * @var Actions
     */
    public $actions;


    /**
     * Initializes the plugin
     */
    public function init()
    {
        // load options
        $this->setup->load_options( 'AxelSpringer\WP\Mango\__OPTION__' );
        $this->settings = new Settings(
            __( __TRANSLATE__::SETTINGS_PAGE_TITLE ),
            __( __TRANSLATE__::SETTINGS_MENU_TITLE ),
            __PLUGIN__::SETTINGS_PAGE,
            __PLUGIN__::SETTINGS_PERMISSION,
            $this->setup->version
        );

        if ( false === get_option( 'permalink_structure' ) || empty( get_option( 'permalink_structure' ) ) ) { // REST only available
			add_action( 'admin_notices', [ $this, 'admin_notice_enable_permalinks' ] );

			return false; // todo: should display error message
        }

        // Credentials
        $this->credentials = new Credentials();
		$this->credentials->generate_credentials();

        // load hooks
        $this->load_hooks();
    }

    /**
     * Load hooks
     * 
     */
    public function load_hooks()
    {
        $this->actions = new Actions( $this->setup, $this->credentials );
    }

    /**
     * Activates the Bootstrap plugin
     *
     * @return bool
     */
    public static function activation()
    {   
        // noop
		return true;
    }

    /**
     * Do actions after init
     */
    public function after_init()
    {
        // noop
    }

    /**
     * Deactivates the Bootstrap plugin
     *
     * @return bool
     */
    public static function deactivation()
    {
        // noop
		return true;
    }

    /**
     * Permalink notice
     */
    public function admin_notice_enable_permalinks() {
		$message = __( 'Warning! Please, enable permalink structure to use Mango Plugin.' );
		$class   = 'notice notice-warning is-dismissible';

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

    /**
     * Enqueue required scripts
     *
     * @return
     */
    public function enqueue_scripts()
    {

    }

    /**
     * Enqueue shared styles and scripts
     *
     * @return
     */
    public function enqueue_admin_scripts()
    {

	}
}
