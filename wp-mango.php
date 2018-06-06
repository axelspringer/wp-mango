<?php
/*
Plugin Name: Mango WordPress Plugin
Plugin URI: http://wordpress.org/extend/plugins/mango/
Description: A companion WordPress plugin to support Mango.
Author: Axel Springer SE
Version: 0.0.2
Author URI: https://www.axelspringer.de
Network: True
Text Domain: mango-plugin
Domain Path: /languages/
*/

defined( 'ABSPATH' ) || exit;

// make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// respect composer autoload
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	$loader = require_once __DIR__ . '/vendor/autoload.php';
	$loader->addPsr4( 'AxelSpringer\\WP\\Mango\\', __DIR__ . '/src' );
}

use \AxelSpringer\WP\Mango\WP;
use \AxelSpringer\WP\Mango\Plugin;
use \AxelSpringer\WP\Mango\Mango;

// bootstrap
if ( ! defined( WP::VERSION ) )
	define( WP::VERSION, Plugin::VERSION );

if ( ! defined( WP::URL ) )
	define( WP::URL, plugin_dir_url( __FILE__ ) );

if ( ! defined( WP::SLUG ) )
    define( WP::SLUG, Plugin::SLUG );

// activation
register_activation_hook( __FILE__, '\AxelSpringer\WP\Mango\Mango::activation' );

// deactivation
register_deactivation_hook( __FILE__, '\AxelSpringer\WP\Mango\Mango::deactivation' );

// run
global $wp_mango; // this bootstraps the plugin, and provides a global accessible helper
$wp_mango = new Mango( WP_MANGO_SLUG, WP_MANGO_VERSION, __FILE__ );
