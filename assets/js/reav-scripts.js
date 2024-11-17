jQuery(document).ready(function($) {
    function toggleSubscriptionOptions() {
        if ($('input[name="reav_purchase_type"]:checked').val() == 'subscription') {
            $('#reav-subscription-options').show();
            $('.single_add_to_cart_button').text('Subscribe Now');
        } else {
            $('#reav-subscription-options').hide();
            $('.single_add_to_cart_button').text('Add to Cart');
        }
        updateAddToCartButtonState();
    }

    function updateAddToCartButtonState() {
        var purchaseType = $('input[name="reav_purchase_type"]:checked').val();
        var isSubscriptionSelected = $('input[name="reav_subscription_type"]:checked').length > 0;

        if (purchaseType == 'one_time') {
            // One-time purchase selected
            $('.single_add_to_cart_button').prop('disabled', false);
        } else if (purchaseType == 'subscription' && isSubscriptionSelected) {
            // Subscription purchase and subscription type selected
            $('.single_add_to_cart_button').prop('disabled', false);
        } else {
            // No valid selection made
            $('.single_add_to_cart_button').prop('disabled', true);
        }
    }

    // Initial checks on page load
    toggleSubscriptionOptions();
    updateAddToCartButtonState();

    // Change event for purchase type
    $('input[name="reav_purchase_type"]').change(function() {
        toggleSubscriptionOptions();
    });

    // Change event for subscription type
    $(document).on('change', 'input[name="reav_subscription_type"]', function() {
        updateAddToCartButtonState();
    });

    function updateActiveLabels() {
        // Purchase Type radios
        $('.reav-purchase-type input[type="radio"]').each(function() {
            var $radio = $(this);
            var $label = $radio.next('label');
            if ($radio.is(':checked')) {
                $label.addClass('active');
            } else {
                $label.removeClass('active');
            }
        });

        // Subscription Type radios
        $('.reav-subscription-options input[type="radio"]').each(function() {
            var $radio = $(this);
            var $label = $radio.next('label');
            if ($radio.is(':checked')) {
                $label.addClass('active');
            } else {
                $label.removeClass('active');
            }
        });
    }

    updateActiveLabels();

    $('.reav-buy-and-save').on('change', 'input[type="radio"]', function() {
        updateActiveLabels();
    });
});

jQuery(document).ready(function($) {
    function updateSubscriptionPrices(variation) {
        if (typeof variation !== 'undefined') {
            var basePrice = variation.display_price;
            var isOnSale = variation.display_price !== variation.display_regular_price;

            // Update One-time Purchase Price
            $('.reav-onetime-price').html(wc_price(basePrice));

            // Update Subscription Prices
            $('.reav-subscription-options li').each(function() {
                var discount = parseFloat($(this).find('input[type="radio"]').data('discount'));
                if (isNaN(discount)) {
                    discount = 0;
                }
                var subscriptionPrice = basePrice * (100 - discount) / 100;
                $(this).find('.reav-price').html(wc_price(subscriptionPrice));
            });
        }
    }

    $('.variations_form').on('show_variation', function(event, variation) {
        updateSubscriptionPrices(variation);
    });

    $('.variations_form').each(function() {
        var variation = $(this).data('product_variations');
        if (variation && variation.length > 0) {
            // Assuming the first variation is the default
            updateSubscriptionPrices(variation[0]);
        }
    });

    $('input[name="reav_purchase_type"]').on('change', function() {
        if ($(this).val() === 'subscription') {
            $('#reav-subscription-options').slideDown();
        } else {
            $('#reav-subscription-options').slideUp();
        }
    });
});

function wc_price( price ) {
    var formatted = price.toLocaleString(undefined, { style: 'currency', currency: wc_cart_params.currency });

    return formatted;
}
