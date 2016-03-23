<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.1.22
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Quinto, Martin Hecht (mrbaseman)
 * @copyright           (c) 2009 - 2016, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        
 *
 **/
/* This file adds a field to the form in the backend.*/

require('../../config.php');

// Include WB admin wrapper script
$admin_header = FALSE;
require(WB_PATH.'/modules/admin.php');

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');
// Get new order
$order = new order(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
$position = $order->get_new($section_id);

// Insert new row into database
$database->query(
        "INSERT INTO `".TABLE_PREFIX."mod_mpform_fields`"
        . " (section_id, page_id, position, required) "
        . " VALUES ('".$section_id."', '".$page_id."', '".$position."', '0')"
    );

// Get the id
$field_id = $database->get_one("SELECT LAST_INSERT_ID()");

// Check whether results table exists, create it if not
$ts = $database->query("SELECT "
        . "`tbl_suffix` FROM `".TABLE_PREFIX."mod_mpform_settings` "
        . "WHERE `section_id` = '".$section_id."'"
    );
$setting = $ts->fetchRow();
$suffix = $setting['tbl_suffix'];
$results = TABLE_PREFIX . "mod_mpform_results_" . $suffix;
$oTestQuery = $database->query("SHOW TABLES LIKE '".$results."'");
if ($oTestQuery->numRows() < 1 ) {
    $sSQL = "CREATE TABLE `$results` ( `session_id` VARCHAR(20) NOT NULL,"
            . " `started_when` INT NOT NULL DEFAULT '0' ,"   // time when first form was sent to browser
            . " `submitted_when` INT NOT NULL DEFAULT '0' ," // time when last form was sent back to server
            . " `referer` VARCHAR( 255 ) NOT NULL, "         // referer page
            . " PRIMARY KEY ( `session_id` ) "
            . " )";
    $database->query($sSQL);
}

// Insert new column into database
$sSQL = "ALTER TABLE `$results` add `field" . $field_id . "` TEXT NOT NULL";
$database->query($sSQL);

$iFID = $field_id; 
if(method_exists($admin, 'getIDKEY')){
    $iFID = $admin->getIDKEY($iFID);
}
// Say that a new record has been added, then redirect to modify page
$sUrlToGo =  WB_URL.'/modules/mpform/modify_field.php'
        . '?page_id='.(int)$page_id.'&section_id='
        . (int)$section_id.'&field_id='.$iFID.'&success=add';
if(!$database->is_error()) {        
        #$admin->print_success($TEXT['SUCCESS'],);
        header("Location: ". $sUrlToGo);
        } else {        
        $admin->print_header();
        $admin->print_error($database->get_error(), $sUrlToGo);        
        $admin->print_footer();
}

