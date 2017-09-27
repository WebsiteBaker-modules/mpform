var MODULE_URL = WB_URL + '/modules/mpform';
var ICONS = MODULE_URL + '/images';
var AJAX_PLUGINS =  MODULE_URL + '/ajax';  // this var could change in the future
var LANGUAGE = LANGUAGE ? LANGUAGE : 'EN'; // set var LANGUAGE to EN if LANGUAGE not set before
var REDIRECT_TIMER = REDIRECT_TIMER ? REDIRECT_TIMER : 1500; // ms
$.insert(AJAX_PLUGINS +"/localization.js"); // load external language file

// @function:       getUrlVars (retrieve GET Parameters)
// @purpose:        Read a page's GET URL variables and return them as an associative array.
//                  we use this script along with jNotify
// @source:         http://jquery-howto.blogspot.com/2009/09/get-url-parameters-values-with-jquery.html
function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}
$(function() {

    $.insert( AJAX_PLUGINS +"/ajaxDeleteRecord.js");
    // AjaxHelper delete fields records
    $("td.delete_field").ajaxDeleteRecord({
        MODULE : 'mpform',
        DB_RECORD_TABLE: 'mpform_fields',
        DB_COLUMN: 'field_id',
        sFTAN: ''
    });

    // AjaxHelper delete submissions records
    $("td.delete_submission").ajaxDeleteRecord({
        MODULE : 'mpform',
        DB_RECORD_TABLE: 'mpform_submissions',
        DB_COLUMN: 'submission_id',
        sFTAN: ''
    });

    $.insert( AJAX_PLUGINS +"/ajaxChangeFormFieldStatus.js");
    // AjaxHelper change FormFieldStatus
    $("td.required_status").ajaxChangeFormFieldStatus({
        MODULE : 'mpform',
        DB_RECORD_TABLE: 'mpform_fields',
        DB_COLUMN: 'field_id',
        sFTAN: ''
    });


    function inspectAllInputFields(){
        // disable BUTTONS as long requiredFields ain't filled
        var count = 0;
        var oFields = $('#save_settings, #copy_field, #add_field');
        $('.requiredInput').each(function(i){
            if( $(this).val() === '')
                count++;
            if(count == 0)
                oFields.prop('disabled', false);
            else {
                oFields.prop('disabled', true);
            }
        });
    }
    // disable BUTTONS as long requiredFields ain't filled
    if($('#field_title').val() === '')
        $('#save_settings, #copy_field, #add_field').prop('disabled', true);

    $('.requiredInput').change(function() {
       inspectAllInputFields();
    });


    // also when the mouse pointer goes over one of the buttons, update the status
    $('.mod_mpform_button').mouseover(function() {
       inspectAllInputFields();
    });



    /**
     *
     *     LOAD
     *    jNotify
     *    @documentation: http://www.myjqueryplugins.com/jNotify/
     */
    if(getUrlVars()["success"])
    {
        var MESSAGE = getUrlVars()["success"];
        switch (MESSAGE){
            case 'add':   var SUCCESS_STRING = LANG.RECORD_NEW_SAVED;            break;
            case 'save':  var SUCCESS_STRING = LANG.SETTINGS_SAVED;              break;
            default:      var SUCCESS_STRING = '<b>' + LANG.SUCCESS + '!</b>';   break;
        }
        $.insert( AJAX_PLUGINS +"/jNotify.jquery.js");
        jSuccess( SUCCESS_STRING,
            {
                // jNotify Settings
                autoHide : true, // added in v2.0
                TimeShown : REDIRECT_TIMER,
                HorizontalPosition : 'center',
                VerticalPosition : 'top',
                ColorOverlay : '#FFF'
            }
        );
    }

    if($('.pry').length > 0){
        // LOAD ajaxPryFunction (cluetip)
        $.insert(AJAX_PLUGINS +"/ajaxPryFunction.js");
    }

    // Load external ajax_dragdrop file
    if($('.dragdrop_form').length > 0){
        $.insert(AJAX_PLUGINS +"/ajax_dragdrop.js");
    }

    /*
        jEditable
        @purpose: inline edit
    */
    if($('.inlineEdit, .inlineEditArea').length > 0){
        jQuery.insert(AJAX_PLUGINS +"/jquery.jeditable.js");
        $('.inlineEdit').editable(AJAX_PLUGINS +"/inline_save.php", {
            indicator: 'saving',
            tooltip: LANG.DOUBLECLICK_TO_EDIT,
            placeholder: 'empty',
            event: "dblclick"
        });
        $('.inlineEditArea').editable(AJAX_PLUGINS +"/inline_save.php", {
            type: 'textarea',
            cancel: LANG.CANCEL,
            submit: LANG.SAVE,
            indicator: 'saving',
            tooltip: LANG.DOUBLECLICK_TO_EDIT,
            placeholder: 'empty',
            event: "dblclick"
        });
    }
});
