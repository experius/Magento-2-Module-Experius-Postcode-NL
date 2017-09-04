define([
    'jquery',
    'Magento_Ui/js/form/components/group',
    'Experius_Postcode/js/action/postcode',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry'
], function($,Abstract,getPostcodeInformation,checkoutData,registry) {
    'use strict';
    return Abstract.extend({
        defaults: {
            checkDelay: 500,
            emailCheckTimeout: 0,
            isLoading: false,
            checkRequest: null,
            isPostcodeCheckComplete: null,
            postcodeCheckValid: true,
            addressType: 'shipping',
            imports: {
                observeCountry: '${ $.parentName }.country_id:value',
                observeDisableCheckbox: '${ $.parentName }.experius_postcode_fieldset.experius_postcode_disable:value',
                observePostcodeField: '${ $.parentName }.experius_postcode_fieldset.experius_postcode_postcode:value',
                observeHousenumberField: '${ $.parentName }.experius_postcode_fieldset.experius_postcode_housenumber:value',
                observeAdditionDropdown: '${ $.parentName }.experius_postcode_fieldset.experius_postcode_housenumber_addition:value',
                observeAdditionManual: '${ $.parentName }.experius_postcode_fieldset.experius_postcode_housenumber_addition_manual:value',
                observeStreet: '${ $.parentName }.street:visible'
            },
            listens: {
                '${ $.provider }:${ $.customScope ? $.customScope + "." : ""}data.validate': 'validate',
            },
            visible: true,
        },

        getAddressData: function(){
            if(this.addressType=='shipping' && typeof checkoutData.getShippingAddressFromData() !== 'undefined' && checkoutData.getShippingAddressFromData()) {
                return checkoutData.getShippingAddressFromData();
            } else if(this.addressType=='billing' && typeof checkoutData.getBillingAddressFromData() !== 'undefined' && checkoutData.getBillingAddressFromData()){
                return checkoutData.getBillingAddressFromData();
            } else if(this.source) {
                return this.source.get(this.customScope);
            } else {
                return;
            }
        },
        initialize: function () {
            this._super()
                ._setClasses();

            this.updatePostcode();

            return this;
        },
        initObservable: function () {
            var rules = this.validation = this.validation || {};

            this._super().observe(['isLoading']);

            this.observe('isLoading checked error disabled focused preview visible value warn isDifferedFromDefault notice')
                .observe('isUseDefault')
                .observe({
                    'required': !!rules['required-entry']
                });

            return this;
        },
        observeStreet: function(value){
            if(value===false){

            }
        },
        observeCountry: function (value) {
            if (value) {
                this.toggleFieldsByCountry(this.getAddressData());
            }
        },
        observeDisableCheckbox: function (value) {
            if(value){
                this.hideFields();
                this.enableFields();
                this.postcodeCheckValid = true;
                this.notice('')
                this.error(null)
                this.toggleHousenumberAdditionFields(this.getAddressData());
            } else if (registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_disable').get('visible')) {
                this.hideFields();
                this.postcodeCheckValid = null;
                this.disableFields();
                this.updatePostcode();
                this.toggleHousenumberAdditionFields(this.getAddressData());
            }
        },
        observePostcodeField: function (value) {
            if(value) {
                this.updatePostcode();
            }
        },
        observeHousenumberField: function (value) {
            if(value) {
                this.updatePostcode();
            }
        },
        observeAdditionDropdown: function (value) {
            this.observeAdditionManual(value);
        },
        observeAdditionManual: function (value) {
            this.postcodeHouseNumberAdditionHasChanged(value);
            this.updatePreview();
        },
        toggleFieldsByCountry: function(address){
            if(address && address.country_id=='NL' && !address.experius_postcode_disable) {
                this.hideFields();
                this.disableFields();
                this.postcodeCheckValid = null;
                this.debug('hide fields based on country value');
                registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_disable').set('visible', true);
                this.updatePostcode();
            } else if(address && address.country_id=='NL' && address.experius_postcode_disable){
                this.hideFields();
                this.enableFields();
                this.postcodeCheckValid = true;
                this.error(null)
                this.debug('show fields based on country value and disable checkbox');
                registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_disable').set('visible',true);
            } else {
                this.showFields();
                this.enableFields();
                this.postcodeCheckValid = true;
                this.error(null)
                this.debug('show fields based on country value');
                registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_disable').set('visible',false);
                registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_disable').set('value',false);
            }
            this.toggleHousenumberAdditionFields(address);
        },
        updatePostcode: function(){
            var self = this;
            if (self.getSettings().timeout != undefined){
            	clearTimeout(self.getSettings().timeout);
            }
            self.getSettings().timeout = setTimeout(function () {
                self.postcodeHasChanged();
            }, self.checkDelay);
        },
        postcodeHasChanged: function() {

            var self = this;
			
			if (!this.source) {
                return;
            }
		
            var formData = this.source.get(this.customScope);
            if (!formData){
            	return;
            }

            this.debug(formData);

            if(!formData.experius_postcode_disable && formData.country_id=='NL') {
                this.hideFields();
            }

            if(formData.experius_postcode_postcode && formData.experius_postcode_housenumber && formData.experius_postcode_disable !== true && formData.country_id=='NL') {
                this.debug('start postcode lookup');
                clearTimeout(this.emailCheckTimeout);
                this.emailCheckTimeout = setTimeout(function () {
                    self.getPostcodeInformation();
                }, self.checkDelay);
            } else {
                if(self.getSettings().useStreet2AsHouseNumber){
                    registry.get(self.parentName + '.street.1').set('value',formData.experius_postcode_housenumber).set('error',false);
                    self.debug('address on two lines');
                } else {
                    registry.get(self.parentName + '.street.0').set('value',formData.street + ' ' + formData.experius_postcode_housenumber).set('error',false);
                    self.debug('address on single line');
                }
                this.debug('postcode or housenumber not set. ' + 'housenumber:' + formData.experius_postcode_housenumber + ' postcode:' + formData.experius_postcode_postcode);
            }
            

        },

        disableFields: function(){

            this.debug('hide magento default fields');

            var self = this;
            $.each(['street.0','city','postcode'], function(key,fieldName){

                var element = registry.get(self.parentName + '.' + fieldName);
                if (element) {
                    if (element.component.indexOf('/group') !== -1) {
                        $.each(element.elems(), function (index, elem) {
                            elem.set('disabled', true);
                            if (fieldName == 'postcode') {
                                elem.set('visible', false);
                            }
                        });
                    }else{
                        element.set('disabled', true);
                        if (fieldName == 'postcode') {
                            element.set('visible', false);
                        }
                    }
                }
            });

			registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_postcode').set('visible',true);
            registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber').set('visible',true);

            if(registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition'))
            {
                registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition').set('visible', false);
            }

        },

        enableFields: function(){

            this.debug('show magento default fields');

            var self = this;
            $.each(['street.0','city'], function(key,fieldName){

                var element = registry.get(self.parentName + '.' + fieldName);
                if (element) {
                    if (element.component.indexOf('/group') !== -1) {
                        $.each(element.elems(), function (index, elem) {
                            elem.set('visible',true).set('labelVisible',true).set('disabled',false);
                        });
                    }else{
                        element.set('visible', true).set('labelVisible',true).set('disabled',false);
                    }
                }
            });

            this.notice('');
        },

        hideFields: function(){

            this.debug('hide magento default fields');

            var self = this;
            $.each(['postcode', 'street.1', 'street.2'], function(key,fieldName){

                var element = registry.get(self.parentName + '.' + fieldName);
                if (element) {
                    if (element.component.indexOf('/group') !== -1) {
                        $.each(element.elems(), function (index, elem) {
                            elem.set('visible', false).set('labelVisible', false).set('disabled', true);
                        });
                    }else{
                        element.set('visible', false).set('labelVisible', false).set('disabled', true);
                    }
                }
            });

            registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_postcode').set('visible',true);
            registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber').set('visible',true);

            var streetElement = registry.get(this.parentName + '.street');
            var additionalClasses = streetElement.get('additionalClasses');
            $.each(additionalClasses, function(className, active) {
                if (className.indexOf('-street') >= 0) {
                    additionalClasses["experius-postcode-enabled"] = true;
                    streetElement.set('additionalClasses', additionalClasses);
                    $("fieldset." + className).addClass("experius-postcode-enabled");
                }
            });



        },

        toggleHousenumberAdditionFields: function(address){
            if(address && address.country_id=='NL' && !address.experius_postcode_disable) {
                if (registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition')) {
                    var value = registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition').value();
                    this.observeAdditionDropdown(value);
                }
                if(registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition_manual')) {
                    registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition_manual').set('visible', false);
                }
            } else if(address && address.country_id=='NL' && address.experius_postcode_disable){
                if(registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition')) {
                    registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition').set('visible', false);
                }
                if(registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition_manual')) {
                    registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition_manual').set('visible', true);
                    var value = registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition_manual').value();
                    this.observeAdditionManual(value);
                }
            } else {
                if(registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition')) {
                    registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition').set('visible', false);
                }
                if(registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition_manual')) {
                    registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition_manual').set('visible', false);
                }
            }
        },

        showFields: function(){

            this.debug('show magento default fields');

            var self = this;
            $.each(['postcode', 'street.1', 'street.2'], function(key,fieldName){

                var element = registry.get(self.parentName + '.' + fieldName);
                if (element) {
            		if (element.component.indexOf('/group') !== -1) {
		                $.each(element.elems(), function (index, elem) {
		                    elem.set('visible',true).set('labelVisible',true).set('disabled',false);
		                });
		            }else{
                		element.set('visible',true).set('labelVisible',true).set('disabled',false);
                	}
                }
            });

            $.each([
                'experius_postcode_fieldset.experius_postcode_postcode',
                'experius_postcode_fieldset.experius_postcode_housenumber',
                'experius_postcode_fieldset.experius_postcode_housenumber_addition'
                ], function(key,fieldName){
                if (registry.get(self.parentName + '.' + fieldName)) {
                    registry.get(self.parentName + '.' + fieldName).set('visible',false);
                }
            });

            var streetElement = registry.get(this.parentName + '.street');
            var additionalClasses = streetElement.get('additionalClasses');
            $.each(additionalClasses, function(className, active) {
                if (className.indexOf('-street') >= 0) {
                    additionalClasses["experius-postcode-enabled"] = false;
                    streetElement.set('additionalClasses', additionalClasses);
                    $("fieldset." + className).removeClass("experius-postcode-enabled");
                }
            });
            
            this.notice('');
        },
        getSettings: function() {
            var settings = window.checkoutConfig.experius_postcode.settings;
            return settings;
        },
        getPostcodeInformation: function () {
            
            var self = this;
            var response = false;
			
			if (!this.source) {
                return;
            }
		
            var formData = this.source.get(this.customScope);

            this.validateRequest();
            this.postcodeCheckValid = null;
            this.isPostcodeCheckComplete = $.Deferred();
            this.checkRequest = getPostcodeInformation(this.isPostcodeCheckComplete,formData.experius_postcode_postcode,formData.experius_postcode_housenumber);

            this.isLoading(true);

            $.when(this.isPostcodeCheckComplete).done(function (data) {
                
                response = JSON.parse(data);
                
                self.debug(response);

                if (response.street) {
                    self.error(false);
                    
                    if(self.getSettings().useStreet2AsHouseNumber){
                        registry.get(self.parentName + '.street.0').set('value',response.street).set('error',false);
                        registry.get(self.parentName + '.street.1').set('value',response.houseNumber.toString()).set('error',false);
                        self.debug('address on two lines');
                    } else {
                        registry.get(self.parentName + '.street.0').set('value',response.street + ' ' + response.houseNumber).set('error',false);
                        self.debug('address on single line');
                    }
                    registry.get(self.parentName + '.country_id').set('value','NL').set('error',false);
                    registry.get(self.parentName + '.region_id').set('value',response.province).set('error',false);
                    registry.get(self.parentName + '.city').set('value',response.city).set('error',false);
                    registry.get(self.parentName + '.postcode').set('value',response.postcode).set('error',false);
                    
                    self.updatePreview();

                    self.setHouseNumberAdditions(response.houseNumberAdditions);
                    self.postcodeCheckValid = true;
                
                } else {

                    self.error(response.message);
                    self.notice(false);

                    registry.get(self.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition').set('visible',false);
                }

                self.isLoading(false);

            }).fail(function () {
                // fail
            }).always(function () {

            });

        },
        setHouseNumberAdditions: function(additions){

            if(registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition') && additions.length>1 && !registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_disable').get('value')) {
                var previousValue = registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition').value();
                var options = [];
                $.each(additions, function(key,addition){
                    if (!addition) {
                        options[key] = {'label':' - ','labeltitle':' - ','value':''};
                    } else {
                        var additionStripped = addition.replace(" ", "");
                        options[key] = {'label':additionStripped,'labeltitle':additionStripped,'value':additionStripped};
                    }
                });
                
                registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition').set('visible',true).set('options',options);
                registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition').value(previousValue);
            }  else if(registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition')) {
                registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition').set('visible',false);
                registry.get(this.parentName + '.experius_postcode_fieldset.experius_postcode_housenumber_addition').value('');
            }
        },
        validateRequest: function () {
            if (this.checkRequest != null && $.inArray(this.checkRequest.readyState, [1, 2, 3])) {
                this.checkRequest.abort();
                this.checkRequest = null;
            }
        },
        validate: function() {
            var isValid = !this.error() && this.postcodeCheckValid;
            if (!isValid) {
                this.source.set('params.invalid', true);
            }
            return {
                valid: isValid,
                target: this
            };
        },
        debug: function(message){
            if(this.getSettings().debug){
                console.log(message);
            }
        },
        updatePreview: function(){
            var preview = '<i>';
			
			if (!this.source) {
                return;
            }

            var address = this.source.get(this.customScope);

            $.each(address.street, function(index,street){
                preview += street + ' ';
            });

            preview += "<br/>" + address.postcode + "<br/>";
            preview += address.city;
            preview += "</i>"

            this.notice(preview);
        },
        postcodeHouseNumberAdditionHasChanged: function(newValue){

            var current_street_value = false;
            var new_street_value = false;
            var addition = false;
            if(newValue==undefined){
                return;
            }

            var parentPartentName = this.parentName;

            /* Needs refactoring */
            if(this.getSettings().useStreet2AsHouseNumber && registry.get(parentPartentName + '.street.1') && registry.get(parentPartentName + '.street.1').get('value')){
                current_street_value = this.removeOldAdditionFromString(registry.get(parentPartentName + '.street.1').get('value'));
                addition =  (newValue) ? ' ' +  newValue : '';
                new_street_value = current_street_value + addition;
                registry.get(parentPartentName + '.street.1').set('value',new_street_value);
            } else if(this.getSettings().useStreet3AsHouseNumberAddition && registry.get(parentPartentName + '.street.2')){
                registry.get(parentPartentName + '.street.2').set('value',newValue);
            } else if(registry.get(parentPartentName + '.street.0') && registry.get(parentPartentName + '.street.0').get('value')) {
                current_street_value = this.removeOldAdditionFromString(registry.get(parentPartentName + '.street.0').get('value'));
                addition =  (newValue) ? ' ' +  newValue : '';
                new_street_value = current_street_value + addition;
                registry.get(parentPartentName + '.street.0').set('value',new_street_value);
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
        }
    });
});
