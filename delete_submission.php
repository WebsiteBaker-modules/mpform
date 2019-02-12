<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.28
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2019, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/

/* This file deletes a submission in a section in the backend.
  It does not delete it from the results table! */

require('../../config.php');

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

require_once(dirname(__FILE__).'/constants.php');


// Get id
if ( method_exists( $admin, 'checkIDKEY' )
    && (!(defined('MPFORM_SKIP_IDKEY')&&(MPFORM_SKIP_IDKEY))) ) {
    $submission_id = $admin->checkIDKEY('submission_id', false, 'GET');
    if (!$submission_id) {
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
            .' (IDKEY) '.__FILE__.':'.__LINE__,
            ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
        exit();
    }
} else {
    if ((!isset($_GET['submission_id']) || !is_numeric($_GET['submission_id']))
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
        $submission_id = $_GET['submission_id'];
    }
}

// find out section_id
$res  = $database->query("SELECT "
        . "`section_id` FROM `".TP_MPFORM."submissions`"
        . " WHERE `submission_id` = '$submission_id'"
        );
$rec = $res->fetchRow();
$section_id = $rec['section_id'];

// Delete row
$database->query(
    "DELETE FROM ".TP_MPFORM."submissions"
        . " WHERE submission_id = '$submission_id'"
);

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
    $admin->print_error($database->get_error(),
        ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {


    // find results table
    $ts = $database->query("SELECT "
        . "`tbl_suffix` FROM `".TP_MPFORM."settings` "
        . "WHERE `section_id` = '".$section_id."'"
        );

    $setting = $ts->fetchRow();
    $suffix = $setting['tbl_suffix'];
    if ($suffix != "DISABLED"){

        $results = TP_MPFORM."results_" . $suffix;

        // Check whether results table contains submission_id
        $res = $database->query("SHOW COLUMNS"
            . " FROM `$results` "
            . " LIKE 'submission_id'"
            );
        if ($res->numRows() > 0 ) {
            $database->query(
                "DELETE FROM `$results` "
                    . " WHERE submission_id = '$submission_id'"
            );
        }

        if($database->is_error()) {
            $admin->print_error($database->get_error(),
                ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
        } else {
            $admin->print_success($TEXT['SUCCESS'],
                ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
        }
    } else {
        $admin->print_success($TEXT['SUCCESS'],
            ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
    }
}
// Print admin footer
$admin->print_footer();
