define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'Experius_Postcode/js/action/postcode',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry'
], function($,Abstract,getPostcodeInformation,quote,checkoutData,registry) {
    'use strict';
    return Abstract.extend({
        defaults: {
          listens: {
            focused: 'postcodeHasChanged',
          },
          checkDelay: 2000,
          emailCheckTimeout: 0,
          isLoading: false,
          checkRequest: null,
          isPostcodeCheckComplete: null,
        },
        initialize: function () {
            _.bindAll(this, 'reset');

            this._super()
                .setInitialValue()
                ._setClasses()
                .initSwitcher();
            
            if(this.index=='experius_postcode_housenumber'){    
                
                var self = this;
                
                setTimeout(function () {
                     self.postcodeHasChanged();
                     self.hideFields();
                }, self.checkDelay);
               
            }
                
            return this;
        },
        initObservable: function () {
            var rules = this.validation = this.validation || {};

            this._super();

            this.observe('error disabled focused preview visible value warn isDifferedFromDefault notice')
                .observe('isUseDefault')
                .observe({
                    'required': !!rules['required-entry']
                });
            
            return this;
        },
        postcodeHasChanged: function () {
            
            this.debug('start lookup');
            
            var self = this;
            
            var formData = this.source.get(this.parentScope);
            
            this.debug(formData);

            if(formData.experius_postcode_postcode && formData.experius_postcode_housenumber) {
                clearTimeout(this.emailCheckTimeout);
                this.emailCheckTimeout = setTimeout(function () {
                    self.getPostcodeInformation();
                }, self.checkDelay);
            } else {
                this.debug('postcode or housenumber not set. ' + 'housenumber:' + formData.experius_postcode_housenumber + ' postcode:' + formData.experius_postcode_postcode);   
            }

        },
        hideFields(){
            
            this.debug('hide fields');
            
            if (registry.get(this.parentName + '.street')) {
                registry.get(this.parentName + '.street').set('visible',false).set('label','').set('required',false);
            }
            
            registry.get(this.parentName + '.street.0').set('visible',false).set('labelVisible',false).set('disabled',true);
            
            if(registry.get(this.parentName + '.street.1')) {
                registry.get(this.parentName + '.street.1').set('visible',false).set('labelVisible',false).set('disabled',true);
            }
        
            registry.get(this.parentName + '.postcode').set('visible',false).set('labelVisible',false).set('disabled',true);
            
            if (registry.get(this.parentName + '.region_id')) {
                registry.get(this.parentName + '.region_id').set('visible',false).set('labelVisible',false).set('disabled',true);
            }
             
            if (registry.get(this.parentName + '.region')) {
                registry.get(this.parentName + '.region').set('visible',false).set('labelVisible',false).set('disabled',true).setVisible(false);
            }
           
            registry.get(this.parentName + '.country_id').set('visible',false).set('labelVisible',false).set('disabled',true);
            
            registry.get(this.parentName + '.city').set('visible',false).set('labelVisible',false).set('disabled',true);
            
            //dirty fix for now
            $('fieldset.street').css('margin','0px').hide();
            $('div[name="'+this.parentName+'.region"]').hide();

        },
        getSettings(){
            return {'useStreet2AsHouseNumber': false, 'useStreet3AsHouseNumberAddition': false, 'debug':true}
        },
        getPostcodeInformation: function () {
            
            var self = this;
            var response = false;
            var formData = this.source.get(this.parentScope);

            this.validateRequest();
            this.isPostcodeCheckComplete = $.Deferred();
            this.checkRequest = getPostcodeInformation(this.isPostcodeCheckComplete,formData.experius_postcode_postcode,formData.experius_postcode_housenumber);

            $.when(this.isPostcodeCheckComplete).done(function (data) {
                
                response = JSON.parse(data);
                
                self.debug(response);
                
                if (response.street) {
                    self.error(false);
                    
                    if(!self.getSettings().useStreet2AsHouseNumber){
                        registry.get(self.parentName + '.street.0').set('value',response.street + ' ' + response.houseNumber).set('error',false);
                    } else {
                        registry.get(self.parentName + '.street.0').set('value',response.street).set('error',false);
                        registry.get(self.parentName + '.street.1').set('value',response.houseNumber).set('error',false);
                    }
                    registry.get(self.parentName + '.country_id').set('value','NL').set('error',false);
                    registry.get(self.parentName + '.region_id').set('value',response.province).set('error',false);
                    registry.get(self.parentName + '.city').set('value',response.city).set('error',false);
                    registry.get(self.parentName + '.postcode').set('value',response.postcode).set('error',false);
                    
                    registry.get(self.parentName + '.experius_postcode_housenumber').set('notice','<br/>' + response.street + ' ' + response.houseNumber + '<br/>' + response.postcode + ' ' + response.city + '<br/>Nederland');
                
                } else {
                    registry.get(self.parentName + '.experius_postcode_housenumber').set('error',response.message);
                    registry.get(self.parentName + '.experius_postcode_housenumber').set('notice',false);
                    registry.get(self.parentName + '.street.0').set('value','');
                    registry.get(self.parentName + '.street.1').set('value','');
                    registry.get(self.parentName + '.country_id').set('value','');
                    registry.get(self.parentName + '.region_id').set('value','');
                    registry.get(self.parentName + '.city').set('value','');
                    registry.get(self.parentName + '.postcode').set('value','');
                }
                
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
        },
        debug: function(message){
            console.log(message);
        }
    });
});