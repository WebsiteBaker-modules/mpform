<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.3.4
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2017, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @url                 https://forum.wbce.org/viewtopic.php?id=661
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        probably php >= 5.3 ?
 *
 **/
/* This file prints the main form of the module in the backend. */
// prevent this file from being accessed directly
if (!defined('WB_PATH')) die(header('Location: ../../index.php'));

// obtain module directory
$mod_dir = basename(dirname(__FILE__));
require(WB_PATH.'/modules/'.$mod_dir.'/info.php');

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

require_once(dirname(__FILE__).'/constants.php');


// include the module language file depending on the backend language of the current user
if (!include(get_module_language_file($mod_dir))) return;

//Delete all form fields with no title
$database->query("DELETE FROM "
    . "`".TP_MPFORM."fields`  "
    . "WHERE `page_id` = '$page_id' "
    . "AND `section_id` = '$section_id' "
    . "AND `title` = '';"
);

// include template parser class and set template
if (file_exists(WB_PATH . '/include/phplib/template.inc')) 
    require_once(WB_PATH . '/include/phplib/template.inc');
$tpl = new Template(dirname(__FILE__) . '/htt/');
$tpl->set_unknowns('keep');
$tpl->debug = 0;

$tpl->set_file('page', 'backend_modify.htt');
$tpl->set_block('page', 'main_block', 'main');

// ensure that page and section id are numeric
$page_id = (isset($page_id)) ? (int) $page_id : '';
$section_id = (isset($section_id)) ? (int) $section_id : '';

$imgurl = THEME_URL . '/images/';

$tpl->set_var(
    array(
        // variables from Website Baker framework
        'PAGE_ID'            => (int) $page_id,
        'SECTION_ID'         => (int) $section_id,
        'IMG_URL'            => $imgurl,
        'WB_URL'             => WB_URL,
        'LANGUAGE'           => LANGUAGE,
        'MODULE_URL'         => WB_URL.'/modules/'.$mod_dir,
        'FTAN'               => method_exists( $admin, 'getFTAN' )  ? $admin->getFTAN() : '',
        
        // variables from global WB language files
        'TXT_SAVE'           => $TEXT['SAVE'],
        'TXT_CANCEL'         => $TEXT['CANCEL'],
        'TXT_HELP'           => $MENU['HELP'],
        'TEXT_HEADING_F'     => $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$TEXT['FIELD'] ,
        'TEXT_HEADING_S'     => $TEXT['SUBMISSIONS'], 
        'TEXT_DELETE'        => $TEXT['DELETE'],
        'TEXT_ARE_YOU_SURE'  => str_replace(' ', '%20', $TEXT['ARE_YOU_SURE']),
        'TEXT_FIELD'         => $TEXT['FIELD'],
        'TEXT_MOVE_UP'       => $TEXT['MOVE_UP'],
        'TEXT_MOVE_DOWN'     => $TEXT['MOVE_DOWN'],
        'TEXT_MODIFY'        => $TEXT['MODIFY'],
        'TEXT_SUBMISSION_ID' => $TEXT['SUBMISSION_ID'],
        'TEXT_SUBMITTED'     => $TEXT['SUBMITTED'],
        'TEXT_OPEN'          => $TEXT['OPEN'],

        // module settings
        'TXT_HEADING'        => $module_name,
        'MODULE_DIR'         => $mod_dir,
        'MOD_CANCEL_URL'     => ADMIN_URL,
        'TEXT_TYPE'          => $LANG['backend']['TXT_TYP'],
        'TXT_ADV_SETTINGS'   => $LANG['backend_adv']['adv_settings'],
        'TXT_FIELDS'         => $LANG['backend']['TXT_ADD_FIELD'],
        'TXT_SETTINGS'       => $LANG['backend']['TXT_SETTINGS'],
        'EDIT_CSS'           => $LANG['backend']['TXT_EDIT_CSS'],
        'TXT_EXPORT_FORM'    => $LANG['backend']['TXT_EXPORT_FORM'],
        'TXT_IMPORT_FORM'    => $LANG['backend']['TXT_IMPORT_FORM'],
        'TXT_EXPORT_SUBMISSIONS' => $LANG['backend']['TXT_EXPORT_SUBMISSIONS'],
    )
);

// Include the ordering class
require_once(WB_PATH.'/framework/class.order.php');
// Create new order object and reorder
$order = new order(TP_MPFORM.'fields', 'position', 'field_id', 'section_id');
$order->clean($section_id);
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');
$tpl->set_block('main_block', 'field_block' , 'field_loop');

// Loop through existing fields
$query_fields 
    = $database->query(
        "SELECT *"
        . " FROM `".TP_MPFORM."fields`"
        . " WHERE `section_id` = '$section_id'"
        . " ORDER BY `position` ASC");
$num_fields = $query_fields->numRows();
$pos = 0;
if($num_fields > 0) {
    while($field = $query_fields->fetchRow()) {
        $pos++;    
        
        // switch the different Form Field Types
        switch ($field['type']){
            case 'textfield':       
                $rt = $TEXT['SHORT_TEXT'];                  
            break;
            case 'textarea':        
                $rt = $TEXT['LONG_TEXT'];                   
            break;
            case 'hiddenfield':       
                $rt = $LANG['backend']["hiddenfield"];                  
            break;
            case 'heading':         
                $rt = $TEXT['HEADING'];                     
            break;
            case 'select':          
                $rt = $TEXT['SELECT_BOX'];                  
            break;
            case 'checkbox':        
                $rt = $TEXT['CHECKBOX_GROUP'];              
            break;
            case 'radio':           
                $rt = $TEXT['RADIO_BUTTON_GROUP'];          
            break;
            case 'filename':        
                $rt = $TEXT['UPLOAD_FILES'];                
            break;
            case 'date';            
                $rt = $TEXT['DATE'];                        
            break;
            case 'email':           
                $rt = $TEXT['EMAIL_ADDRESS'];               
            break;
            case 'fieldset_start':  
                $rt = $LANG['backend']['fieldset_start'];   
            break;
            case 'fieldset_end':    
                $rt = $LANG['backend']['fieldset_end'];     
            break;
            case 'integer_number':  
                $rt = $LANG['backend']['integer_number'];   
            break;
            case 'decimal_number':  
                $rt = $LANG['backend']['decimal_number'];   
            break;
            case 'email_recip':     
                $rt = $LANG['backend']['email_recip'];      
            break;
            case 'email_subj':      
                $rt = $LANG['backend']['email_subj'];       
            break;
            case 'html':            
                $rt = $LANG['backend']['HTML'];
            break;
            case 'conditional':        
                $rt = $LANG['backend']['conditional'];                 
            break;
            default:                
                $rt = '';
        }
        $multiselect_field='';
        if ($field['type'] == 'select') {
            $field['extra'] = explode(',',$field['extra']);
            $multiselect_txt = $TEXT['MULTISELECT'] .': '
                .(($field['extra'][1] == 'multiple') ? $TEXT['YES'] : $TEXT['NO']);
            $multiselect_img = WB_URL.'/modules/'.$mod_dir.'/images/'
                .(($field['extra'][1] == 'multiple') ? "select_multiple.gif" : "select.gif");
            $multiselect_field 
                = "<img src='$multiselect_img' border='0'"
                . " alt='$multiselect_txt' title='$multiselect_txt' />";
        }
        
        switch($field['required']){
            case 1:
                $entry = $LANG['backend']['compulsory_entry'];
                $entrytype = 'required';
            break;            
            case 2:
                $entry = $LANG['backend']['ro_entry'];
                $entrytype = "readonly";
            break;
            case 0:
                $entry = $LANG['backend']['optional_entry'];
                $entrytype = "optional";
            break;    
            default:
                $entry = $LANG['backend']['disabled_entry'];
                $entrytype = "disabled";
            break;    
        } 
        $sIconSrc = WB_URL. "/modules/".$mod_dir."/images/".$entrytype.".gif";
        $sRequiredIcon 
            = sprintf('<img id="req_%s" rel="%d" src="'.$sIconSrc.'" alt="" title="%s" />',
                 $field['field_id'], $section_id, $entry 
            );
        
        // set vars for this field
        $tpl->set_var(
            array(
                'FTAN'               => $admin->getFTAN(),
                'FIELD_ID'           => $field['field_id'],
                'FIELD_IDKEY'        => ( method_exists( $admin, 'getIDKEY' ) 
                                          ? $admin->getIDKEY($field['field_id'])
                                          : $field['field_id']),
                'MOVE_UP_STYLE'      => (($pos != 1) ? '' : 'style="display:none"'),
                'MOVE_DOWN_STYLE'    => (($pos != $num_fields) ? '' : 'style="display:none"'),
                // Alternate row color (even/odd zebra style):                
                'ROW_CLASS'          => $pos %2  ? 'even' : 'odd', 
                'field_field_title'  => $field['title'].' (ID: '.$field['field_id'].')',
                'field_title'        => $field['title'],
                'type_field'         => $rt,
                'entrytype'          => $entrytype,        
                'REQUIRED_ICON'      => $sRequiredIcon,        
                'multiselect_field'  => $multiselect_field,
            )
        );
        $tpl->parse('field_loop', 'field_block', true);
    }
} else {
    $tpl->set_var('field_loop', $TEXT['NONE_FOUND']);
}

$tpl->set_block('main_block', 'submission_block' , 'submission_loop');

// Query submissions table
$query_submissions 
    = $database->query(
        "SELECT * FROM `".TP_MPFORM."submissions`"
           . " WHERE section_id = '$section_id'"
           . " ORDER BY submitted_when ASC"
    );
if($query_submissions->numRows() > 0) {
    // List submissions
    $pos = 0;
    while($submission = $query_submissions->fetchRow()) {
        $pos++;
        $tpl->set_var(
            array(
                'SUBMISSION_ID'          => method_exists( $admin, 'getIDKEY' )  
                                            ? $admin->getIDKEY($submission['submission_id']) 
                                            : $submission['submission_id'],
                                            // Alternate row color (even/odd zebra style):
                'ROW_CLASS'              => $pos %2  ? 'even' : 'odd', 
                'field_submission_id'    => $submission['submission_id'],
                'submissionIDKEY'        => method_exists( $admin, 'getIDKEY')
                                            ? $admin->getIDKEY($field['submission_id'])
                                            : $field['submission_id'],
                'field_submission_when'  => date(TIME_FORMAT.', '.DATE_FORMAT, 
                                                 $submission['submitted_when']),
            )
        );
        $tpl->parse('submission_loop', 'submission_block', true);
    }
} else {
    $tpl->set_var('submission_loop','<tr><td>'.$TEXT['NONE_FOUND'].'</td></tr>');
}
// Parse template objects output
$tpl->parse('main', 'main_block', false);
$tpl->pparse('output', 'page', false, false);

$redirect_timer 
    = ((defined('REDIRECT_TIMER')) && (REDIRECT_TIMER <= 10000)) 
    ? REDIRECT_TIMER 
    : 0;


?>
<script type="text/javascript"> 
    /* <![CDATA[ */    
        var LANGUAGE = '<?php echo LANGUAGE ?>'; 
        var REDIRECT_TIMER =   <?php echo $redirect_timer ?>;              
    /* ]]> */
</script>
