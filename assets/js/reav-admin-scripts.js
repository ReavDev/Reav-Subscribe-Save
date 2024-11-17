jQuery(document).ready(function($) {
    function generateId() {
        return 'sub_' + Math.random().toString(36).substr(2, 9);
    }

   // Add Subscription (Global)
$('#reav-add-subscription').on('click', function() {
    var index = $('#reav-subscriptions-rows tr').length;
    var id = generateId();
    var row = '<tr class="reav-subscription-row">' +
        '<td><input type="text" name="reav_global_subscriptions[subscriptions][' + index + '][name]" required />' +
        '<input type="hidden" name="reav_global_subscriptions[subscriptions][' + index + '][id]" value="' + id + '" /></td>' +
        '<td><input type="number" name="reav_global_subscriptions[subscriptions][' + index + '][frequency_number]" min="1" value="1" required />' +
        '<select name="reav_global_subscriptions[subscriptions][' + index + '][frequency_period]" required>' +
        '<option value="day">Day</option>' +
        '<option value="week">Week</option>' +
        '<option value="month">Month</option>' +
        '<option value="year">Year</option>' +
        '</select></td>' +
        '<td><input type="number" name="reav_global_subscriptions[subscriptions][' + index + '][discount]" min="0" max="100" step="0.01" value="0" required /></td>' +
        '<td><textarea name="reav_global_subscriptions[subscriptions][' + index + '][features]" rows="3"></textarea>' +
        '<p class="description">Enter one feature per line.</p></td>' +
        '<td><button type="button" class="button reav-remove-subscription">Remove</button></td>' +
        '</tr>';
    $('#reav-subscriptions-rows').append(row);
});


    // Remove Subscription
    $(document).on('click', '.reav-remove-subscription', function() {
        $(this).closest('tr').remove();
    });

    // Show/Hide Product Subscriptions
    $('#_reav_override_subscriptions').on('change', function() {
        if ($(this).is(':checked')) {
            $('#reav-product-subscriptions').show();
        } else {
            $('#reav-product-subscriptions').hide();
        }
    }).trigger('change');

    // Add Subscription (Product)
$('#reav-add-product-subscription').on('click', function() {
    var index = $('#reav-product-subscriptions-rows tr').length;
    var id = generateId();
    var row = '<tr class="reav-subscription-row">' +
        '<td><input type="text" name="reav_product_subscriptions[' + index + '][name]" required />' +
        '<input type="hidden" name="reav_product_subscriptions[' + index + '][id]" value="' + id + '" /></td>' +
        '<td><input type="number" name="reav_product_subscriptions[' + index + '][frequency_number]" min="1" value="1" required />' +
        '<select name="reav_product_subscriptions[' + index + '][frequency_period]" required>' +
        '<option value="day">Day</option>' +
        '<option value="week">Week</option>' +
        '<option value="month">Month</option>' +
        '<option value="year">Year</option>' +
        '</select></td>' +
        '<td><input type="number" name="reav_product_subscriptions[' + index + '][discount]" min="0" max="100" step="0.01" value="0" required /></td>' +
        '<td><textarea name="reav_product_subscriptions[' + index + '][features]" rows="3"></textarea>' +
        '<p class="description">Enter one feature per line.</p></td>' +
        '<td><button type="button" class="button reav-remove-subscription">Remove</button></td>' +
        '</tr>';
    $('#reav-product-subscriptions-rows').append(row);
});

});
