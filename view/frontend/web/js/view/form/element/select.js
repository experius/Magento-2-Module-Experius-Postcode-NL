define([
    'jquery',
    'Magento_Ui/js/form/element/select',
    'Experius_Postcode/js/action/postcode',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry'
], function($,Abstract,getPostcodeInformation,quote,checkoutData,registry) {
    'use strict';
    return Abstract.extend({
       defaults: {
          listens: {
            value: 'postcodeHouseNumberAdditionHasChanged',
          },
        },
        postcodeHouseNumberAdditionHasChanged: function(newValue){
            var current_street_value = false;
            var new_street_value = false;
            
            if(registry.get(this.parentName + '.street.0')) {
                current_street_value = registry.get(this.parentName + '.street.0').get('value');
                new_street_value = current_street_value + ' ' +  newValue;
                registry.get(this.parentName + '.street.0').set('value',new_street_value);
            }

        },
        getSettings: function() {
            var settings = window.checkoutConfig.experius_postcode.settings;
            return settings;
        },
    });
});