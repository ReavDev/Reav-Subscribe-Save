<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Reav_Buy_And_Save {

    /**
     * Initialize the plugin
     */
    public static function init() {
        $instance = new self();
        $instance->load_dependencies();
        $instance->setup_hooks();
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Load frontend functionality
        require_once REAV_PLUGIN_DIR . 'includes/class-reav-frontend.php';

        // Load admin functionality if in admin area
        if ( is_admin() ) {
            require_once REAV_PLUGIN_DIR . 'includes/class-reav-admin.php';
        }
    }

    /**
     * Setup plugin hooks
     */
    private function setup_hooks() {
        // Load text domain for translations
        add_action( 'init', array( $this, 'load_textdomain' ) );
    }

    /**
     * Load plugin text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'reav-buy-and-save', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
}
