define([
    "jquery"
], function($) {

    $.widget('experius.postcode', {
        options: {
            url: '',
            loaderIconUrl: '',
            ajax: null
        },

        _create: function () {
            this._initObservers();
        },

        _initObservers: function(){
            this._hideFields();
        },

        _hideFields: function(){
            $('div.field.street').hide();
            $('div.field.city').hide();
            $('div.field.region').hide();
        },

        _showFields: function(){

        },

        _save: function(observerData){

            var self = this;

            this._loader(observerData.reload,'show');
            
            var data = {};
            
            if(this.ajax) {
                this.ajax.abort();
            }

            this.ajax = $.ajax({
                type: "POST",
                url: this.options.url,
                data: data,
                success: function(response){
                    console.log(response);

                    if(observerData.reload!==undefined) {
                        self._updateContent(response.content);
                    }

                    if(observerData.redirect!==undefined) {
                        window.location.href=observerData.redirect;
                    }

                    self._loader(observerData.reload, 'hide');
                },
            });

        },

        _updateContent: function(content){

        },

        _loader: function(selectors,action){
            var loaderClassName = 'experius-postcode-loader';
            $.each(selectors, function(index,selector){
                var element =  $('#' + selector);
                if(action=='show'){
                    element.append('<div class="'+loaderClassName+'">reloading</div>');
                } else {
                    element.find('.'+loaderClassName).remove();
                }
            });
        },

    });

    return $.experius.postcode;
});
