/** Explorer/Payment/view/frontend/web/js/view/payment/onaccount.js **/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
        ) {
        'use strict';
        rendererList.push(
            {
                type: 'onaccount',
                component: 'Kensium_Payment/js/view/payment/method-renderer/onaccount-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);