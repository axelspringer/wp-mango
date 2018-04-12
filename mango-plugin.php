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
$vendor_autoload_file = 'vendor/autoload.php';
if ( file_exists( $vendor_autoload_file ) ) {
	require $vendor_autoload_file;
}

// define global constants
define( 'MANGO__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

global $mango;

// create instance
$mango = new Wp\Mango\Mango( __FILE__, '1.0.0' );

// register 
register_activation_hook( __FILE__, 'Mango::activation' );
