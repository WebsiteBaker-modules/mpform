/**
 *   wbAjax Plugin
 *   Plugin to change the status of Form Fields (required, readonly, optional) 
 *   without a new page load (no reload)
 */        

/*
        ajaxChangeFormFieldStatus OPTIONS
        =====================================================================================
        MODULE = 'modulename',                                        // (string)
        DB_RECORD_TABLE: 'modulename_table',        // (string)
        DB_COLUMN: 'item_id',                                         // (string) the key column you will use as reference
        sFTAN: ''                                                                 // (string) FTAN 

*/
        
(function($) {
        $.fn.ajaxChangeFormFieldStatus = function(options) {                
                        var aOpts = $.extend({}, $.fn.ajaxChangeFormFieldStatus.defaults, options);
                        $(this).find('img').css('cursor', 'pointer');
                        
                        $(this).click(function() {
                                var oElement = $(this).find('img');
                                var iRecordID = oElement.attr("id").substring(4);
                                var iSectionID = oElement.attr("rel");
                                switch(oElement.attr("src")){
                                        case ICONS +"/optional.gif": var action = "required"; break;
                                        case ICONS +"/required.gif": var action = "readonly"; break;
                                        case ICONS +"/readonly.gif": var action = "optional"; break;
                                }
                                var sDataString = 'purpose=toggle_status&action='+action+'&DB_RECORD_TABLE='+aOpts.DB_RECORD_TABLE+'&DB_COLUMN='+aOpts.DB_COLUMN+'&MODULE='+aOpts.MODULE+'&iRecordID='+iRecordID+'&iSectionID=' + iSectionID;
                                jQuery.ajax({
                                        url: MODULE_URL +"/ajax/ajax.php",
                                        type: "POST",
                                        dataType: 'json',
                                        data: sDataString,
                                        success: function(json_respond)
                                        {
                                                if(json_respond.success == true) {
                                                        
                                                        oElement.animate({opacity: 0.55}), 'fast';
                                                        oElement.attr("src", ICONS +"/"+ action +".gif");
                                                        oElement.attr("title", LANG[json_respond.message]);
                                                        oElement.animate({opacity: 1});
                                                } else {
                                                        alert(json_respond.message);
                                                }
                                        }
                                });
                        return false;
                });
        }
})(jQuery);