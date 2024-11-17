<?php
/**
 * Plugin Name: Reav Buy and Save for WooCommerce
 * Description: Allows customers to choose purchase type and subscription options with features.
 * Version: 1.0.0
 * Author: ReavDev
 * Text Domain: reav-buy-and-save
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define( 'REAV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'REAV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include main class
require_once REAV_PLUGIN_DIR . 'includes/class-reav-buy-and-save.php';

// Initialize the plugin
add_action( 'plugins_loaded', array( 'Reav_Buy_And_Save', 'init' ) );
