<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.3.8
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
/* This file copies a field of the form in the backend. */

require('../../config.php');


// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include the wrapper for escaping sql queries in old php / WB versions
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');

require_once(dirname(__FILE__).'/constants.php');

$sError = '';
// Include WB admin wrapper script
$admin_header = FALSE;
require(WB_PATH.'/modules/admin.php');

// Get id
$oldfield_id 
      = ( method_exists( $admin, 'checkIDKEY' ) )
      ? $admin->checkIDKEY('oldfield_id', false, 'GET')
      : $_GET['oldfield_id'];
      
if ((!$oldfield_id)
    && (!(defined('MPFORM_SKIP_IDKEY')&&(MPFORM_SKIP_IDKEY)))) {
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
        .' (IDKEY) '.__FILE__.':'.__LINE__,
        ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
    exit();
}

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');
// Get new order
$order = new order(
    TP_MPFORM.'fields', 
    'position', 
    'field_id', 
    'section_id'
);

$position = $order->get_new($section_id);

// Insert new row into database

$database->query(
    "INSERT INTO `".TP_MPFORM."fields`"
    . " SET"
    . " `section_id` = '".$section_id."', "
    . " `page_id` = '".$page_id."', "
    . " `position` = '".$position."', "
    . " `required` = '0', "
    . " `value` = '', "
    . " `extra` = ''"
    );

if($database->is_error()) {
    $sError .= ' INSERT INTO"';
}
// Get the id
$field_id = $database->get_one("SELECT LAST_INSERT_ID()");


// get values from existing field
$query_content = $database->query(
    "SELECT * "
    . " FROM `".TP_MPFORM."fields`"
    . " WHERE `field_id` = '$oldfield_id'");
    
$old = $query_content->fetchRow();

// copy settings from existing to new field - need to protect this
$sql="UPDATE ".TP_MPFORM."fields"
    . " SET  type='" . mpform_escape_string($old['type']). "',"
    . "     title='" . mpform_escape_string($old['title']). " [DUPLICATE]',"
    . "  required='" . mpform_escape_string($old['required']). "',"
    . "     value='" . mpform_escape_string($old['value']). "',"
    . "     extra='" . mpform_escape_string($old['extra']). "',"
    . "      help='" . mpform_escape_string($old['help']). "'"
    . " WHERE field_id = '$field_id'";
$database->query($sql);  
if($database->is_error()) {
    $sError .= ' error witch "UPDATE mod_mpform_fields"';
}

// Insert new column into results database
$suffix = $database->get_one(
    "SELECT `tbl_suffix`"
    . " FROM `".TP_MPFORM."settings`"
    . " WHERE `section_id` = '$section_id'"
);

$sUrlToGo = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;

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

        if($database->is_error()) {    
            $admin->print_header();
            $admin->print_error("could not add results table", $sUrlToGo);    
            $admin->print_footer();
            exit(0);
        }
    }

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
            $admin->print_header();
            $admin->print_error("could not add field to results table", $sUrlToGo);    
            $admin->print_footer();
            exit(0);
        }
    }
}

// Say that a new record has been added, then redirect to modify page
if ((method_exists( $admin, 'getIDKEY' )) 
    && (!(defined('MPFORM_SKIP_IDKEY')&&(MPFORM_SKIP_IDKEY)))){
    $field_id = $admin->getIDKEY($field_id);
}
if($database->is_error()) {
    $admin->print_header();
    $admin->print_error(
        $database->get_error(). $sError, 
        WB_URL
        .'/modules/mpform/modify_field.php'
        .'?page_id='.$page_id
        .'&section_id='.$section_id
        .'&field_id='.$field_id
        .'&success=copy'
    );
    $admin->print_footer();
} else {
    $sUrlToGo = WB_URL
       .'/modules/mpform/modify_field.php'
       .'?page_id='.$page_id
       .'&section_id='.$section_id
       .'&field_id='.$field_id
       .'&success=copy';
    if(headers_sent())
      $admin->print_success($TEXT['SUCCESS'],$sUrlToGo);
    else 
      header("Location: ". $sUrlToGo);
}
