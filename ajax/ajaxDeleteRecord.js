/**
 * AJAX
 *        Plugin to delete Records from a given table without a new page load (no reload)
 */        
// Building a jQuery Plugin
// using the Tutorial: http://www.learningjquery.com/2007/10/a-plugin-development-pattern
// plugin definition
/*
        ajaxDeleteRecord OPTIONS
        =====================================================================================
        MODULE = 'modulename',                                        // (string)
        DB_RECORD_TABLE: 'modulename_table',        // (string)
        DB_COLUMN: 'item_id',                                         // (string) the key column you will use as reference
        sFTAN: ''                                                                 // (string) FTAN 

*/
        
(function($) {
        $.fn.ajaxDeleteRecord = function(options) {
                
                var aOpts = $.extend({}, $.fn.ajaxDeleteRecord.defaults, options);                
                $(this).find('a').removeAttr("href").css('cursor', 'pointer');
                
                $(this).click(function() {
                        var oElement = $(this).find('img');                
                        var oLink = $(this).find('a');                
                        var iRecordID = oElement.attr("id").substring(4);
                        var iSectionID = oElement.attr("rel");
                        var oRecord = $("tr#" + aOpts.DB_COLUMN +'_'+ iRecordID);        
                        
                        if (confirm(LANG.SURE_DELETE_RECORD)) {                                        
                                // pregenerate the data string
                                var sDataString = 'purpose=delete_record&action=delete&DB_RECORD_TABLE='+aOpts.DB_RECORD_TABLE+'&DB_COLUMN='+aOpts.DB_COLUMN+'&MODULE='+aOpts.MODULE+'&iRecordID='+iRecordID+'&iSectionID=' + iSectionID;

                                $.ajax({
                                        url: MODULE_URL +"/ajax/ajax.php",
                                        type: "POST",
                                        dataType: 'json',
                                        data: sDataString,                                                
                                        success: function(json_respond) {
                                                if(json_respond.success == true) {
                                                        //row.fadeOut('slow');
                                                        oRecord.fadeOut(1200);        
                                                        // alert(json_respond.message + ' ' + sDataString);        // debug
                                                } else {
                                                        alert(json_respond.message);
                                                }
                                        }                
                                });
                        }
                });
        }        
})(jQuery);
