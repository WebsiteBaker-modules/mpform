<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.1.24
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Quinto, Martin Hecht (mrbaseman)
 * @copyright           (c) 2009 - 2016, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        
 *
 **/
/* This file saves the settings made in the main form of the module in the backend. */

// include global configuration file
require('../../config.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include the wrapper for escaping sql queries in old php / WB versions
require_once(WB_PATH.'/modules/'.$mod_dir.'/dbfunctions.php');

// unset page/section IDs defined via GET before including the admin file (we expect POST here)
unset($_GET['page_id']);
unset($_GET['section_id']);

// include WB admin wrapper script to check permissions
$update_when_modified = true;
$admin_header = false;
require(WB_PATH . '/modules/admin.php');
if ((WB_VERSION >= "2.8.2") && (!$admin->checkFTAN())) {
        $admin->print_header();
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'],
        ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
        $admin->print_footer();
        exit();
} else {
        $admin->print_header();
}

// protect from cross page writing
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."sections WHERE section_id = '$section_id'");
$res = $query_content->fetchRow();
if ($res['page_id'] != $page_id) {  
        header("Location: ".ADMIN_URL."/pages/index.php");
        exit(0);
}

// obtain module directory
$curr_dir = dirname(__FILE__);

// convert page/section id to numbers (already checked by /modules/admin.php but kept for consistency)
$page_id = (isset($_POST['page_id'])) ? (int) $_POST['page_id'] : '';
$section_id = (isset($_POST['section_id'])) ? (int) $_POST['section_id'] : '';

$update_keys = array(
    'header',
    'field_loop',
    'footer',
    'field_loop',
    'email_to',
    'email_from',
    'email_replyto',
    'email_fromname',
    'email_subject',
    'success_page',
    'success_text',
    'submissions_text',
    'email_text',
    'success_email_to',
    'success_email_from',
    'success_email_fromname',
    'success_email_text',
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
    'email_fromname_field'
);
foreach($upd_extra as $key) {
        ${$key} = (isset($_POST[$key])) ? $admin->get_post_escaped($key) : '';
}

// Sanitize data, cleaning if necessary:
$tbl_suffix = preg_replace("/\W/", "", $tbl_suffix);  // only allow alphanumerical chars as table suffix

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
        if (count($teil) == 1) {               // no name part found
                $ok = true;                    // $admin->validate_email(trim($teil[0]));
        } elseif (count($teil) == 2) {         // with name part
                $s = explode(">", $teil[1]);   // string with (list of) email address(es)
                $se = explode(",", $s[0]);     // array  with (list of) email address(es)
                foreach ($se as $sh) {
                        $ok = true;            // $admin->validate_email(trim($sh)); 
            if (!$ok)                          // check each address
                break;                         // break as soon as an invalid address is found
                }
        }
    if ($ok)
        $temp_email_to .= "$recip\n";          // only take valid lines
}
$email_to = $temp_email_to;

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

if ($email_from_field != '')
    $email_from = $email_from_field; // use a field of the form as sender's address
if ($email_replyto_field != '')
    $email_replyto = $email_replyto_field; // use a field of the form as sender's address
if ($email_replyto == '')
    $email_replyto = $email_from;
if ($email_fromname_field != '')
    $email_fromname = $email_fromname_field; //  use a field of the form as sender's name

// now loop over update values and create the SQL query string (this way we do not forget values)
// - no need to protect this anymore because post-values were already protected above 
$sql_key_values = '';
foreach($update_keys as $key) {
        $sql_key_values .= (($sql_key_values) ? ', ' : '' ) . "`$key` = '" . ${$key} . "'";
}

// write page settings to the module table
$table = TABLE_PREFIX . 'mod_mpform_settings';
$sql = "UPDATE `$table` SET $sql_key_values
        WHERE `section_id` = '$section_id'";

$database->query($sql);

// check if there is a db error, otherwise say successful
if ($database->is_error()) {
        $admin->print_error($database->get_error(), ADMIN_URL . '/pages/modify.php?page_id=' . $page_id);
} else {
        $admin->print_success($TEXT['SUCCESS'], ADMIN_URL . '/pages/modify.php?page_id=' . $page_id);
}

// print admin footer
$admin->print_footer();
