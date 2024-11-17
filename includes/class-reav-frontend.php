<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Reav_Frontend {

    public function __construct() {
        // Check if WooCommerce Subscriptions is active
        if ( ! class_exists( 'WC_Subscription' ) ) {
            // WooCommerce Subscriptions is not active
            add_action( 'admin_notices', array( $this, 'woocommerce_subscriptions_inactive_notice' ) );
            return;
        }

        // Enqueue scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        // Add custom product fields
        add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'display_custom_options' ) );

        // Handle cart item data
        add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
        add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 20, 2 );
        add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 3 );

        // Display custom data in cart and checkout pages
        add_filter( 'woocommerce_get_item_data', array( $this, 'display_cart_item_data' ), 10, 2 );

        // Save data to order
        add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_meta' ), 10, 4 );

        // Modify button text
        add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'custom_cart_button_text' ) );

        // Mark product as subscription if necessary
        add_filter( 'woocommerce_is_subscription', array( $this, 'reav_is_subscription' ), 10, 3 );
    }

    /**
     * Admin notice when WooCommerce Subscriptions is not active
     */
    public function woocommerce_subscriptions_inactive_notice() {
        echo '<div class="notice notice-error"><p>';
        _e( 'Reav Buy and Save requires WooCommerce Subscriptions to be installed and active.', 'reav-buy-and-save' );
        echo '</p></div>';
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        if ( is_product() ) {
            wp_enqueue_style( 'reav-styles', REAV_PLUGIN_URL . 'assets/css/reav-styles.css' );
            wp_enqueue_script( 'reav-scripts', REAV_PLUGIN_URL . 'assets/js/reav-scripts.js', array( 'jquery', 'wc-add-to-cart-variation' ), '1.0.0', true );

            // Localize script with necessary data
            wp_localize_script( 'reav-scripts', 'reav_vars', array(
                'ajax_url'                    => admin_url( 'admin-ajax.php' ),
                'currency_format_num_decimals' => wc_get_price_decimals(),
                'currency_format_symbol'      => get_woocommerce_currency_symbol(),
                'currency_format_decimal_sep' => wc_get_price_decimal_separator(),
                'currency_format_thousand_sep'=> wc_get_price_thousand_separator(),
                'currency_format'             => get_woocommerce_price_format(),
            ) );
        }
    }

    /**
     * Display custom options on the product page
     */
    public function display_custom_options() {
        global $product;

        if ( ! $product->is_purchasable() ) {
            return;
        }

        // Get subscription options
        $subscriptions = $this->get_subscription_options( $product->get_id() );

        // If there are no subscriptions, do not display the subscription options
        if ( empty( $subscriptions ) ) {
            return;
        }

        // Include the template file
        include REAV_PLUGIN_DIR . 'templates/product-custom-options.php';
    }

    /**
     * Get subscription options
     */
    public function get_subscription_options( $product_id ) {
        // Check if per-product override is enabled
        $override = get_post_meta( $product_id, '_reav_override_subscriptions', true );

        if ( 'yes' === $override ) {
            // Get per-product settings
            $product_subscriptions = get_post_meta( $product_id, '_reav_subscriptions', true );

            // Use per-product settings if available
            if ( ! empty( $product_subscriptions ) ) {
                return $product_subscriptions;
            }
        }

        // Fallback to global settings
        $subscriptions = get_option( 'reav_global_subscriptions', array() );

        return $subscriptions;
    }

    /**
     * Add custom data to cart item
     */
    public function add_cart_item_data( $cart_item_data, $product_id ) {
        if ( isset( $_POST['reav_purchase_type'] ) ) {
            $cart_item_data['reav_purchase_type'] = sanitize_text_field( $_POST['reav_purchase_type'] );

            if ( $cart_item_data['reav_purchase_type'] == 'subscription' && isset( $_POST['reav_subscription_type'] ) ) {
                $cart_item_data['reav_subscription_type'] = sanitize_text_field( $_POST['reav_subscription_type'] );

                // Store the original product ID
                $cart_item_data['original_product_id'] = $product_id;

                // Get subscription options
                $subscriptions = $this->get_subscription_options( $product_id );

                // Find the selected subscription
                foreach ( $subscriptions as $subscription ) {
                    if ( $subscription['id'] == $cart_item_data['reav_subscription_type'] ) {
                        // Store the subscription details in cart item data
                        $cart_item_data['subscription_details'] = $subscription;
                        // Calculate the discounted price
                        $original_product = wc_get_product( $product_id );
                        $regular_price = floatval( wc_get_price_including_tax( $original_product ) );
                        $discount_percentage = floatval( $subscription['discount'] );
                        $discounted_price = $regular_price * ( ( 100 - $discount_percentage ) / 100 );

                        // Set the new price
                        $cart_item_data['custom_price'] = $discounted_price;

                        break;
                    }
                }
            }
        }

        return $cart_item_data;
    }

    /**
     * Modify cart item after it has been added to the cart
     */
    public function add_cart_item( $cart_item, $cart_item_key ) {
        if ( isset( $cart_item['subscription_details'] ) && $cart_item['reav_purchase_type'] == 'subscription' ) {
            $subscription_details = $cart_item['subscription_details'];
            $product = $cart_item['data'];

            $product->update_meta_data( '_subscription_period', $subscription_details['frequency_period'] );
            $product->update_meta_data( '_subscription_period_interval', intval( $subscription_details['frequency_number'] ) );
        }

        // Set custom price if available
        if ( isset( $cart_item['custom_price'] ) ) {
            $cart_item['data']->set_price( $cart_item['custom_price'] );
        }

        return $cart_item;
    }

    /**
     * Get cart item from session
     */
    public function get_cart_item_from_session( $cart_item, $values, $key ) {
        if ( isset( $values['subscription_details'] ) ) {
            $cart_item['subscription_details'] = $values['subscription_details'];
        }

        if ( isset( $values['reav_purchase_type'] ) ) {
            $cart_item['reav_purchase_type'] = $values['reav_purchase_type'];
        }

        if ( isset( $values['reav_subscription_type'] ) ) {
            $cart_item['reav_subscription_type'] = $values['reav_subscription_type'];
        }

        if ( isset( $values['original_product_id'] ) ) {
            $cart_item['original_product_id'] = $values['original_product_id'];
        }

        if ( isset( $values['custom_price'] ) ) {
            $cart_item['custom_price'] = $values['custom_price'];
            $cart_item['data']->set_price( $values['custom_price'] );
        }

        // Ensure subscription meta data is set on the product
        if ( isset( $cart_item['subscription_details'] ) && $cart_item['reav_purchase_type'] == 'subscription' ) {
            $subscription_details = $cart_item['subscription_details'];
            $product = $cart_item['data'];

            $product->update_meta_data( '_subscription_period', $subscription_details['frequency_period'] );
            $product->update_meta_data( '_subscription_period_interval', intval( $subscription_details['frequency_number'] ) );
        }

        return $cart_item;
    }

    /**
     * Display custom data in cart and checkout pages
     */
    public function display_cart_item_data( $item_data, $cart_item ) {
        if ( isset( $cart_item['reav_purchase_type'] ) ) {
            $item_data[] = array(
                'name'  => __( 'Purchase Type', 'reav-buy-and-save' ),
                'value' => ucfirst( $cart_item['reav_purchase_type'] ),
            );

            if ( $cart_item['reav_purchase_type'] == 'subscription' && isset( $cart_item['reav_subscription_type'] ) ) {
                $subscriptions = $this->get_subscription_options( $cart_item['original_product_id'] );
                foreach ( $subscriptions as $subscription ) {
                    if ( $subscription['id'] == $cart_item['reav_subscription_type'] ) {
                        $item_data[] = array(
                            'name'  => __( 'Subscription Type', 'reav-buy-and-save' ),
                            'value' => $subscription['name'],
                        );
                        break;
                    }
                }
            }
        }

        return $item_data;
    }

    /**
     * Add custom data to order item meta
     */
    public function add_order_item_meta( $item, $cart_item_key, $values, $order ) {
        if ( isset( $values['reav_purchase_type'] ) ) {
            $item->add_meta_data( __( 'Purchase Type', 'reav-buy-and-save' ), ucfirst( $values['reav_purchase_type'] ), true );

            if ( $values['reav_purchase_type'] == 'subscription' && isset( $values['reav_subscription_type'] ) ) {
                $subscriptions = $this->get_subscription_options( $values['original_product_id'] );
                foreach ( $subscriptions as $subscription ) {
                    if ( $subscription['id'] == $values['reav_subscription_type'] ) {
                        $item->add_meta_data( __( 'Subscription Type', 'reav-buy-and-save' ), $subscription['name'], true );
                        $item->add_meta_data( '_subscription_details', $subscription, true );
                        break;
                    }
                }
            }
        }
    }

    /**
     * Modify the "Add to Cart" button text
     */
    public function custom_cart_button_text( $button_text ) {
        if ( is_product() ) {
            $button_text = __( 'Add to Cart', 'reav-buy-and-save' );
        }
        return $button_text;
    }

    /**
     * Mark product as subscription if necessary
     */
    public function reav_is_subscription( $subscription, $product_id, $product ) {
        if ( ! $product ) {
            return $subscription;
        }

        if ( is_admin() && ! wp_doing_ajax() ) {
            return $subscription;
        }

        // Check if the cart exists and the get_cart method is available
        if ( WC()->cart && method_exists( WC()->cart, 'get_cart' ) ) {
            // Check if the cart item has the subscription purchase type
            foreach ( WC()->cart->get_cart() as $cart_item ) {
                if ( isset( $cart_item['reav_purchase_type'] ) && $cart_item['reav_purchase_type'] == 'subscription' && $cart_item['product_id'] == $product_id ) {
                    return true;
                }
            }
        }

        return $subscription;
    }
}

new Reav_Frontend();
