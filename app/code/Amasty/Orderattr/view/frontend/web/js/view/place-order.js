define([
    'uiRegistry',
    'Amasty_Orderattr/js/action/save-order-attributes-to-quote'
], function(registry, saveOrderAttributesToQuote) {
    'use strict';

    return function (Payment) {
        return Payment.extend({
            placeOrder: function () {
                var source = registry.get('checkoutProvider');
                saveOrderAttributesToQuote(source.get('shippingAddress.custom_attributes'));

                return this._super();
            }
        });
    }
});