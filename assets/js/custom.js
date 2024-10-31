/* global woocommerce_settings_params, wp */
jQuery(document).ready(function () {
    jQuery("#scon_order_number_prefix").on('keypress',function(e) {
        var selectorField = jQuery(this)
        maxlength = 4
        if(jQuery.isNumeric(maxlength)){
            if(selectorField.val().length == maxlength) { e.preventDefault(); return; }
            selectorField.val(selectorField.val().substr(0, maxlength));
        }
    });
    jQuery("#scon_order_number_suffix").on('keypress',function(e) {
        var selectorField = jQuery(this)
        maxlength = 4
        if(jQuery.isNumeric(maxlength)){
            if(selectorField.val().length == maxlength) { e.preventDefault(); return; }
            selectorField.val(selectorField.val().substr(0, maxlength));
        }
    });
});
