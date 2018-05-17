define([
  'jquery',
  'jquery/ui'
  ], function($){
    'use strict';

    $.widget('icube.rayallenb2b', {

        _create: function() {
            this.initAllPages();
            this.initHomePage();
            this.initCategoryPage();
            this.initProductPage();
            this.initSearchPage();
            this.initShoppingCartPage();
            this.initCheckoutPage();
            this.initCmsPage();
            this.initCustomerAccountPage();
        },

        initAllPages: function() {

        },

        initHomePage: function() {
            if ($('body.cms-index-index').length) {
               
            }
        },

        initCategoryPage: function() {
            if ($('body.catalog-category-view').length) {
           }
        },

        initProductPage: function() {

            if ($('body.catalog-product-view').length) {

            }
        },

        initSearchPage: function() {

            if ($('body.catalogsearch-result-index, body.wordpress-search-index').length) {

            }
        },

        initShoppingCartPage: function() {

            if ($('body.checkout-cart-index').length) {
               
            }
        },

        initCheckoutPage: function() {

            if ($('body.checkout-index-index').length) {
                
            }
        },

        initCmsPage: function() {

            if ($('body.cms-page-view').length) {
            }

            if ($('body.cms-requestquote').length) {
                $(window).load(function() {
                    var checkInterval;
                    checkInterval = setInterval(function () {

                        var loaderContainer = $('.desc1-textinput-14');

                        //Return if loader still load
                        if (loaderContainer.length == 0) {
                            return;
                        }

                        //Remove loader and clear update interval if content loaded
                        if (loaderContainer.length > 0 ) {
                            clearInterval(checkInterval);
                            $( '#quote-text p.quote-description' ).insertAfter( '.cms-requestquote .amasty_custom_form [class*="field-textinput-email-"]' );

                            // Setup Dynamic Static Boxes for SKU/Items
                            // Check last input box index
                            var lastIndexInput = 14,
                                counter = 1;
                            $('.desc1-textinput-14').closest('.field').nextAll().hide();
                            $('.amasty_custom_form_fieldset').append('<button class="add-more-items"><span><span>Add More Items</span></span></button>');
                            $('.amasty_custom_form_fieldset').on('click', 'button.add-more-items',function(){
                                var lastVisibelField = $('.amasty_custom_form_fieldset > .field:visible:last');
                                lastVisibelField.next().css('display', 'inline-block');
                                lastVisibelField.next().next().css('display', 'inline-block');
                                lastVisibelField.next().next().next().css('display', 'inline-block');
                                return false;                                
                            });
                        }
                    }, 100);
                });

            }

        },

        initCustomerAccountPage: function() {

            if( $('body.account').length ) {
            }
        }

    });
    $(document).rayallenb2b();
});
