<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.25
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2019, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* This file prints the settings FOR A FIELD OF THE FORM in the backend. */
// manually include the config.php file (defines the required constants)
require('../../config.php');

// $admin_header = false;
// Tells script to update when this page was last updated
$update_when_modified = false;
// show the info banner
$print_info_banner = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;

require_once(WB_PATH.'/modules/'.$mod_dir.'/constants.php');

// Get id
if ( method_exists( $admin, 'checkIDKEY' )
    && (!(defined('MPFORM_SKIP_IDKEY')&&(MPFORM_SKIP_IDKEY))) ) {
    $field_id = $admin->checkIDKEY('field_id', false, 'GET');
    if (!$field_id) {
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
            .' (IDKEY) '.__FILE__.':'.__LINE__,
            ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
        exit();
    }
} else {
    if((!isset($_GET['field_id']) || !is_numeric($_GET['field_id']))
        && (!(defined('MPFORM_SKIP_ID_CHECK')&&(MPFORM_SKIP_ID_CHECK)))) {
        $sUrlToGo = ADMIN_URL."/pages/index.php";
        if(headers_sent())
            $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
            .' (ID_CHECK) '.__FILE__.':'.__LINE__,
            $sUrlToGo);
        else
               header("Location: ". $sUrlToGo);
        exit(0);
    } else {
        $field_id = $_GET['field_id'];
    }
}

// Get header and footer
$query_content
    = $database->query(
        "SELECT *"
            . " FROM ".TP_MPFORM."fields"
            . " WHERE field_id = '$field_id'"
    );
$form = $query_content->fetchRow();
$type = $form['type'];
if($type == '') {
    $type = 'none';
}

// protect from cross page reading
if (($form['page_id'] != $page_id)
    && (!(defined('MPFORM_SKIP_ID_CHECK')&&(MPFORM_SKIP_ID_CHECK)))) {
    $sUrlToGo = ADMIN_URL."/pages/index.php";
    if(headers_sent())
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
        .' (ID_CHECK) '.__FILE__.':'.__LINE__,
        $sUrlToGo);
    else
        header("Location: ". $sUrlToGo);
    exit(0);
}

// include template parser class and set template
if (!class_exists('Template') && file_exists(WB_PATH . '/include/phplib/template.inc'))
    require_once(WB_PATH . '/include/phplib/template.inc');
$tpl = new Template(dirname(__FILE__) . '/htt/');

// define how to handle unknown variables
//(default:='remove', during development use 'keep' or 'comment')
$tpl->set_unknowns('keep');

// define debug mode (default:=0 (disabled), 1:=variable assignments,
// 2:=calls to get variable, 4:=show internals)
$tpl->debug = 0;

$tpl->set_file('page', 'backend_modify_field.htt');
$tpl->set_block('page', 'main_block', 'main');

// list possible field types
$tpl->set_block('main_block', 'field_block', 'field_loop');
$fieldtypes = array (
    "heading"        => $TEXT['HEADING'],
    "fieldset_start" => $LANG['backend']['fieldset_start'],
    "fieldset_end"   => $LANG['backend']['fieldset_end'],
    "textfield"      => $LANG['backend']["textfield"],
    "textarea"       => $LANG['backend']["textarea"],
    "select"         => $TEXT['SELECT_BOX'],
    "checkbox"       => $TEXT['CHECKBOX_GROUP'],
    "radio"          => $TEXT['RADIO_BUTTON_GROUP'],
    "email"          => $TEXT['EMAIL_ADDRESS'],
    "email_recip"    => $LANG['backend']['email_recip'],
    "email_subj"     => $LANG['backend']['email_subj'],
    "date"           => $LANG['backend']['date'],
    "filename"       => $LANG['backend']['fileupload'],
    "integer_number" => $LANG['backend']['integer_number'],
    "decimal_number" => $LANG['backend']['decimal_number'],
    "html"           => $LANG['backend']['HTML'],
    "hiddenfield"    => $LANG['backend']["hiddenfield"],
    "conditional"    => $LANG['backend']["conditional"]
);
foreach ($fieldtypes as $k => $v) {
    $selected = ($k == $type) ? " selected=\"selected\">" : ">";
    $tpl->set_var('VAL_FIELDTYPE', '"'. $k . '"'. $selected . $v);
    $tpl->parse('field_loop', 'field_block', true);
}

// show additional fields depending on type
$tpl->set_block('main_block', 'type_options', 'typeoptions');
$fieldtypeoption = "";
// first round:
switch ($type) {
    case 'heading':
        $use_in_form="";
        $use_in_site_html="";
        $use_in_user_html="";
        if(($form['value'] == '') or (preg_match('/form/',$form['value'])))
            $use_in_form=" checked='checked'";
        if(($form['value'] == '') or (preg_match('/site/',$form['value'])))
            $use_in_site_html=" checked='checked'";
        if(($form['value'] == '') or (preg_match('/user/',$form['value'])))
            $use_in_user_html=" checked='checked'";
        $fieldtypeoption = "<tr>\n"
        ."<th>". $TEXT['TEMPLATE'] ."</th>\n"
        ."<td><textarea name='template'  cols='50' rows='5' style='width: 98%; height: 200px;'>". $form['extra'] ."</textarea></td>\n"
        ."</tr>\n"
        ."<tr><th>".$LANG['backend']['TXT_WHERE_USE_HTML']."</th>\n<td>"
        ."<input type='checkbox' name='use_in_form'".$use_in_form." />"
        .'<label for="use_in_form">'. $LANG['backend']['TXT_USE_IN_FORM'] ."</label>"
        ."<input type='checkbox' name='use_in_site_html'".$use_in_site_html." />"
        .'<label for="use_in_site_html">'. $LANG['backend']['TXT_USE_IN_SITE_HTML'] ."</label>"
        ."<input type='checkbox' name='use_in_user_html'".$use_in_user_html." />"
        .'<label for="use_in_user_html">'. $LANG['backend']['TXT_USE_IN_USER_HTML'] ."</label>"
        ."</td></tr>\n";
        $form['required'] |= 3;
        break;
    case 'textfield':
    case 'integer_number':
    case 'decimal_number':
    case 'email_subj':
        $fieldtypeoption = "<tr>\n"
        ."<th>". $TEXT['LENGTH'] .":</th>\n"
        ."<td><input type='text' name='length' value='". $form['extra']
        ."' style='width: 98%;' maxlength='3' /></td>\n"
        ."</tr>\n"
        ."<tr>\n"
        ."<th>". $LANG['backend']['TXT_DEFAULT'] .":</th>\n"
        ."<td><input type='text' name='value' value='". $form['value']
        ."' style='width: 98%;' /></td>\n"
        ."</tr>\n";
        break;
    case 'hiddenfield':
        $fieldtypeoption = "<tr>\n"
        ."<th>". $LANG['backend']['TXT_DEFAULT'] .":</th>\n"
        ."<td><input type='text' name='value' value='". $form['value']
        ."' style='width: 98%;' /></td>\n"
        ."</tr>\n";
        break;
    case 'textarea':
        $cr = explode(',', $form['extra']);
        if (isset($cr[0]) and is_numeric($cr[0])) {
            $cols = $cr[0];
        } else {
            $cols = 25;
        }
        if (isset($cr[1]) and is_numeric($cr[1])) {
            $rows = $cr[1];
        } else {
            $rows = 5;
        }
        if (isset($cr[2]) and is_numeric($cr[2])) {
            $maxlength = $cr[2];
        } else {
            $maxlength = '';
        }
        $fieldtypeoption = "<tr>\n"
        ."<th>". $TEXT['WIDTH'] .":</th>\n"
        ."<td><input type='text' name='width' value='$cols'"
        ." style='width: 98%;' maxlength='3' /></td>\n"
        ."</tr>\n"
        ."<tr>\n"
        ."<th>". $LANG['backend']['ROWS'] .":</th>\n"
        ."<td><input type='text' name='rows' value='$rows'"
        ." style='width: 98%;' maxlength='3' /></td>\n"
        ."</tr>\n"
        ."<tr>\n"
        ."<th>". $LANG['backend']['TXT_DEFAULT'] .":</th>\n"
        ."<td><textarea name='value' cols='50' rows='5'"
        ." style='width: 98%; height: 100px;'>". $form['value'] ."</textarea></td>\n"
        ."</tr>\n"
        ."<tr>\n"
        ."<th>". $TEXT['LENGTH'] .":</th>\n"
        ."<td><input type='text' name='maxlength' value='". $maxlength
        ."' style='width: 98%;' maxlength='3' /></td>\n"
        ."</tr>\n";
        break;
    case 'conditional': // get all fields
        $query_fields
            = $database->query(
                "SELECT *"
                . " FROM `".TP_MPFORM."fields`"
                . " WHERE `section_id` = '$section_id'"
                . " AND ( `type` = 'checkbox'"
                . "   OR  `type` = 'radio'"
                . "   OR  `type` = 'select')"
                . " ORDER BY `position` ASC");
        if($query_fields->numRows() > 0) {
            $fieldtypeoption = "<tr>\n"
             . "<th>". $LANG['backend']['dependency'] .":</th>"
             . '<td>'
             . '<select name="value" class="requiredInput" id="select_value" style="width: 30%;">'
             . '<option value="">'
             . $TEXT['PLEASE_SELECT']
             . '...</option>';
            while($field = $query_fields->fetchRow()) {
              $fieldtypeoption .= '<option value="'.$field['field_id'].'">'.$field['title']."</option>\n";
            }
            $fieldtypeoption .= "</select></td></tr>\n";
        }
        break;
    case 'html':
        $use_in_form="";
        $use_in_site_html="";
        $use_in_user_html="";
        if(($form['extra'] == '') or (preg_match('/form/',$form['extra'])))
            $use_in_form=" checked='checked'";
        if(($form['extra'] == '') or (preg_match('/site/',$form['extra'])))
            $use_in_site_html=" checked='checked'";
        if(($form['extra'] == '') or (preg_match('/user/',$form['extra'])))
            $use_in_user_html=" checked='checked'";

        $fieldtypeoption = "<tr>\n"
        ."<th>". $LANG['backend']['HTML'] .":</th>\n"
        ."<td><textarea name='value' cols='80' rows='8'"
        ." style='width: 98%; height: 200px;'>". $form['value'] ."</textarea></td>\n"
        ."</tr>\n"
        ."<tr><th>".$LANG['backend']['TXT_WHERE_USE_HTML']."</th>\n<td>"
        ."<input type='checkbox' name='use_in_form'".$use_in_form." />"
        .'<label for="use_in_form">'. $LANG['backend']['TXT_USE_IN_FORM'] ."</label>"
        ."<input type='checkbox' name='use_in_site_html'".$use_in_site_html." />"
        .'<label for="use_in_site_html">'. $LANG['backend']['TXT_USE_IN_SITE_HTML'] ."</label>"
        ."<input type='checkbox' name='use_in_user_html'".$use_in_user_html." />"
        .'<label for="use_in_user_html">'. $LANG['backend']['TXT_USE_IN_USER_HTML'] ."</label>"
        ."</td></tr>\n";
        $form['required'] |= 3;
        break;
    case 'date':
        $fieldtypeoption = "<tr>\n"
        ."<th>". $TEXT['LENGTH'] .":</th>\n"
        ."<td><input type='text' name='length' value='". $form['extra']
        ."' style='width: 98%;' maxlength='3' /></td>\n"
        ."</tr>\n"
        ."<tr>\n"
        ."<th>". $LANG['backend']['TXT_DEFAULT'] .":</th>\n"
        ."<td><input type='text' name='value' value='". $form['value']
        ."' style='width: 98%;' /></td>\n"
        ."</tr>\n";
        break;
    case 'email':
        $fieldtypeoption = "<tr>\n"
        ."<th>". $TEXT['LENGTH'] .":</th>\n"
        ."<td><input type='text' name='length' value='". $form['extra']
        ."' style='width: 98%;' maxlength='3' /></td>\n"
        ."</tr>\n"
        ."<tr>\n"
        ."<th>". $LANG['backend']['TXT_DEFAULT'] .":</th>\n"
        ."<td><input type='text' name='value' value='". $form['value']
        ."' style='width: 98%;' /></td>\n"
        ."</tr>\n";
        break;
    case 'filename':
        $fieldtypeoption = "<tr>\n"
        ."<th>". $TEXT['LENGTH'] .":</th>\n"
        ."<td><input type='text' name='length' value='". $form['extra']
        ."' style='width: 98%;' maxlength='3' /></td>\n"
        ."</tr>\n";
        break;
    case 'select':
    case 'checkbox':
    case 'radio':
        ($type == 'radio') ? $kind = 'radio' : $kind = 'checkbox';
        $fieldtypeoption = "<tr>\n"
        //."<tr>\n"
        ."<th>". $LANG['backend']['TXT_LIST'] .":</th>\n"
        ."<td>";

        $option_count = 0;
        $imgurl = THEME_URL . '/images/';
        $list = explode(',', $form['value']);
        foreach($list AS $option_value) {
            $def = strpos($option_value, MPFORM_IS_DEFAULT);
            if ($def > 0) {
                $ovalue = substr($option_value, 0, $def);
                $cv = " checked='checked'";
            } else {
                $ovalue = $option_value;
                $cv = "";
            }
            $ovalue=htmlspecialchars(str_replace('&#44;', ',', $ovalue), ENT_QUOTES);
            $option_count = $option_count+1;
            ($type == 'radio') ? $isdef = "isdefault" : $isdef = "isdefault$option_count";
            $fieldtypeoption .= '<table cellpadding="3" cellspacing="0" width="100%" border="0">'
                ."<tr>\n<td width='70'>". $TEXT['OPTION'].' '.$option_count .":</td>\n"
                ."<td><input type='text' name='value$option_count'"
                ." value='$ovalue' style='width: 250px;' /> "
                ."<input type='$kind' name='$isdef' value='$option_count' $cv /></td>\n"

                ."<td width='20' class='move_position'><a href='#' "
                . (($option_count != 1) ? '' : 'style="display:none"') . " onclick='"
                ."var value$option_count "
                ."  = document.getElementsByName(\"value$option_count\")[0].value; "
                ."document.getElementsByName(\"value$option_count\")[0].value"
                ."  = document.getElementsByName(\"value".($option_count-1)."\")[0].value; "
                ."document.getElementsByName(\"value".($option_count-1)."\")[0].value"
                ."  = value$option_count; return false;' title=\"".$TEXT['MOVE_UP']."\">"
                ."<img src=\"$imgurl/up_16.png\" border='0' alt='^' /></a></td>\n"

                ."<td width='20' class='move_position'><a href='#' onclick='"
                ."var value$option_count "
                ."  = document.getElementsByName(\"value$option_count\")[0].value; "
                ."document.getElementsByName(\"value$option_count\")[0].value"
                ."  = document.getElementsByName(\"value".($option_count+1)."\")[0].value; "
                ."document.getElementsByName(\"value".($option_count+1)."\")[0].value"
                ."  = value$option_count; return false;' title=\"".$TEXT['MOVE_DOWN']."\">"
                ."<img src=\"$imgurl/down_16.png\" border='0' alt='v' /></a></td>\n"

                ."</tr></table>\n";
        }
        for($i = 0; $i < 2; $i++) {
            $option_count = $option_count+1;
            ($type == 'radio') ? $isdef = "isdefault" : $isdef = "isdefault$option_count";
            $fieldtypeoption .= '<table cellpadding="3" cellspacing="0" width="100%" border="0">'
                ."<tr>\n<td width='70'>". $TEXT['OPTION'].' '.$option_count .":</td>\n"
                ."<td><input type='text' name='value$option_count'"
                ." value='' style='width: 250px;' /> "
                ."<input type='$kind' name='$isdef' value='$option_count' /></td>\n"

                ."<td width='20' class='move_position'><a href='#' onclick='"
                ."var value$option_count "
                ."  = document.getElementsByName(\"value$option_count\")[0].value; "
                ."document.getElementsByName(\"value$option_count\")[0].value"
                ."  = document.getElementsByName(\"value".($option_count-1)."\")[0].value; "
                ."document.getElementsByName(\"value".($option_count-1)."\")[0].value"
                ."  = value$option_count; return false;' title=\"".$TEXT['MOVE_UP']."\">"
                ."<img src=\"$imgurl/up_16.png\" border='0' alt='^' /></a></td>\n"

                ."<td width='20' class='move_position'><a href='#' "
                . (($i != 1) ? '' : 'style="display:none"') . " onclick='"
                ."var value$option_count "
                ."  = document.getElementsByName(\"value$option_count\")[0].value; "
                ."document.getElementsByName(\"value$option_count\")[0].value"
                ."  = document.getElementsByName(\"value".($option_count+1)."\")[0].value; "
                ."document.getElementsByName(\"value".($option_count+1)."\")[0].value"
                ."  = value$option_count; return false;' title=\"".$TEXT['MOVE_DOWN']."\">"
                ."<img src=\"$imgurl/down_16.png\" border='0' alt='v' /></a></td>\n"

                ."</tr></table>\n";
            }
        $fieldtypeoption .= "<input type='hidden' name='list_count'"
            ." value='$option_count' /></td>\n</tr>\n";
        break;
    case 'fieldsetstart':
    case 'fieldsetend':
    case 'none':
        $form['required'] |= 3;

}  // switch ($type)

// second round:
if($type == 'select') {
    $form['extra'] = explode(',',$form['extra']);
    $fieldtypeoption .= "<tr>\n<th>". $TEXT['SIZE'] .":</th>\n";
    $fieldtypeoption .= '<td><input type="text" name="size" value="'. trim($form['extra'][0])
        .'" style="width: 98%;" maxlength="3" /></td>';
    $fieldtypeoption .= "\n</tr>\n<tr>\n";
    $fieldtypeoption .= "<th>". $TEXT['ALLOW_MULTIPLE_SELECTIONS'] .":</th>\n";
    $fieldtypeoption .= '<td><input type="radio" name="multiselect"'
        .' id="multiselect_true" value="multiple"';
    if($form['extra'][1] == 'multiple') $fieldtypeoption .= ' checked="checked"';
    $fieldtypeoption .= " />";
    $fieldtypeoption .= '<label for="multiselect_true">';
    $fieldtypeoption .= $TEXT['YES'] ."</label>";
    $fieldtypeoption .= '<input type="radio" name="multiselect"'
        .' id="multiselect_false" value=""';
    if($form['extra'][1] == '') $fieldtypeoption .= ' checked="checked"';
    $fieldtypeoption .= " />";
    $fieldtypeoption .= '<label for="multiselect_false">';
    $fieldtypeoption .= $TEXT['NO'] ."</label></td>\n</tr>\n";
}
if($type == 'checkbox' OR $type == 'radio') {
    $fieldtypeoption .= "<tr>\n<th>". $TEXT['SEPERATOR'] .":</th>\n";
    $fieldtypeoption .= '<td><input type="text" name="seperator" value="'. $form['extra']
        .'" style="width: 98%;" />'."</td>\n</tr>\n";
}

$fieldtypeoption .= "<tr>\n<th>". $LANG['backend']['entry'] .":</th>\n<td>";

if( $type != 'heading'
    AND $type != 'fieldset_start'
    AND $type != 'fieldset_end'
    AND $type != 'none'
    AND $type != 'html'
    AND $type != 'conditional') {
        $fieldtypeoption .= '<input type="radio"'
            .' name="required" id="required_true" value="1"';
        if(($form['required'] & 3 ) == 1 OR $type == 'email_recip') {
            $fieldtypeoption .= ' checked="checked"';
        }
        $fieldtypeoption .= " />";
        $fieldtypeoption .= '<label for="required_true">'
           . $LANG['backend']['compulsory_entry'] ."</label>\n";
        $fieldtypeoption .= '<input type="radio" name="required"'
           .' id="required_false" value="0"';
        if(($form['required'] & 3 ) == 0 AND $type != 'email_recip') {
            $fieldtypeoption .= ' checked="checked"';
        }
        $fieldtypeoption .= " />";
        $fieldtypeoption .= '<label for="required_false">'
           .$LANG['backend']['optional_entry'] ."</label>";
        $fieldtypeoption .= '<input type="radio" name="required"'
            .' id="required_ro" value="2"';
        if(($form['required'] & 2 ) == 2 AND $type != 'email_recip'){
            $fieldtypeoption .= ' checked="checked"';
        }
        $fieldtypeoption .= " />";
        $fieldtypeoption .= '<label for="required_ro">'
           . $LANG['backend']['ro_entry'] ."</label>";
}

$fieldtypeoption .= '<input type="checkbox" name="disabled" id="disabled"';
if(($form['required'] & 4) != 0){
    $fieldtypeoption .= ' checked="checked"';
}
$fieldtypeoption .= " />";
$fieldtypeoption .= '<label for="disabled">'
    .$LANG['backend']['disabled_entry']
    ."</label></td>\n</tr>\n";

if( $type != 'heading'
    AND $type != 'fieldset_start'
    AND $type != 'fieldset_end'
    AND $type != 'none'
    AND $type != 'html'
    AND $type != 'conditional') {
        $fieldtypeoption .= "<tr>\n<th>". $MENU['HELP'] .":</th>\n";
        $fieldtypeoption .= '<td><textarea name="help"  cols="50" rows="5"'
            .' style="width: 98%; height: 100px;">'
            . $form['help'] ."</textarea></td>\n</tr>\n";


        // obtain field loop from the database to check if we need the template at all
        $table = TP_MPFORM.'settings';
        $sql = "SELECT *"
             . " FROM `$table`"
             . " WHERE `section_id` = '$section_id'";
        $sql_result = $database->query($sql);
        $settings = $sql_result->fetchRow();
        if(preg_match('/{FORMATTED_FIELD}/',$settings['field_loop']) ||
           ( preg_match('/{TEMPLATE/',$settings['field_loop'])
             && preg_match('/{FORMATTED_FIELD}/',$form['template'])) ||
           preg_match('/{TEMPLATE/',$settings['heading_html']) ||
           preg_match('/{TEMPLATE/',$settings['short_html']) ||
           preg_match('/{TEMPLATE/',$settings['long_html']) ||
           preg_match('/{TEMPLATE/',$settings['email_html']) ||
           preg_match('/{TEMPLATE/',$settings['uploadfile_html'])
          ){
            $fieldtypeoption .= "<tr>\n<th>". $LANG['backend']['txt_extraclasses'] .":</th>\n";
            $fieldtypeoption .= '<td><textfield name="extraclasses" maxlength="250"'
                .' style="width: 98%;">'
                . $form['extraclasses'] ."</textfield><br />"
                . "<small>".$LANG['backend']['des_extraclasses']."</small></td>\n</tr>\n";
        }
        if(preg_match('/{TEMPLATE/',$settings['field_loop'])){
            $fieldtypeoption .= "<tr>\n<th>". $TEXT['FIELD'].' '.$TEXT['TEMPLATE'] .":</th>\n";
            $fieldtypeoption .= '<td><textarea name="fieldtemplate" cols="50" rows="5" maxlength="250"'
                .' style="width: 98%; height: 100px;">'
                . $form['template'] ."</textarea><br />"
                . "<small>".$LANG['backend']['des_field_template']."</small></td>\n</tr>\n";
        }
}

$tpl->set_var('VAL_TYPE_OPTIONS', $fieldtypeoption);
$tpl->parse('typeoptions', 'type_options', true);

$tpl->set_var(
    array(
        // variables from Website Baker framework
        'PAGE_ID'           => (int) $page_id,
        'SECTION_ID'        => (int) $section_id,
        'FIELD_ID'          => (int) $field_id,
        'WB_URL'            => WB_URL,
        'ADMIN_URL'         => ADMIN_URL,
        'TXT_SAVE'          => $TEXT['SAVE'],
        'TXT_CANCEL'        => $TEXT['CANCEL'],
        'TXT_TITLE'         => $TEXT['TITLE'],
        'TXT_PLEASE_SELECT' => $TEXT['PLEASE_SELECT'],
        'MODULE_URL'        => WB_URL.'/modules/'.$mod_dir,
        'FTAN'              => method_exists( $admin, 'getFTAN' ) ? $admin->getFTAN() : '',

        // module settings
        'MODULE_DIR'        => $mod_dir,
        'TXT_TYPE'          => $LANG['backend']['TXT_TYP'],
        'TXT_COPY_FIELD'    => $LANG['backend']['TXT_COPY_FIELD'],
        'TXT_ADD_FIELD'     => $LANG['backend']['TXT_ADD_FIELD'],
        'TXT_MODIFY_FIELD'  => sprintf($LANG['backend']['TXT_MODIFY_FIELD'], $field_id),
        'VAL_TITLE'         => $form['title']
    )
);

// Parse template objects output
$tpl->parse('main', 'main_block', false);
$tpl->pparse('output', 'page',false, false);

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

<?php
// Print admin footer
$admin->print_footer();


