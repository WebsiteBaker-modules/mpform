<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.9
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
/* This file saves the settings made in the main form of the module in the backend. */

// include global configuration file
require('../../config.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include the wrapper for escaping sql queries in old php / WB versions
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');

require_once(dirname(__FILE__).'/constants.php');


// unset page/section IDs defined via GET before including the admin file (we expect POST here)
unset($_GET['page_id']);
unset($_GET['section_id']);

// include WB admin wrapper script to check permissions
$update_when_modified = true;
$admin_header = false;
require(WB_PATH . '/modules/admin.php');
if (( method_exists( $admin, 'checkFTAN' )  && (!$admin->checkFTAN()))
    && (!(defined('MPFORM_SKIP_FTAN')&&(MPFORM_SKIP_FTAN)))) {
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
        .' (FTAN) '.__FILE__.':'.__LINE__,
        ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
    $admin->print_footer();
    exit();
} else {
    $admin->print_header();
}

// protect from cross site scripting
$query_content = $database->query(
    "SELECT *"
    . " FROM ".TABLE_PREFIX."sections"
    . " WHERE section_id = '$section_id'");

$res = $query_content->fetchRow();
if (($res['page_id'] != $page_id)
    && (!(defined('MPFORM_SKIP_ID_CHECK')&&(MPFORM_SKIP_ID_CHECK)))) {
    $sUrlToGo = ADMIN_URL."/pages/index.php";
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
        .' (ID_CHECK) '.__FILE__.':'.__LINE__,
        $sUrlToGo);
    exit(0);
}

// obtain module directory
$curr_dir = dirname(__FILE__);

// convert page/section id to numbers
// (already checked by /modules/admin.php but kept for consistency)
$page_id = (isset($_POST['page_id'])) ? (int) $_POST['page_id'] : '';
$section_id = (isset($_POST['section_id'])) ? (int) $_POST['section_id'] : '';

$update_keys = array(
    'header',
    'field_loop',
    'footer',
    'email_to',
    'email_from',
    'email_replyto',
    'email_fromname',
    'email_subject',
    'success_page',
    'success_text',
    'submissions_text',
    'email_text',
    'email_css',
    'success_email_to',
    'success_email_from',
    'success_email_fromname',
    'success_email_text',
    'success_email_css',
    'success_email_subject',
    'is_following',
    'tbl_suffix'
);
foreach($update_keys as $key) {
    ${$key} = (isset($_POST[$key])) ? $admin->get_post_escaped($key) : '';
}
$upd_extra = array(
    'email_from_field',
    'email_replyto_field',
    'email_fromname_field',
    'success_email_from_field',
    'success_email_fromname_field'
);
foreach($upd_extra as $key) {
    ${$key} = (isset($_POST[$key])) ? $admin->get_post_escaped($key) : '';
}

// Sanitize data, cleaning if necessary:
// (only allow alphanumerical chars as table suffix)
$tbl_suffix = preg_replace("/\W/", "", $tbl_suffix);

// check multiple email recipients
$temp_email_to = "";
$arrtorep = array(
    "\r\n",
    "\n\r",
    "\r"
);
$email_to = str_replace($arrtorep, "\n", $email_to);
$emails = preg_split('/[\n]/', $email_to);
foreach ($emails as $recip) {
    $teil = explode("<", $recip);
    $ok = false;
    if (count($teil) == 1) {           // no name part found
        $ok = true;                    // $admin->validate_email(trim($teil[0]));
    } elseif (count($teil) == 2) {     // with name part
        $s = explode(">", $teil[1]);   // string with (list of) email address(es)
        $se = explode(",", $s[0]);     // array  with (list of) email address(es)
        foreach ($se as $sh) {
            $ok = true;                // $admin->validate_email(trim($sh));
        if (!$ok)                      // check each address
            break;                     // break as soon as an invalid address is found
        }
    }
    if ($ok)
    $temp_email_to .= "$recip\n";      // only take valid lines
}
$email_to = trim($temp_email_to);

if (!$admin->validate_email($email_from)) {
    $email_from = '';
}
if (!$admin->validate_email($email_replyto)) {
    $email_replyto = '';
}
if (!$admin->validate_email($success_email_from)) {
    $success_email_from = '';
}

$email_fromname = htmlspecialchars($email_fromname, ENT_QUOTES);
$email_subject = htmlspecialchars($email_subject, ENT_QUOTES);
$success_email_fromname = htmlspecialchars($success_email_fromname, ENT_QUOTES);
$success_email_subject = htmlspecialchars($success_email_subject, ENT_QUOTES);
$success_email_text = htmlspecialchars($success_email_text, ENT_QUOTES);

// end of data cleaning

if(is_array($email_fromname_field)&&!empty($email_fromname_field)){
    // check which ones were there already
    $query_settings
       = $database->query(
           "SELECT `email_fromname`"
               . " FROM ".TP_MPFORM."settings"
               . " WHERE section_id = '$section_id'"
        );
    $curr_email_fromname = array();
    if($query_settings->numRows() > 0) {
        $fetch_settings = $query_settings->fetchRow();
        $curr_email_fromname = $fetch_settings['email_fromname'];
        if(substr($curr_email_fromname, 0, 5) == 'field') {
            $curr_email_fromname = explode (",", $curr_email_fromname);
        }
    }
    // now we have an array which contains the previous fromname fields
    $tmp_fromname_field = array();
    // pick out the ones we already had and which are still in the current choice
    foreach($curr_email_fromname as $tmp_frmn){
        if(in_array($tmp_frmn, $email_fromname_field)){
            $tmp_fromname_field[] = $tmp_frmn;
        }
    }
    // finally add the ones which are selected in addition to these
    foreach($email_fromname_field as $tmp_frmn){
        if((!in_array($tmp_frmn, $tmp_fromname_field))
            && (substr($tmp_frmn, 0, 5) == 'field')) {
                $tmp_fromname_field[] = $tmp_frmn;
        }
    }
    // now we have the previous ones at the beginning, new ones at the end. Let's implode:
    $email_fromname_field = implode(",", $tmp_fromname_field);
}

if ($email_from_field != '')
    $email_from = $email_from_field; // use a field of the form as sender's address
if ($email_replyto_field != '')
    $email_replyto = $email_replyto_field; // use a field of the form as sender's address
if ($email_replyto == '')
    $email_replyto = $email_from;
if ($email_fromname_field != '')
    $email_fromname = $email_fromname_field; //  use a field of the form as sender's name
if ($success_email_from_field != '')
    $success_email_from = $success_email_from_field; // use a field of the form as sender's address
if ($success_email_fromname_field != '')
    $success_email_fromname = $success_email_fromname_field; //  use a field of the form as sender's name

// now loop over update values and create the SQL query string (this way we do not forget values)
// - no need to protect this anymore because post-values were already protected above
$sql_key_values = '';
foreach($update_keys as $key) {
    $sql_key_values .= (($sql_key_values) ? ', ' : '' ) . "`$key` = '" . ${$key} . "'";
}

// write page settings to the module table
$table = TP_MPFORM.'settings';
$sql = "UPDATE `$table` SET $sql_key_values
    WHERE `section_id` = '$section_id'";

$database->query($sql);

if (!($database->is_error()) and ($tbl_suffix != "DISABLED")) {

     $sUrlToGo = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;

     $results = TP_MPFORM."results_" . $tbl_suffix;
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

         if($database->is_error()) {
             $admin->print_error("Could not add results table to database", $sUrlToGo);
             $admin->print_footer();
             exit(0);
         }
     }

     // loop through fields and update results table
     $query_fields
         = $database->query(
             "SELECT *"
             . " FROM `".TP_MPFORM."fields`"
             . " WHERE `section_id` = '$section_id'"
             . " ORDER BY `position` ASC");
     if($query_fields->numRows() > 0) {
         while($field = $query_fields->fetchRow()) {
             $field_id = $field['field_id'];
             // Check whether results table contains field_id
             $res = $database->query("SHOW COLUMNS"
                 . " FROM `$results` "
                 . " LIKE 'field".$field_id."'"
                 );
             if ($res->numRows() < 1 ) {
                 // Insert new column into database
                 $sSQL = "ALTER TABLE `$results`"
                       . " add `field".$field_id."` TEXT NOT NULL";
                 $database->query($sSQL);
                 if($database->is_error()) {
                     $admin->print_error(
                         "could not add field "
                         . $field['field_id']
                         . "to results table",
                         $sUrlToGo
                     );
                     $admin->print_footer();
                     exit(0);
                 }
             }
         }
    }
    // Check whether results table contains submission_id
    $res = $database->query("SHOW COLUMNS"
        . " FROM `$results` "
        . " LIKE 'submission_id'"
        );
    if ($res->numRows() < 1 ) {
        // Insert new column into database
        $sSQL = "ALTER TABLE `$results`"
              . " add `submission_id` INT NOT NULL DEFAULT '0' AFTER `referer`";
        $database->query($sSQL);

        if($database->is_error()) {
            $admin->print_header();
            $admin->print_error("could not add submission_id to results table", $sUrlToGo);
            $admin->print_footer();
            exit(0);
        }
    }

}

// check if there is a db error, otherwise say successful
if ($database->is_error()) {
    $admin->print_error($database->get_error(),
        ADMIN_URL . '/pages/modify.php?page_id=' . $page_id);
} else {
    $admin->print_success($TEXT['SUCCESS'],
        ADMIN_URL . '/pages/modify.php?page_id=' . $page_id);
}

// print admin footer
$admin->print_footer();
