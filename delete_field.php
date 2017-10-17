<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.11
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
/* This file deletes a field in a section in the backend. */
require('../../config.php');

require_once(dirname(__FILE__).'/constants.php');

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
$admin_header = FALSE;
require(WB_PATH.'/modules/admin.php');

// Get id
if ( method_exists( $admin, 'checkIDKEY' ) ) {
    $field_id = $admin->checkIDKEY('field_id', false, 'GET');
    if ((!$field_id)
       && (!(defined('MPFORM_SKIP_IDKEY')&&(MPFORM_SKIP_IDKEY)))) {
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
            .' (IDKEY) '.__FILE__.':'.__LINE__,
            ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
        exit();
    }
} else {
    if((!isset($_GET['field_id']) OR !is_numeric($_GET['field_id']))
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

// Delete row
$database->query(
    "DELETE FROM ".TP_MPFORM."fields"
        . " WHERE field_id = '$field_id'"
        . " and page_id = '$page_id'"
);

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
    $admin->print_header();
    $admin->print_error($database->get_error(),
         ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
    $admin->print_footer();
} else {
    $sUrlToGo = ADMIN_URL
        .'/pages/modify.php'
        .'?page_id='.$page_id
        .'&section_id='.$section_id
        .'&success=deleted';
    if(headers_sent())
      $admin->print_success($TEXT['SUCCESS'],$sUrlToGo);
    else
      header("Location: ". $sUrlToGo);
}
