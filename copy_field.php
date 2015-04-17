<?php
/*
   WebsiteBaker CMS module: mpForm
   ===============================
   This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
   
   @module              mpform
   @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman), Quinto
   @copyright           (c) 2009 - 2015, Website Baker Org. e.V.
   @url                 http://www.websitebaker.org/
   @license             GNU General Public License

   Improvements are copyright (c) 2009-2011 Frank Heyne

   For more information see info.php   

*/
/* This file copies a field of the form in the backend. */
require('../../config.php');
$sError = '';
// Include WB admin wrapper script
$admin_header = FALSE;
require(WB_PATH.'/modules/admin.php');

// Get id
$oldfield_id = $admin->checkIDKEY('oldfield_id', false, 'GET');
if (!$oldfield_id) {
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL);
        exit();
}

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');
// Get new order
$order = new order(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
$position = $order->get_new($section_id);

// Insert new row into database
$database->query("INSERT INTO ".TABLE_PREFIX."mod_mpform_fields (section_id, page_id, position, required) VALUES ('$section_id','$page_id','$position','0')");
if($database->is_error()) {
        $sError .= ' INSERT INTO"';
}
// Get the id
$field_id = $database->get_one("SELECT LAST_INSERT_ID()");


// get values from existing field
$query_content = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `field_id` = '$oldfield_id'");
$old = $query_content->fetchRow();

// copy settings from existing to new field
$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET type='". $old['type']. "', title='". $old['title']. " [DUPLICATE]', required='". $old['required']. "', value='". $old['value']. "',"
                                 ." extra='". $old['extra']. "', help='". $old['help']. "' WHERE field_id = '$field_id'");  
if($database->is_error()) {
        $sError .= ' error witch "UPDATE mod_mpform_fields"';
}
// Insert new column into results database
$suffix = $database->get_one("SELECT `tbl_suffix` FROM `".TABLE_PREFIX."mod_mpform_settings` WHERE `section_id` = '$section_id'");
$results = TABLE_PREFIX . "mod_mpform_results_" . $suffix;
$s = "ALTER TABLE `$results` add `field" . $field_id . "` TEXT NOT NULL";
$database->query($s);
if($database->is_error()) {
        $sError .= ' error with ALTER TABLE';
}
// Say that a new record has been added, then redirect to modify page
$field_id = $admin->getIDKEY($field_id);
if($database->is_error()) {
        $admin->print_header();
        $admin->print_error($database->get_error(). $sError, WB_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$field_id.'&field_id='.$field_id.'&success=copy');
        $admin->print_footer();
} else {
        header("Location: ". WB_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$field_id.'&success=copy');
}
