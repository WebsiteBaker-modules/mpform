<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.32
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2019, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* This file contains site related private functions for the frontend,
 * to be created by the admin if required.
 */

// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

// replace the sample content in the functions below with your own content, if required!

function private_function_before_new_form($section_id) {
/*
    // example how to use: put a predefined value into one of the fields
    if ($section_id == 4526) {
        $_SESSION['mpf']['field136'] = $_SESSION['EMAIL'];
        return true;
    }

    // another example how to use: again, pre-fill a field
    if ($section_id == 4575) {
        $_SESSION['mpf']['field4568'] = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $_SESSION['mpf']['field4568'] = $_SERVER['HTTP_REFERER'];
        }
        return true;
    }
*/
    return true;
}


function private_function_for_field(
                            $field_id,
                            $post_field
                        ) {
/*
    // example how to use: for a specific section verify that the value is in the proper range
    if ($field_id == 3269) {
        if((intval($post_field)<0) or (intval($post_field)>10))
            return "pleaseenter a value between 0 and 10";
    }
    // ... checks for other fields - this function is called separately for each field
*/
    return "";
}



function private_function_before_email(
                            $section_id,
                            &$html_data_user,
                            &$html_data_site
                        ) {
/*
    // example how to use: append a value extracted from the session to the list of submitted fields
    if ($section_id == 4524) {
        $html_data_user
            .= $_SESSION['study_id'] . "<br />\n";
        $html_data_site
            .= $_SESSION['study_id'] . "<br />\n";
        return true;
    }
*/
    return true;
}


function private_function_after_email(
                            $section_id,
                            &$html_data_site,
                            &$mpform_fields
                        ) {
/*
    // example how to use: anonymize ip addresses for storing the result in the database (this might be a legal requirement)
    if ($section_id == 4524) {
        global $mpform_fields;
        // SQL-statements for results table
        $mpform_fields = preg_replace('/\b([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\b/', '$1.$2.xxx.yyy', $mpform_fields);
        // html version for submissions table
        $html_data_site = preg_replace('/\b([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\b/', '$1.$2.xxx.yyy', $html_data_site);
        return true;
    }
*/
    return true;
}

function private_function_on_success($section_id) {
/*
    global $database;
    // example for looping through a form
    // set the following values according your forms
    // see online help for tutorial!
    $prev_section =   9000;    // section_id of the part of the form before the loop
    $prev_field =     8000;    // id of the field deciding whether to skip the loop or not
    $prev_cond =     "yes";    // value of the condition for entering the loop
    $loop_section =   9001;    // section_id of the looping part of the form
    $loop_field =     8000;    // id of the field deciding whether to leave the loop or not
    $loop_cond =     "yes";    // value of the condition for staying in the loop
    $loop_page =      7001;    // id of the page with the loop
    $next_page =      7002;    // id of the page following after the loop

    if ($section_id == $prev_section) {
        ($_SESSION['mpf']['field'.$prev_field] == $prev_cond)
            ? $np = $loop_page
            : $np = $next_page;
        $query_menu
            = $database->query(
                "SELECT link,target"
                . " FROM ".TABLE_PREFIX."pages"
                . " WHERE `page_id` = $np"
            );
        if($query_menu->numRows() > 0) {
            $fetch_settings = $query_menu->fetchRow();
            $link = WB_URL.PAGES_DIRECTORY.$fetch_settings['link'].PAGE_EXTENSION;
            echo "<script type='text/javascript'>location.href='".$link."';</script>";
        }
        return false;
    }
    if ($section_id == $loop_section) {
        if ($_SESSION['mpf']['field'.$loop_field] == $loop_cond) {
            $np = $loop_page;
            $_SESSION['submission_id_'.$section_id] .= "_";
            foreach ($_SESSION as $k => $v) {
                if (substr($k, 0, 5) == "field") unset ($_SESSION[$k]);
            }
        } else {
            $np = $next_page;
            // restore original submission_id:
            $_SESSION['submission_id_'.$section_id]
                = substr($_SESSION['submission_id_'.$section_id], 0, 8);
        }
        $query_menu
            = $database->query(
                "SELECT link,target"
                . " FROM ".TABLE_PREFIX."pages"
                . " WHERE `page_id` = $np"
            );
        if($query_menu->numRows() > 0) {
            $fetch_settings = $query_menu->fetchRow();
            $link = WB_URL.PAGES_DIRECTORY.$fetch_settings['link'].PAGE_EXTENSION;
            echo "<script type='text/javascript'>location.href='".$link."';</script>";
        }
        return false;
    }
*/
    return true;
}

