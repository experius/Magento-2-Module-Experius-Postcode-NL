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
            previousValue: ""
        },
        postcodeHouseNumberAdditionHasChanged: function(newValue){
            var current_street_value = false;
            var new_street_value = false;
            var addition = false;

            if(newValue==undefined){
                return;
            }

            /* Needs refactoring */

            if(this.getSettings().useStreet2AsHouseNumber && registry.get(this.parentName + '.street.1') && registry.get(this.parentName + '.street.1').get('value')){
                current_street_value = this.removeOldAdditionFromString(registry.get(this.parentName + '.street.1').get('value'));
                addition =  (newValue) ? ' ' +  newValue : '';
                new_street_value = current_street_value + addition;
                registry.get(this.parentName + '.street.1').set('value',new_street_value);
            } else if(this.getSettings().useStreet3AsHouseNumberAddition && registry.get(this.parentName + '.street.2')){
                registry.get(this.parentName + '.street.2').set('value',newValue);
            } else if(registry.get(this.parentName + '.street.0') && registry.get(this.parentName + '.street.0').get('value')) {
                current_street_value = this.removeOldAdditionFromString(registry.get(this.parentName + '.street.0').get('value'));
                addition =  (newValue) ? ' ' +  newValue : '';
                new_street_value = current_street_value + addition;
                registry.get(this.parentName + '.street.0').set('value',new_street_value);
            }

            this.previousValue = newValue;

        },
        removeOldAdditionFromString: function(street){
            if(this.previousValue!=undefined && this.previousValue && street) {
                var streetParts  = (""+street).split(" ");
                if(streetParts.length>1) {
                    streetParts.pop();
                }
                street = streetParts.join(" ");
                return street;
            }
            return street;
        },
        getSettings: function() {
            var settings = window.checkoutConfig.experius_postcode.settings;
            return settings;
        }
    });
});