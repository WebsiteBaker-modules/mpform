<?php
/*
   WebsiteBaker CMS module: mpForm
   ===============================
   This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
   
   @module              mpform
   @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Quinto, Martin Hecht (mrbaseman)
   @copyright           (c) 2009 - 2015, Website Baker Org. e.V.
   @url                 http://www.websitebaker.org/
   @license             GNU General Public License

   Improvements are copyright (c) 2009-2011 Frank Heyne

   For more information see info.php   

*/
/* This file saves the advanced settings made in the main form of the module in the backend. */

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
require(WB_PATH.'/modules/admin.php');
if ((WB_VERSION >= "2.8.2") && (!$admin->checkFTAN()))
{
        $admin->print_header();
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
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

$update_keys=array('heading_html', 'short_html', 'long_html', 'email_html', 'uploadfile_html', 'enum_start',
                                   'use_captcha', 'date_format', 'max_submissions', 'stored_submissions', 'upload_files_folder',
                                   'upload_dir_mask', 'upload_file_mask', 'attach_file', 'max_file_size_kb', 'upload_only_exts');
foreach($update_keys as $key) {
        ${$key} = (isset($_POST[$key])) ? $admin->get_post_escaped($key) : '';
}

// Sanitize data, cleaning if necessary:
if(!is_numeric($max_file_size_kb))        $max_file_size_kb = 1024;

$upload_only_exts = preg_replace("/[^0-9a-zA-Z,]/", "", $upload_only_exts);  // only allow valid chars 

if(!is_numeric($max_submissions)) $max_submissions = 50;
if(!is_numeric($stored_submissions)) $stored_submissions = 1000;
// Make sure max submissions is not greater than stored submissions if stored_submissions <> 0
if($max_submissions > $stored_submissions) $max_submissions = $stored_submissions;

//Add folder for the files
require_once(WB_PATH.'/framework/functions.php');
if (!$upload_files_folder) $upload_files_folder = MEDIA_DIRECTORY . '/';
if ((substr($upload_files_folder,-1,1) === "\\") or (substr($upload_files_folder,-1,1) === "/")) {
        $upload_files_folder = substr($upload_files_folder,0,-1);
}        
$upload_files_folder = preg_replace("/[^\\0-9a-zA-Z_\-\.\/]/", "", $upload_files_folder);  // only allow valid chars 

$upload_dir_mask = preg_replace("/[^0-7]/", "", $upload_dir_mask);  // only allow valid chars
if(!is_numeric($upload_dir_mask) || $upload_dir_mask==0) $upload_dir_mask = '0705';

$upload_file_mask = preg_replace("/[^0-7]/", "", $upload_file_mask);  // only allow valid chars
if(!is_numeric($upload_file_mask) || $upload_file_mask==0) $upload_file_mask = '0204';

// set permissions for upload directory //stop touching WB directory settings
//change_mode(WB_PATH.MEDIA_DIRECTORY);  // reset to full permission
//@chmod(WB_PATH.MEDIA_DIRECTORY, intval('0755', 8));

if ($upload_files_folder != MEDIA_DIRECTORY ) {
        if (!file_exists(WB_PATH.$upload_files_folder) && !is_dir(WB_PATH.$upload_files_folder)) {
                make_dir(WB_PATH.$upload_files_folder);
                copy($curr_dir.'/index.php', WB_PATH.$upload_files_folder.'/index.php'); // no directory listings allowed
        }
//cant get the point in that
/*        if (is_dir(WB_PATH.$upload_files_folder)) {
                change_mode(WB_PATH.$upload_files_folder); // reset to full permission
                @chmod(WB_PATH.$upload_files_folder, intval($upload_dir_mask, 8));
        }*/
}



// now loop over update values and create the SQL query string (this way we do not forget values)
// - no need to protect this anymore, because it was already protected above
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


