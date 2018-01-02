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

// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// composer autoload
require 'vendor/autoload.php';

// define global constants
define( 'MANGO__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

global $mango;
$abspath = dirname( __FILE__ );

// includes
require_once $abspath . '/includes/functions.php';
require_once $abspath . '/classes/mango-settings-field.php';
require_once $abspath . '/classes/mango-settings-section.php';
require_once $abspath . '/classes/mango-settings.php';
require_once $abspath . '/classes/mango.php';

// create instance
$mango = new Mango( __FILE__, '0.0.2' );

// register 
register_activation_hook( __FILE__, 'Mango::activation' );
