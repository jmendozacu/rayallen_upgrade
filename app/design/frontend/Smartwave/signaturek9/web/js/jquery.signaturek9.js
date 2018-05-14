define([
  'jquery',
  'jquery/ui'
  ], function($){
    'use strict';

    $.widget('icube.signaturek9', {

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
            	var checkInterval;
                checkInterval = setInterval(function () {

                    var loaderContainer = $('.cms-requestquote .amasty_custom_form [class*="field-textinput-email-"]');

                    //Return if loader still load
                    if (loaderContainer.length == 0) {
                        return;
                    }

                    //Remove loader and clear update interval if content loaded
                    if (loaderContainer.length > 0 ) {
                        clearInterval(checkInterval);
            			$( '#quote-text p.quote-description' ).insertAfter( '.cms-requestquote .amasty_custom_form [class*="field-textinput-email-"]' );
                    }
                }, 100);

            }

        },

        initCustomerAccountPage: function() {

            if( $('body.account').length ) {
            }
        }

    });
    $(document).signaturek9();
});
