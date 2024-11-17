<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Reav_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_product_fields' ) );
        add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_product_fields' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( 'woocommerce_page_reav-buy-and-save' === $hook || 'post.php' === $hook || 'post-new.php' === $hook ) {
            wp_enqueue_script( 'reav-admin-scripts', REAV_PLUGIN_URL . 'assets/js/reav-admin-scripts.js', array( 'jquery' ), '1.0.0', true );
            wp_enqueue_style( 'reav-admin-styles', REAV_PLUGIN_URL . 'assets/css/reav-admin-styles.css' );
        }
    }

    /**
     * Add submenu under WooCommerce
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'Reav Buy and Save Settings', 'reav-buy-and-save' ),
            __( 'Reav Buy and Save', 'reav-buy-and-save' ),
            'manage_options',
            'reav-buy-and-save',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Create settings page content
     */
    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Reav Buy and Save Settings', 'reav-buy-and-save' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'reav_settings_group' );
                do_settings_sections( 'reav-buy-and-save' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting( 'reav_settings_group', 'reav_global_subscriptions', array( $this, 'sanitize_settings' ) );

        add_settings_section(
            'reav_settings_section',
            __( 'Global Subscription Types', 'reav-buy-and-save' ),
            null,
            'reav-buy-and-save'
        );

        add_settings_field(
            'reav_subscriptions',
            __( 'Subscription Types', 'reav-buy-and-save' ),
            array( $this, 'subscriptions_callback' ),
            'reav-buy-and-save',
            'reav_settings_section'
        );
    }

    /**
     * Sanitize settings input
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();

        if ( isset( $input['subscriptions'] ) && is_array( $input['subscriptions'] ) ) {
            foreach ( $input['subscriptions'] as $subscription ) {
                $sanitized_subscription = array();

                $sanitized_subscription['id'] = sanitize_key( $subscription['id'] );
                $sanitized_subscription['name'] = sanitize_text_field( $subscription['name'] );
                $sanitized_subscription['frequency_number'] = intval( $subscription['frequency_number'] );
                $sanitized_subscription['frequency_period'] = sanitize_text_field( $subscription['frequency_period'] );
                $sanitized_subscription['discount'] = floatval( $subscription['discount'] );
                $sanitized_subscription['features'] = array_filter( array_map( 'sanitize_text_field', explode( "\n", $subscription['features'] ) ) );

                $sanitized[] = $sanitized_subscription;
            }
        }

        return $sanitized;
    }

    /**
     * Subscriptions callback
     */
    public function subscriptions_callback() {
        $subscriptions = get_option( 'reav_global_subscriptions', array() );
        ?>
        <table class="reav-subscriptions-table">
            <thead>
                <tr>
                    <th><?php _e( 'Subscription Name', 'reav-buy-and-save' ); ?></th>
                    <th><?php _e( 'Frequency', 'reav-buy-and-save' ); ?></th>
                    <th><?php _e( 'Discount (%)', 'reav-buy-and-save' ); ?></th>
                    <th><?php _e( 'Features', 'reav-buy-and-save' ); ?></th>
                    <th><?php _e( 'Action', 'reav-buy-and-save' ); ?></th>
                </tr>
            </thead>
            <tbody id="reav-subscriptions-rows">
                <?php if ( ! empty( $subscriptions ) ) : ?>
                    <?php foreach ( $subscriptions as $index => $subscription ) : ?>
                        <?php $this->render_subscription_row( $subscription, $index ); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <button type="button" class="button" id="reav-add-subscription"><?php _e( 'Add Subscription', 'reav-buy-and-save' ); ?></button>
        <?php
    }

    /**
     * Render a subscription row
     */
    private function render_subscription_row( $subscription = array(), $index = 0, $is_product = false ) {
        $field_name_prefix = $is_product ? 'reav_product_subscriptions' : 'reav_global_subscriptions[subscriptions]';
        ?>
        <tr class="reav-subscription-row">
            <td>
                <input type="text" name="<?php echo $field_name_prefix; ?>[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $subscription['name'] ); ?>" required />
                <input type="hidden" name="<?php echo $field_name_prefix; ?>[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $subscription['id'] ); ?>" />
            </td>
            <td>
                <input type="number" name="<?php echo $field_name_prefix; ?>[<?php echo esc_attr( $index ); ?>][frequency_number]" value="<?php echo esc_attr( $subscription['frequency_number'] ); ?>" min="1" required />
                <select name="<?php echo $field_name_prefix; ?>[<?php echo esc_attr( $index ); ?>][frequency_period]" required>
                    <option value="day" <?php selected( $subscription['frequency_period'], 'day' ); ?>><?php _e( 'Day', 'reav-buy-and-save' ); ?></option>
                    <option value="week" <?php selected( $subscription['frequency_period'], 'week' ); ?>><?php _e( 'Week', 'reav-buy-and-save' ); ?></option>
                    <option value="month" <?php selected( $subscription['frequency_period'], 'month' ); ?>><?php _e( 'Month', 'reav-buy-and-save' ); ?></option>
                    <option value="year" <?php selected( $subscription['frequency_period'], 'year' ); ?>><?php _e( 'Year', 'reav-buy-and-save' ); ?></option>
                </select>
            </td>
            <td>
                <input type="number" name="<?php echo $field_name_prefix; ?>[<?php echo esc_attr( $index ); ?>][discount]" value="<?php echo esc_attr( $subscription['discount'] ); ?>" min="0" max="100" step="0.01" required />
            </td>
            <td>
                <textarea name="<?php echo $field_name_prefix; ?>[<?php echo esc_attr( $index ); ?>][features]" rows="3"><?php echo esc_textarea( implode( "\n", $subscription['features'] ) ); ?></textarea>
                <p class="description"><?php _e( 'Enter one feature per line.', 'reav-buy-and-save' ); ?></p>
            </td>
            <td>
                <button type="button" class="button reav-remove-subscription"><?php _e( 'Remove', 'reav-buy-and-save' ); ?></button>
            </td>
        </tr>
        <?php
    }

    /**
     * Add per-product fields
     */
    public function add_product_fields() {
        global $post;

        echo '<div class="options_group">';

        // Get existing values
        $override = get_post_meta( $post->ID, '_reav_override_subscriptions', true );
        $product_subscriptions = get_post_meta( $post->ID, '_reav_subscriptions', true );

        // Checkbox to enable per-product subscriptions
        woocommerce_wp_checkbox( array(
            'id'          => '_reav_override_subscriptions',
            'label'       => __( 'Override Global Subscriptions', 'reav-buy-and-save' ),
            'description' => __( 'Enable to set custom subscriptions for this product.', 'reav-buy-and-save' ),
            'desc_tip'    => true,
            'value'       => $override,
        ) );

        echo '<div id="reav-product-subscriptions" style="' . ( 'yes' === $override ? '' : 'display: none;' ) . '">';

        echo '<h3>' . __( 'Product Subscription Types', 'reav-buy-and-save' ) . '</h3>';

        ?>
        <table class="reav-subscriptions-table">
            <thead>
                <tr>
                    <th><?php _e( 'Subscription Name', 'reav-buy-and-save' ); ?></th>
                    <th><?php _e( 'Frequency', 'reav-buy-and-save' ); ?></th>
                    <th><?php _e( 'Discount (%)', 'reav-buy-and-save' ); ?></th>
                    <th><?php _e( 'Features', 'reav-buy-and-save' ); ?></th>
                    <th><?php _e( 'Action', 'reav-buy-and-save' ); ?></th>
                </tr>
            </thead>
            <tbody id="reav-product-subscriptions-rows">
                <?php if ( ! empty( $product_subscriptions ) ) : ?>
                    <?php foreach ( $product_subscriptions as $index => $subscription ) : ?>
                        <?php $this->render_subscription_row( $subscription, $index, true ); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <button type="button" class="button" id="reav-add-product-subscription"><?php _e( 'Add Subscription', 'reav-buy-and-save' ); ?></button>
        <?php

        echo '</div>'; // End of reav-product-subscriptions

        echo '</div>';
    }

    /**
     * Save per-product fields
     */
    public function save_product_fields( $product ) {
        // Override Subscriptions Checkbox
        $override = isset( $_POST['_reav_override_subscriptions'] ) ? 'yes' : 'no';
        $product->update_meta_data( '_reav_override_subscriptions', $override );

        if ( 'yes' === $override ) {
            // Sanitize and save subscriptions
            $subscriptions = array();
            if ( isset( $_POST['reav_product_subscriptions'] ) && is_array( $_POST['reav_product_subscriptions'] ) ) {
                foreach ( $_POST['reav_product_subscriptions'] as $subscription ) {
                    $sanitized_subscription = array();

                    $sanitized_subscription['id'] = sanitize_key( $subscription['id'] );
                    $sanitized_subscription['name'] = sanitize_text_field( $subscription['name'] );
                    $sanitized_subscription['frequency_number'] = intval( $subscription['frequency_number'] );
                    $sanitized_subscription['frequency_period'] = sanitize_text_field( $subscription['frequency_period'] );
                    $sanitized_subscription['discount'] = floatval( $subscription['discount'] );
                    $sanitized_subscription['features'] = array_filter( array_map( 'sanitize_text_field', explode( "\n", $subscription['features'] ) ) );

                    $subscriptions[] = $sanitized_subscription;
                }
            }

            $product->update_meta_data( '_reav_subscriptions', $subscriptions );
        } else {
            $product->delete_meta_data( '_reav_subscriptions' );
        }
    }
}

new Reav_Admin();
