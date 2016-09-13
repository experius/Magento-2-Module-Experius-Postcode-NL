define([
    'jquery',
    'Magento_Ui/js/form/form',
    'Experius_Postcode/js/action/postcode',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data'
], function($,Component,getPostcodeInformation,fullScreenLoader,quote,checkoutData) {
    'use strict';
    return Component.extend({
        initialize: function () {
            this._super();
            return this;
        },
        defaults: {
          checkDelay: 2000,
          emailCheckTimeout: 0,
          isLoading: false,
          checkRequest: null,
          isPonumberCheckComplete: null,
          value: window.checkoutConfig.quoteData.experius_po_number
        },
        onSubmit: function() {
            this.source.set('params.invalid', false);
            this.source.trigger('experiusPostcodeForm.data.validate');
            if (!this.source.get('params.invalid')) {
                this.ponumberHasChanged();
            }
        },
        ponumberHasChanged: function () {
            var self = this;

            clearTimeout(this.emailCheckTimeout);

            this.emailCheckTimeout = setTimeout(function () {
                self.getPostcodeInformation();
            }, self.checkDelay);
        },
        getPostcodeInformation: function () {
            
            var self = this;
            var response = false;
            var formData = this.source.get('experiusPostcodeForm');

            this.validateRequest();
            this.isPonumberCheckComplete = $.Deferred();
            this.checkRequest = getPostcodeInformation(this.isPonumberCheckComplete,formData.experius_postcode_postcode,formData.experius_postcode_housenumber);

            $.when(this.isPonumberCheckComplete).done(function (data) {
                
                response = JSON.parse(data);
                console.log(response);
                if (response.street) {
                    console.log('success');
                    self.source.set('shippingAddress.street[0]',response.street);
                    self.source.set('shippingAddress.street[1]',response.houseNumber);
                    self.source.set('shippingAddress.postcode',response.postcode);
                    self.source.set('shippingAddress.province',response.region);
                    self.source.set('shippingAddress.country_id','NL');
                    self.source.set('shippingAddress.city',response.city);
                } else {
                    console.log('error');
                }
                
                
                // finished
            }).fail(function () {
                // fail
            }).always(function () {
                // always
            });
            
            
            
        },
        validateRequest: function () {
            if (this.checkRequest != null && $.inArray(this.checkRequest.readyState, [1, 2, 3])) {
                this.checkRequest.abort();
                this.checkRequest = null;
            }
        }
    });
});