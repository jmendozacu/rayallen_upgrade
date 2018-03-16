define(['jquery'], function ($) {
    var ampromoForm = {
        update: function () {
            this.showFields(['rule_discount_qty', 'rule_discount_step', 'rule_apply_to_shipping']);
            this.hideFields(['rule_ampromo_sku', 'rule_ampromo_type', 'rule_simple_free_shipping']);

            var actionFieldset = $('#rule_actions_fieldset').parent();

            actionFieldset.show();

            var action = $('#rule_simple_action').val();

            switch (action) {
                case 'ampromo_cart':
                    actionFieldset.hide();

                    this.hideFields([
                        'rule_simple_free_shipping',
                        'rule_discount_qty',
                        'rule_discount_step',
                        'rule_apply_to_shipping'
                    ]);

                    this.showFields(['rule_ampromo_sku', 'rule_ampromo_type']);
                    break;
                case 'ampromo_items':
                    this.hideFields(['rule_simple_free_shipping', 'rule_apply_to_shipping']);
                    this.showFields(['rule_ampromo_sku', 'rule_ampromo_type']);
                    break;
                case 'ampromo_product':
                    this.hideFields(['rule_simple_free_shipping', 'rule_apply_to_shipping']);
                    break;
                case 'ampromo_percentage':
                    this.showFields(['rule_simple_free_shipping', 'rule_apply_to_shipping']);
                    this.hideFields(['rule_ampromo_sku', 'rule_ampromo_type']);
                    break;
                case 'ampromo_spent':
                    actionFieldset.hide();

                    this.hideFields(['rule_simple_free_shipping', 'rule_apply_to_shipping']);
                    this.showFields(['rule_ampromo_sku', 'rule_ampromo_type']);
                    break;
            }
        },

        hideFields: function (names) {
            return this.toggleFields(false, names);
        },

        showFields: function (names) {
            return this.toggleFields(true, names);
        },

        toggleFields: function (status, names) {
            $.each(names, function (i, name) {
                $('#' + name).parents('.field').toggle(status);
            });
        }
    };

    return ampromoForm;
});
