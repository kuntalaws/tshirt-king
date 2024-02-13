jQuery(window).on('load',function(){
    if(typeof tkawsSelectedTemplateCategories == 'object' && typeof tkawsSelectedTemplate == 'object'){
        console.log(tkawsSelectedTemplateCategories[0]);

        if(tkawsSelectedTemplateCategories[0]){
            /* lumise.actions.add('object:added', function(param){
                console.log(param,'param');
                jQuery('.lumise-left-nav li[data-tab="templates"]').trigger('click');
            });
            lumise.actions.add('after:render', function(param){
                console.log(param,'after:render');
            }); */
            setTimeout(function(){
                jQuery('.lumise-left-nav li[data-tab="templates"]').trigger('click');
            },2000);
            setTimeout(function(){
                jQuery('.lumise-template-categories button').trigger('click');
            },2000);
            setTimeout(function(){
                var stc = tkawsSelectedTemplateCategories[0];            
                for(var key in stc){
                    if(jQuery('.lumise-categories-wrp .smooth li[data-id="'+stc[key]+'"]').length > 0){
                        jQuery('.lumise-categories-wrp .smooth li[data-id="'+stc[key]+'"]').trigger('click');
                        if (typeof tkawsSelectedTemplate[0].name !== 'undefined') {
                            jQuery('#lumise-templates-search-inp').val(tkawsSelectedTemplate[0].name);
                            jQuery('#lumise-templates-search-inp').focus();

                            setTimeout(function(){
                                // create a new keyboard event and set the key to "Enter"
                                const event = new KeyboardEvent('keydown', {
                                    key: 'Enter',
                                    code: 'Enter',
                                    which: 13,
                                    keyCode: 13,
                                });
                                
                                // dispatch the event on some DOM element
                                document.getElementById('lumise-templates-search-inp').dispatchEvent(event);

                            },2000);

                            setTimeout(function(){
                                jQuery('.lumise-list-items li').each(function(){
                                    var dataOps = jQuery(this).data('ops');
                                    if(typeof dataOps[0].id !='undefined' && dataOps[0].id == tkawsSelectedTemplate[0].id){
                                        jQuery(this).trigger('click');
                                        jQuery('.lumise-left-nav li[data-tab="product"]').trigger('click');
                                        jQuery('#lumise-x-thumbn-preview').fadeOut();
                                        jQuery('.loader').fadeOut();
                                    }
                                });
                            },3000);

                        }
                    }
                };
            },3000);
        }
    }
    //tkawsSelectedTemplate 
});