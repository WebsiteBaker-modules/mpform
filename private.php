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
/* This file contains site related private functions for the frontend, to be created by the admin if required. */

// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

// replace the sample content in the functions below with your own content, if required!

function private_function_before_new_form($section_id) {
        // example how to use:
        if ($section_id == 4526) {
                $_SESSION['field136'] = $_SESSION['EMAIL'];
                return true;
        }

        // another example how to use:
        if ($section_id == 4575) {
                $_SESSION['field4568'] = '';
                if (isset($_SERVER['HTTP_REFERER'])) {
                        $_SESSION['field4568'] = $_SERVER['HTTP_REFERER'];
                } 
                return true;
        }

        return true;
}

function private_function_before_email($section_id) {
        // example how to use:
        if ($section_id == 4524) {
                global $felder;
                $felder .= ", field133 = '". $_SESSION['study_id'] ."'";
                return true;
        }

        return true;
}

function private_function_on_success($section_id) {
        global $database;
        
        // example for looping through a form
        // set the following values according your forms
        // see online help for tutorial!
        $prev_section =       9000;        // section_id of the part of the form before the loop 
        $prev_field =         8000;        // id of the field deciding whether to skip the loop or not
        $prev_cond =         "yes";        // value of the condition for entering the loop
        $loop_section =       9001;        // section_id of the looping part of the form 
        $loop_field =         8000;        // id of the field deciding whether to leave the loop or not
        $loop_cond =         "yes";        // value of the condition for staying in the loop
        $loop_page =          7001;        // id of the page with the loop
        $next_page =          7002;        // id of the page following after the loop
        
        if ($section_id == $prev_section) {
                ($_SESSION['field'.$prev_field] == $prev_cond) ? $np = $loop_page : $np = $next_page;
                $query_menu = $database->query("SELECT link,target FROM ".TABLE_PREFIX."pages WHERE `page_id` = $np");
                if($query_menu->numRows() > 0) {
                        $fetch_settings = $query_menu->fetchRow();
                        $link = WB_URL.PAGES_DIRECTORY.$fetch_settings['link'].PAGE_EXTENSION;
                        echo "<script type='text/javascript'>location.href='".$link."';</script>";
                }
                return false;
        }
        if ($section_id == $loop_section) {
                if ($_SESSION['field'.$loop_field] == $loop_cond) {
                        $np = $loop_page;
                        $_SESSION['submission_id_'.$section_id] .= "_";
                        foreach ($_SESSION as $k => $v) {
                                if (substr($k, 0, 5) == "field") unset ($_SESSION[$k]);
                        }
                } else {
                        $np = $next_page;
                        $_SESSION['submission_id_'.$section_id] = substr($_SESSION['submission_id_'.$section_id], 0, 8);  // restore original submission_id
                }
                $query_menu = $database->query("SELECT link,target FROM ".TABLE_PREFIX."pages WHERE `page_id` = $np");
                if($query_menu->numRows() > 0) {
                        $fetch_settings = $query_menu->fetchRow();
                        $link = WB_URL.PAGES_DIRECTORY.$fetch_settings['link'].PAGE_EXTENSION;
                        echo "<script type='text/javascript'>location.href='".$link."';</script>";
                }
                return false;
        }

        return true;
}


