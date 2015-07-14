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
/* This file deletes a submission in a section in the backend. It does not delete it from the results table! */
require('../../config.php');

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

// Get id
if (WB_VERSION >= "2.8.2") {
        $submission_id = $admin->checkIDKEY('submission_id', false, 'GET');
        if (!$submission_id) {
                $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL);
                exit();
        }
} else {
        if(!isset($_GET['submission_id']) OR !is_numeric($_GET['submission_id'])) {
                header("Location: ".ADMIN_URL."/pages/index.php");
                exit(0);
        } else {
                $submission_id = $_GET['submission_id'];
        }
}

// Delete row
$database->query("DELETE FROM ".TABLE_PREFIX."mod_mpform_submissions WHERE submission_id = '$submission_id'");

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
        $admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
        $admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();


