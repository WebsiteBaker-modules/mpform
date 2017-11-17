<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.14
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2017, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @url                 https://forum.wbce.org/viewtopic.php?id=661
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* This file saves the settings FOR A FIELD OF THE FORM in the backend. */
require('../../config.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;


// Get id
if((!isset($_POST['field_id']) OR !is_numeric($_POST['field_id']))
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
    $field_id = (int) $_POST['field_id'];
}

require_once(WB_PATH.'/modules/mpform/constants.php');

// Include WB admin wrapper script
$update_when_modified = TRUE; // Tells script to update when this page was last updated
$admin_header = FALSE;
require(WB_PATH.'/modules/admin.php');
if (( method_exists( $admin, 'checkFTAN' ) && (!$admin->checkFTAN()))
    && (!(defined('MPFORM_SKIP_FTAN')&&(MPFORM_SKIP_FTAN)))){
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
         .' (FTAN) '.__FILE__.':'.__LINE__,
         ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
    $admin->print_footer();
    exit();
}

if (( method_exists( $admin, 'checkIDKEY' ))
    && (!(defined('MPFORM_SKIP_IDKEY')&&(MPFORM_SKIP_IDKEY)))) {
    $fid = $admin->getIDKEY($field_id);
} else {
    $fid = $field_id;
}

function int_not0($s) {
    $i = intval($s);
    return (($i==0)?'':$i);
}

// no need for the user to put a title in the end of a fieldset or html code:
if( $admin->get_post('title') == ''
    AND $admin->get_post('type') == 'fieldset_end')
        $_POST['title'] = "end of fieldset";

if( $admin->get_post('title') == ''
    AND $admin->get_post('type') == 'html')
        $_POST['title'] = "HTML code";

// Validate all fields
if($admin->get_post('title') == '' OR $admin->get_post('type') == '') {
    $admin->print_header();
    $admin->print_error(
        $MESSAGE['GENERIC']['FILL_IN_ALL'],
        WB_URL.'/modules/mpform/modify_field.php'
            .'?page_id='.$page_id
            .'&section_id='.$section_id
            .'&field_id='.$fid
    );
    $admin->print_footer();
} else {
    $title  = str_replace(array("[[", "]]"), '', $admin->get_post_escaped('title'));
    $type = str_replace(array("[[", "]]"), '', $admin->get_post_escaped('type'));
    $fieldtemplate = '';
    if(isset($_POST['fieldtemplate']))
        $fieldtemplate = $admin->get_post_escaped('fieldtemplate');
    if($fieldtemplate==null)$fieldtemplate='';
    $fieldtemplate = str_replace(array("[[", "]]"), '', $fieldtemplate);
    $extraclasses = $admin->get_post_escaped('extraclasses');
    if($extraclasses == null)$extraclasses='';
    $extraclasses = str_replace(array("[[", "]]"), '', $extraclasses);
    if (isset($_POST['required'])) {
        $required = $admin->get_post_escaped('required');
    } else {
        $required = '0';
    }
    if (isset($_POST['disabled'])) {
        $required = intval($required) | 4;
    }
    $help = str_replace(array("[[", "]]"), '',
        htmlspecialchars($admin->get_post_escaped('help'), ENT_QUOTES));
}

// is this a new field or an attack?
$broken = TRUE;
$query_settings
   = $database->query(
       "SELECT *"
           . " FROM ".TP_MPFORM."fields"
           . " WHERE field_id = '$field_id'"
    );
if($query_settings->numRows() > 0) {
    $fetch_settings = $query_settings->fetchRow();
    $isnewfield = $fetch_settings['title'] == "";
    $broken = $fetch_settings['page_id'] != $page_id;
}
if (($broken)
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

// Update row
$database->query(
    "UPDATE ".TP_MPFORM."fields"
        . " SET `title` = '$title',"
        . " `type` = '$type',"
        . " `required` = '$required',"
        . " `template` = '$fieldtemplate',"
        . " `extraclasses` = '$extraclasses',"
        . " `help` = '$help'"
        . " WHERE field_id = '$field_id'");
if($database->is_error()) {
    $admin->print_header();
    $admin->print_error($database->get_error());
    $admin->print_footer();
}

// Check whether results table exists, create it if not
$ts = $database->query(
    "SELECT `tbl_suffix`,`header`"
    . " FROM `".TP_MPFORM."settings`"
    . " WHERE `section_id` = '".$section_id."'"
    );
$setting = $ts->fetchRow();
$suffix = $setting['tbl_suffix'];
$header = $setting['header'];
$bTableLayout = (stripos($header, "<table") !== false);

if ($suffix != "DISABLED"){
    $results = TP_MPFORM."results_" . $suffix;
    $oTestQuery = $database->query("SHOW TABLES LIKE '".$results."'");
    if ($oTestQuery->numRows() < 1 ) {
        $sSQL = "CREATE TABLE `$results` ( `session_id` VARCHAR(20) NOT NULL,"
            . " `started_when` INT NOT NULL DEFAULT '0' ,"   // time when first form was sent to browser
            . " `submitted_when` INT NOT NULL DEFAULT '0' ," // time when last form was sent back to server
            . " `referer` VARCHAR( 255 ) NOT NULL, "         // referer page
            . " `submission_id` INT NOT NULL DEFAULT '0', "  // comes from submissions table
            . " PRIMARY KEY ( `session_id` ) "
            . " )";
        $database->query($sSQL);
    }


    // Check whether results table contains field_id
    $res = $database->query("SHOW COLUMNS"
        . " FROM `$results` "
        . " LIKE 'field".$field_id."'"
        );
    if ($res->numRows() < 1 ) {

        // Insert new column into database
        $sSQL = "ALTER TABLE `$results` add `field" . $field_id . "` TEXT NOT NULL";
        $database->query($sSQL);
    }
}

// If field type has multiple options, get all values and implode them
$value = '';
$list_count = $admin->add_slashes($admin->get_post('list_count'));
if(is_numeric($list_count)) {
    $values = array();
    for($i = 1; $i <= $list_count; $i++) {
        if (isset($_POST['isdefault'])
            and (is_numeric($_POST['isdefault']))) {
            $default = $_POST['isdefault'];
        } elseif ( isset($_POST['isdefault'.$i])
                   and (is_numeric($_POST['isdefault'.$i]))) {
            $default = $_POST['isdefault'.$i];
        } else {
            $default = 0;
        }
        if($admin->get_post('value'.$i) != '') {
            ($default == $i) ? $defcode = MPFORM_IS_DEFAULT : $defcode = '';
            $values[] = preg_replace("/&amp;(#?[a-zA-Z0-9]+);/","&\\1;",
                str_replace(array(",", "[[", "]]"),
                    array("&#44;", '', ''),
                    htmlspecialchars($admin->add_slashes(
                        $admin->get_post('value'.$i)),
                        ENT_QUOTES)
                    )
                ) . $defcode;
        }
    }
    $value = implode(',', $values);
}

// Get extra fields for field-type-specific settings
// Validate all fields and translate special chars
if ($admin->get_post('type') == 'textfield'
  or $admin->get_post('type') == 'hiddenfield'
  or $admin->get_post('type') == 'email_subj'
  or $admin->get_post('type') == 'integer_number'
  or $admin->get_post('type') == 'decimal_number') {
    $length = int_not0($admin->get_post_escaped('length'));
    $value = str_replace(array("[[", "]]"), '',
        htmlspecialchars($admin->get_post_escaped('value'), ENT_QUOTES));
    $database->query(
        "UPDATE ".TP_MPFORM."fields"
            . " SET value = '$value',"
            . " extra = '$length'"
            . " WHERE field_id = '$field_id'"
        );
} elseif ($admin->get_post('type') == 'conditional') {
    $iFID = int_not0($admin->get_post_escaped('value'));
    $value_option_separator=MPFORM_DEFAULT_OPT_SEPARATOR; // fallback
    $field_loop='{FIELD}';

    // Get settings (we need the value_option_separator)
    $query_settings
        = $database->query(
            "SELECT *"
                . " FROM ".TP_MPFORM."settings"
                . " WHERE section_id = '$section_id'"
        );
    if($query_settings->numRows() > 0) {
        $aSettings              = $query_settings->fetchRow();
        $field_loop             = $aSettings['field_loop'];
        $value_option_separator = $aSettings['value_option_separator'];
        if($value_option_separator=="") $value_option_separator=MPFORM_DEFAULT_OPT_SEPARATOR; // fallback
    }

    $query_field
        = $database->query(
            "SELECT *"
                . " FROM ".TP_MPFORM."fields"
                . " WHERE field_id = '$iFID'"
                . " ORDER BY position ASC"
        );
    $value = '';

    if($query_field->numRows() > 0) {
        $field = $query_field->fetchRow();
        $conditional_divs
            = "\n<!--/* "
            . sprintf($LANG['backend']['des_conditional_div'], $field['title'])
            . " */-->\n";
        $conditional_js_handler
            = "\n".'<script type="text/javascript">'."\n"
            . 'function EventHandler_fid'
            . $iFID
            . "(){\n";
        $conditional_js_call="}\n";
        $value = $field['value'];
        $bad = array("%", "+");

        if($field['type'] == 'select'){
            $conditional_js_call
                .= 'document.getElementById("'
                . 'field'.$iFID
                . '").addEventListener('
                . '"change",'
                . 'EventHandler_fid'
                . $iFID
                . ");\n";
            $options = explode(',', $value);
            foreach ($options as $idx => $option){
                $def = strpos($option, MPFORM_IS_DEFAULT);
                ($def > 0) ? $h = substr($option, 0, $def) : $h = $option;
                $vals=explode($value_option_separator,$h);
                if(count($vals)==1) $vals[1]=$vals[0];
                if (!(substr($option, 0, 2) == '[=') && ($option != ']')){
                    $label_i = urlencode($option) . $iFID;
                    $label_id = 'wb_'.str_replace($bad, "", $label_i);
                    $conditional_divs
                        .= '<div id="div_'
                        . $label_id
                        . '" style="display:none;">'
                        . $LANG['backend']['txt_you_have_selected'].' '
                        . $vals[1]
                        . "</div>\n";
                    $conditional_js_handler
                        .= '(document.getElementById('
                        . '"field'.$iFID
                        . '").value == "'
                        . $vals[0]
                        . '")'
                        . "\n  "
                        . '? document.getElementById("div_'
                        . $label_id
                        . '").style.display = "block"'
                        . "\n  "
                        . ': document.getElementById("div_'
                        . $label_id
                        . '").style.display = "none";'
                        . "\n";
                }
            }

        } elseif (($field['type'] == 'checkbox')
               or ($field['type'] == 'radio')){
            $options = explode(',', $value);
            foreach ($options as $idx => $option){
                $def = strpos($option, MPFORM_IS_DEFAULT);
                ($def > 0) ? $h = substr($option, 0, $def) : $h = $option;
                $vals=explode($value_option_separator,$h);
                $v = $vals[0];
                if(count($vals)==1) $vals[1]=$vals[0];
                $label_i = urlencode($option) . $iFID;
                $label_id = 'wb_'.str_replace($bad, "", $label_i);
                    $conditional_divs
                        .= '<div id="div_'
                        . $label_id
                        . '" style="display:none;">'
                        . $LANG['backend']['txt_you_have_selected'].' '
                        . $vals[1]
                        . "</div>\n";
                    $conditional_js_handler
                        .= 'document.getElementById('
                        . '"'.$label_id
                        . '").checked '
                        . "\n  "
                        . '? document.getElementById("div_'
                        . $label_id
                        . '").style.display = "block"'
                        . "\n  "
                        . ': document.getElementById("div_'
                        . $label_id
                        . '").style.display = "none";'
                        . "\n";
                   $conditional_js_call
                       .= 'document.getElementById("'
                       . $label_id
                       . '").addEventListener('
                       . '"change",'
                       . 'EventHandler_fid'
                       . $iFID
                       . ");\n";
            }
        }

        $aReplacements = array (
            '{FIELD_ID}'  => "$field_id",
            '{TITLE}'     => '',
            '{REQUIRED}'  => '',
            '{FIELD}'     => $conditional_divs
                           . $conditional_js_handler
                           . $conditional_js_call
                           . 'EventHandler_fid'
                           . $iFID
                           . '();'
                           . "\n</script>\n",
            '{HELP}'      => '',
            '{HELPTXT}'   => '',
            '{CLASSES}'   => 'fid'.$iFID.' '.MPFORM_CLASS_PREFIX.'html',
            '{ERRORTEXT}' => ''
        );


        $value
            = str_replace(
                array_keys($aReplacements),
                array_values($aReplacements),
                $field_loop
            ).PHP_EOL;
        $newtype = 'html';
        $extra = " form ";
    } else {
        $newtype = 'conditional';
        $value = $iFID;
        $extra = "";
    }

    $database->query(
        "UPDATE ".TP_MPFORM."fields"
            . " SET value = '$value',"
            . " type = '$newtype',"
            . " extra = '$extra'"
            . " WHERE field_id = '$field_id'"
        );
} elseif ($admin->get_post('type') == 'filename') {
    $length = int_not0($admin->get_post_escaped('length'));
    $database->query(
        "UPDATE ".TP_MPFORM."fields"
            . " SET  extra = '$length'"
            . " WHERE field_id = '$field_id'"
         );

} elseif ($admin->get_post('type') == 'textarea') {
    $value = str_replace(array("[[", "]]"), '',
        htmlspecialchars($admin->get_post_escaped('value'), ENT_QUOTES));
    $width = int_not0($admin->get_post_escaped('width'));
    $rows  = int_not0($admin->get_post_escaped('rows'));
    $database->query(
        "UPDATE ".TP_MPFORM."fields"
            . " SET value = '$value',"
            . " extra = '$width,$rows'"
            . " WHERE field_id = '$field_id'"
        );
} elseif ($admin->get_post('type') == 'html') {
    $value
       = str_replace(
          array("[[", "]]"),
           '',
           htmlspecialchars(
               $admin->get_post_escaped('value'),
               ENT_QUOTES
           )
    );
    $extra=" ";
    if (isset($_POST['use_in_form'])) $extra .= "form ";
    if (isset($_POST['use_in_site_html'])) $extra .= "site ";
    if (isset($_POST['use_in_user_html'])) $extra .= "user ";
    $database->query(
        "UPDATE ".TP_MPFORM."fields"
            . " SET value = '$value',"
            . " extra = '$extra'"
            . " WHERE field_id = '$field_id'");
} elseif ($admin->get_post('type') == 'heading') {
    $extra = str_replace(array("[[", "]]"), '', $admin->get_post_escaped('template'));
    if(trim($extra) == ''){
        $extra = '{TITLE}{FIELD}';
        if($bTableLayout)
           $extra = '<tr><td class="mpform_heading" colspan="3">'.$extra.'</td></tr>';
    }
    $value=" ";
    if (isset($_POST['use_in_form'])) $value .= "form ";
    if (isset($_POST['use_in_site_html'])) $value .= "site ";
    if (isset($_POST['use_in_user_html'])) $value .= "user ";
    $database->query(
        "UPDATE ".TP_MPFORM."fields"
            . " SET value = '$value',"
            . " extra = '$extra'"
            . " WHERE field_id = '$field_id'"
        );
} elseif ($admin->get_post('type') == 'select') {
    $extra
        = int_not0($admin->get_post_escaped('size'))
        . ','
        . $admin->get_post_escaped('multiselect');
    $database->query(
        "UPDATE ".TP_MPFORM."fields"
            . " SET value = '$value',"
            . " extra = '$extra'"
            . " WHERE field_id = '$field_id'"
        );
} elseif ($admin->get_post('type') == 'checkbox') {
    $extra
        = str_replace(
           array("[[", "]]"),
           '',
           $admin->get_post_escaped('seperator')
        );
    if ($extra=="" and $isnewfield) $extra = "<br />";   // set default value
    $database->query(
        "UPDATE ".TP_MPFORM."fields"
        . " SET value = '$value',"
        . " extra = '$extra'"
        . " WHERE field_id = '$field_id'"
    );
} elseif ($admin->get_post('type') == 'date') {
    $length = int_not0($admin->get_post_escaped('length'));
    $value
        = str_replace(array("[[", "]]"),
            '',
            htmlspecialchars(
                $admin->get_post_escaped('value'), ENT_QUOTES
            )
        );
    $database->query(
        "UPDATE ".TP_MPFORM."fields"
             . " SET value = '$value',"
             . " extra = '$length'"
             . " WHERE field_id = '$field_id'"
    );
} elseif ($admin->get_post('type') == 'radio') {
    $extra
        = str_replace(
            array("[[", "]]"),
            '',
            $admin->get_post_escaped('seperator')
        );
    if ($extra=="" and $isnewfield) $extra = "<br />";   // set default value
    $database->query(
        "UPDATE ".TP_MPFORM."fields"
            . " SET value = '$value',"
            . " extra = '$extra'"
            . " WHERE field_id = '$field_id'"
        );
}

// Check if there is a db error, otherwise say successful

$sModuleUrl =  WB_URL.'/modules/'.basename(dirname(__FILE__));
if ($database->is_error()) {
    $admin->print_header();
    $admin->print_error(
        $database->get_error(),
        $sModuleUrl.'/modify_field.php'
        .'?page_id='.$page_id
        .'&section_id='.$section_id
        .'&field_id='.$fid
    );
    $admin->print_footer();
} else {
    if (isset($_POST['copy'])) {
        $sUrlToGo =  $sModuleUrl
                .'/copy_field.php'
                .'?page_id='.$page_id
                .'&section_id='.$section_id
                .'&oldfield_id='.$fid
                .'&success=copy';
        if(headers_sent())
          $admin->print_success($TEXT['SUCCESS'],$sUrlToGo);
        else
          header("Location: ". $sUrlToGo);
    } elseif (isset($_POST['add'])) {
        $sUrlToGo = $sModuleUrl
            .'/add_field.php'
            .'?page_id='.$page_id
            .'&section_id='.$section_id
            .'&success=save';
        if(headers_sent())
          $admin->print_success($TEXT['SUCCESS'],$sUrlToGo);
        else
          header("Location: ". $sUrlToGo);
    } else {
        $sUrlToGo =   $sModuleUrl
            .'/modify_field.php'
            .'?page_id='.$page_id
            .'&section_id='.$section_id
            .'&field_id='.$fid
            .'&success=save';
        if(headers_sent())
          $admin->print_success($TEXT['SUCCESS'],$sUrlToGo);
        else
          header("Location: ". $sUrlToGo);
    }
}


