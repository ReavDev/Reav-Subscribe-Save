<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;
?>
<div class="reav-buy-and-save">
    <!-- Choose Purchase Type -->
    <p><?php _e( '1. Choose your purchase type', 'reav-buy-and-save' ); ?></p>
    <ul class="reav-purchase-type">
        <li>
            <input type="radio" id="one_time" name="reav_purchase_type" value="one_time" checked>
            <label for="one_time">
                <?php _e( 'One-time', 'reav-buy-and-save' ); ?>
                <span class="reav-price">
                    <?php         $price_including_tax = wc_get_price_including_tax( $product );
                    echo wc_price( $price_including_tax ); ?>
                </span>
            </label>
        </li>
        <?php if ( ! empty( $subscriptions ) ) : ?>
        <?php
            $lowest_price = null;
            $regular_price = floatval( wc_get_price_including_tax( $product ));
            foreach ( $subscriptions as $subscription ) {
                $discount = floatval( $subscription['discount'] );
                $price = $regular_price * (1 - $discount / 100);
                if ( is_null( $lowest_price ) || $price < $lowest_price ) {
                    $lowest_price = $price;
                }
            }
        ?>
        <li>
            <input type="radio" id="subscription" name="reav_purchase_type" value="subscription">
            <label for="subscription">
                <?php _e( 'Subscribe & Save', 'reav-buy-and-save' ); ?>
                <?php if ( ! is_null( $lowest_price ) ) : ?>
                    <span class="reav-price">
                        <?php echo __( 'From', 'reav-buy-and-save' ) . ' ' . wc_price( $lowest_price ); ?>
                    </span>
                <?php endif; ?>
            </label>
        </li>
    </ul>

    <!-- Subscription Options -->
    <div id="reav-subscription-options" style="display:none;">
        <p><?php _e( '2. Choose subscription type', 'reav-buy-and-save' ); ?></p>
        <ul class="reav-subscription-options">
            <?php foreach ( $subscriptions as $index => $subscription ) : ?>
                <?php
                    // Calculate the subscription price
                    $discount = floatval( $subscription['discount'] );
                    $subscription_price = $regular_price * (1 - $discount / 100);
                ?>
                <li>
                    <input type="radio" id="subscription_<?php echo esc_attr( $index ); ?>" name="reav_subscription_type" value="<?php echo esc_attr( $subscription['id'] ); ?>">
                    <label for="subscription_<?php echo esc_attr( $index ); ?>">
                        <?php echo esc_html( $subscription['name'] ); ?>
                        <span class="reav-price">
                            <?php echo wc_price( $subscription_price ); ?>
                        </span>
                        <ul class="reav-subscription-details">
                        <li>
                            <?php
                            $frequency_number = intval( $subscription['frequency_number'] );
                            $frequency_period = ucfirst( $subscription['frequency_period'] );
                            printf( __( 'Frequency: Every %s %s(s)', 'reav-buy-and-save' ), $frequency_number, $frequency_period );
                            ?>
                        </li>
                        <li><?php printf( __( 'Discount: %s%%', 'reav-buy-and-save' ), esc_html( $subscription['discount'] ) ); ?></li>
                        <?php if ( ! empty( $subscription['features'] ) ) : ?>
                            <ul class="reav-subscription-features">
                                <?php foreach ( $subscription['features'] as $feature ) : ?>
                                    <li><?php echo esc_html( $feature ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </ul>
                    </label>
                    
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php else : ?>
    </ul>
    </div>
    <?php endif; ?>
</div>
