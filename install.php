<?php
/*
   WebsiteBaker CMS module: mpForm
   ===============================
   This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
   
   @module              mpform
   @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Quinto, Martin Hecht (mrbaseman)
   @copyright           (c) 2009 - 2015, Website Baker Org. e.V.
   @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
   @license             GNU General Public License

   Improvements are copyright (c) 2009-2011 Frank Heyne

   For more information see info.php   

*/
/* This file provides the installation functions of the module. */
if(defined('WB_URL')) {

require_once(dirname(__FILE__).'/constants.php');
        
        // Rename files
        if (!file_exists(WB_PATH."/modules/mpform/backend.css"))  rename(WB_PATH."/modules/mpform/backend.css.txt",  WB_PATH."/modules/mpform/backend.css");
        if (!file_exists(WB_PATH."/modules/mpform/frontend.css")) rename(WB_PATH."/modules/mpform/frontend.css.txt", WB_PATH."/modules/mpform/frontend.css");
        if (!file_exists(WB_PATH."/modules/mpform/private.php"))  rename(WB_PATH."/modules/mpform/private.php.txt",  WB_PATH."/modules/mpform/private.php");

        // Create tables
        $database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mpform_fields`");
        $mod_mpform = 'CREATE TABLE `'.TABLE_PREFIX.'mod_mpform_fields` ( `field_id` INT NOT NULL AUTO_INCREMENT,'
                . ' `section_id` INT NOT NULL DEFAULT \'0\' ,'
                . ' `page_id` INT NOT NULL DEFAULT \'0\' ,'
                . ' `position` INT NOT NULL DEFAULT \'0\' ,'
                . ' `title` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
                . ' `type` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
                . ' `required` INT NOT NULL DEFAULT \'0\' ,'
                . ' `value` TEXT NOT NULL ,'
                . ' `extra` TEXT NOT NULL ,'
                . ' `help` TEXT NULL ,'
                . ' PRIMARY KEY ( `field_id` ) '
                . ' )';
        $database->query($mod_mpform);
        $database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mpform_settings`");
        $mod_mpform = 'CREATE TABLE `'.TABLE_PREFIX.'mod_mpform_settings` ('
                . ' `section_id` INT NOT NULL DEFAULT \'0\' ,'
                . ' `page_id` INT NOT NULL DEFAULT \'0\' ,'
                . ' `header` TEXT NOT NULL ,'
                . ' `field_loop` TEXT NOT NULL ,'
                . ' `footer` TEXT NOT NULL ,'
                . ' `email_to` TEXT NOT NULL ,'
                . ' `email_from` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
                . ' `email_fromname` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
                . ' `email_subject` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
                . ' `email_text` TEXT NOT NULL ,'
                . ' `success_page` TEXT NOT NULL ,'
                . ' `success_text` TEXT NOT NULL ,'
                . ' `submissions_text` TEXT NOT NULL ,'
                . ' `success_email_to` TEXT NOT NULL ,'
                . ' `success_email_from` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
                . ' `success_email_fromname` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
                . ' `success_email_text` TEXT NOT NULL ,'
                . ' `success_email_subject` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
                . ' `stored_submissions` INT NOT NULL DEFAULT \'0\' ,'
                . ' `max_submissions` INT NOT NULL DEFAULT \'0\' ,'
                . ' `heading_html` TEXT NOT NULL ,'
                . ' `short_html` TEXT NOT NULL ,'
                . ' `long_html` TEXT NOT NULL ,'
                . ' `email_html` TEXT NOT NULL ,'
                . ' `uploadfile_html` TEXT NOT NULL ,'
                . ' `use_captcha` INT NOT NULL DEFAULT \'0\' ,'
                . ' `upload_files_folder` TEXT NOT NULL ,'
                . ' `date_format` TEXT NOT NULL ,'
                . ' `max_file_size_kb` INT NOT NULL DEFAULT \'0\' ,'
                . ' `attach_file` INT NOT NULL DEFAULT \'0\' ,'
                . ' `upload_file_mask` VARCHAR(4) NOT NULL DEFAULT \'\' ,'
                . ' `upload_dir_mask` VARCHAR(4) NOT NULL DEFAULT \'\' ,'
                . ' `upload_only_exts` VARCHAR(255) DEFAULT \'\' ,'
                . ' `is_following` BOOL NOT NULL DEFAULT \'0\' ,'        // 1 = is subsequent part of a multi page form
                . ' `value_option_separator` VARCHAR(10) NOT NULL DEFAULT \'\','
                . ' `tbl_suffix` VARCHAR(100) DEFAULT \'\' ,'            // optional suffix for the results table
                . ' `enum_start` VARCHAR(1) DEFAULT \'\' ,'              // optional enumeration for radio and checkbox
                . ' PRIMARY KEY ( `section_id` ) '
                . ' )';
        $database->query($mod_mpform);
        
        $database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mpform_submissions`");
        $mod_mpform = 'CREATE TABLE `'.TABLE_PREFIX.'mod_mpform_submissions` ( `submission_id` INT NOT NULL AUTO_INCREMENT,'
                . ' `section_id` INT NOT NULL DEFAULT \'0\' ,'
                . ' `page_id` INT NOT NULL DEFAULT \'0\' ,'
                . ' `position` INT NOT NULL DEFAULT \'0\' ,'
                . ' `started_when` INT NOT NULL DEFAULT \'0\' ,'         // time when form was sent to browser
                . ' `submitted_when` INT NOT NULL DEFAULT \'0\' ,'       // time when form was sent back to server
                . ' `submitted_by` INT NOT NULL DEFAULT \'0\','                
                . ' `session_id` TEXT NOT NULL ,'                        // same id for one set of forms
                . ' `ip_addr` TEXT NOT NULL ,'                           // IP address
                . ' `body` TEXT NOT NULL,'
                . ' `upload_filename` TEXT NOT NULL ,'
                . ' `upload_data_serialized` LONGTEXT NULL ,'
                . ' PRIMARY KEY ( `submission_id` ) '
                . ' )';
        $database->query($mod_mpform);
                
        // Insert info into the search table
        // Module query info
        $field_info = array();
        $field_info['page_id'] = 'page_id';
        $field_info['title'] = 'page_title';
        $field_info['link'] = 'link';
        $field_info['description'] = 'description';
        $field_info['modified_when'] = 'modified_when';
        $field_info['modified_by'] = 'modified_by';
        $field_info = serialize($field_info);
        $database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('module', 'mpform', '$field_info')");
        // Query start
        $query_start_code = "SELECT [TP]pages.page_id, [TP]pages.page_title,        [TP]pages.link, [TP]pages.description, [TP]pages.modified_when, [TP]pages.modified_by        FROM [TP]mod_mpform_fields, [TP]mod_mpform_settings, [TP]pages WHERE ";
        $database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_start', '$query_start_code', 'mpform')");
        // Query body
        $query_body_code = " [TP]pages.page_id = [TP]mod_mpform_settings.page_id AND [TP]mod_mpform_settings.header LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'
        OR [TP]pages.page_id = [TP]mod_mpform_settings.page_id AND [TP]mod_mpform_settings.footer LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'
        OR [TP]pages.page_id = [TP]mod_mpform_fields.page_id AND [TP]mod_mpform_fields.title LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'";
        $database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_body', '$query_body_code', 'mpform')");
        // Query end
        $query_end_code = "";
        $database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_end', '$query_end_code', 'mpform')");
        
        // Insert blank row (there needs to be at least on row for the search to work)
        // $database->query("INSERT INTO ".TABLE_PREFIX."mod_mpform_fields (page_id,section_id) VALUES ('0','0')");
        $sql = "INSERT INTO `".TABLE_PREFIX."mod_mpform_fields` "
        . "SET `page_id` = '0', "
        .     "`section_id` = '0', "
        .     "`value` = '', "
        .     "`extra` = ''";
        $database->query($sql);
        // $database->query("INSERT INTO ".TABLE_PREFIX."mod_mpform_settings (page_id,section_id) VALUES ('0','0')");
        $sql = "INSERT INTO `".TABLE_PREFIX."mod_mpform_settings` "
        . "SET `page_id` = '0', "
        .     "`section_id` = '0', "
        .     "`header` = '', "
        .     "`field_loop` = '', "
        .     "`footer` = '', "
        .     "`email_to` = '', "
        .     "`email_text` = '', "
        .     "`success_page` = '', "
        .     "`success_text` = '', "
        .     "`submissions_text` = '', "
        .     "`success_email_to` = '', "
        .     "`success_email_text` = '', "
        .     "`heading_html` = '', "
        .     "`short_html` = '', "
        .     "`long_html` = '', "
        .     "`email_html` = '', "
        .     "`uploadfile_html` = '', "
        .     "`upload_files_folder` = '', "
        .     "`date_format` = '' ";

        $database->query($sql);
}


