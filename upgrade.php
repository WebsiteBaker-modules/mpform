<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.1.20
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Quinto, Martin Hecht (mrbaseman)
 * @copyright           (c) 2009 - 2016, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        
 *
 **/
/* upgrade.php provides the functions for an upgrade from an older version of the module. */
// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

require_once(dirname(__FILE__).'/constants.php');

require(WB_PATH.'/modules/mpform/info.php');

echo "<BR><B>Updating database for module: $module_name</B><BR>";

// adding fields new in version 0.4.0:
//get settings table to see what needs to be created
$settingstable=$database->query("SELECT * FROM `".TABLE_PREFIX."mod_mpform_settings`");
$settings = $settingstable->fetchRow();


// If not already there, add new fields to the existing settings table
echo'<span class="good"><b>Adding new fields to the settings table</b></span><br />';

if (!isset($settings['success_text'])){
        $qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` ADD `success_text` TEXT NOT NULL AFTER `success_page`";
        $database->query($qs);
        if($database->is_error()) {
                echo $database->get_error().'<br />';
        } else {
                echo "Added new field `success_text` successfully<br />";
        }
}

if (!isset($settings['submissions_text'])){
        $qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` ADD `submissions_text` TEXT NOT NULL AFTER `success_text`";
        $database->query($qs);
        if($database->is_error()) {
                echo $database->get_error().'<br />';
        } else {
                echo "Added new field `submissions_text` successfully<br />";
        }
}

if (!isset($settings['email_text'])){
        $qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` ADD `email_text` TEXT NOT NULL AFTER `email_subject`";
        $database->query($qs);
        if($database->is_error()) {
                echo $database->get_error().'<br />';
        } else {
                echo "Added new field `email_text` successfully<br />";
        }
}

if (!isset($settings['enum_start'])){
        $qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` ADD `enum_start` VARCHAR( 1 ) NOT NULL";
        $database->query($qs);
        if($database->is_error()) {
                echo $database->get_error().'<br />';
        } else {
                echo "Added new field `enum_start` successfully<br />";
        }
}

// new in 1.1.15
if (!isset($settings['value_option_separator'])){
        $qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` ADD `value_option_separator` VARCHAR(10) NOT NULL DEFAULT '' AFTER `is_following`";
        $database->query($qs);
        if($database->is_error()) {
                echo $database->get_error().'<br />';
        } else {
                echo "Added new field `value_option_separator` successfully<br />";
        }
} else { // check if there is still a "text" type field left over from the pre-release
        $qs = "SHOW COLUMNS FROM `".TABLE_PREFIX."mod_mpform_settings`"
            . " WHERE `Field` = 'value_option_separator'";
        $cols=$database->query($qs);
        if($database->is_error()) {
                echo $database->get_error().'<br />';
        }
}

// new in 1.1.20

if (!isset($settings['email_replyto'])){
        $qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` ADD `email_replyto` VARCHAR(255) NOT NULL DEFAULT '' AFTER `email_from`";
        $database->query($qs);
        if($database->is_error()) {
                echo $database->get_error().'<br />';
        } else {
                echo "Added new field `email_replyto` successfully<br />";
        }
}

        
// removing fields never ever used:
if (isset($settings['radio_html'])){
        $qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` DROP `radio_html`, DROP `date_html`,
                DROP `check_html_header`, DROP `check_html_loop`, DROP `check_html_footer`, 
                DROP `select_html_header`, DROP `select_html_loop`, DROP `select_html_footer`";
        $database->query($qs);
        if($database->is_error()) {
                echo $database->get_error().'<br />';
        } else {
                echo "Removed unnecessary fields successfully<br />";
        }
}

// Remove bug in Search Query body (below version 0.1.3)
$query_body_code = " [TP]pages.page_id = [TP]mod_mpform_settings.page_id AND [TP]mod_mpform_settings.header LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'
OR [TP]pages.page_id = [TP]mod_mpform_settings.page_id AND [TP]mod_mpform_settings.footer LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'
OR [TP]pages.page_id = [TP]mod_mpform_fields.page_id AND [TP]mod_mpform_fields.title LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'";

$qs = "UPDATE ".TABLE_PREFIX."search SET value = '$query_body_code' WHERE name = 'query_body' and extra = 'mpform' LIMIT 1";
$database->query($qs);
if($database->is_error()) {
        echo $database->get_error().'<br />';
} else {
        echo "Search function updated successfully<br />";
}



// adding new (dummy) field 'position' in version 1.1.15:
//get settings table to see what needs to be created
$submissionstable=$database->query("SELECT * FROM `".TABLE_PREFIX."mod_mpform_submissions`");
$submissions = $submissionstable->fetchRow();

// If not already there, add new field(s) to the existing settings table
echo'<span class="good"><b>Adding new field(s) to the submissions table</b></span><br />';

if (!isset($submissions['position'])){
        $qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_submissions` ADD `position` INT NOT NULL DEFAULT '0' AFTER `page_id`";
        $database->query($qs);
        if($database->is_error()) {
                echo $database->get_error().'<br />';
        } else {
                echo "Added new field `position` successfully<br />";
        }
}

//Copy css files
$mpath = WB_PATH.'/modules/mpform/';

// If not already there, copy the css files
echo'<span class="good"><b>Adding putting css files in place</b></span><br />';

if (!file_exists($mpath.'frontend.css')) { 
        rename($mpath.'frontend.default.css', $mpath.'frontend.css') ; 
        echo "frontend.css<br />";
}

if (!file_exists($mpath.'backend.css')) { 
        rename($mpath.'backend.default.css', $mpath.'backend.css') ; 
        echo "backend.css<br />";
}

if (!file_exists($mpath.'private.php')) { 
        rename($mpath.'private.default.php', $mpath.'private.php') ; 
        echo "private.php<br />";
}

echo "<BR><B>Module $module_name updated to version: $module_version</B><BR>";
sleep (5);

