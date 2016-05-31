<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.1.24
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Quinto, Martin Hecht (mrbaseman)
 * @copyright           (c) 2009 - 2016, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        
 *
 **/
/* This file adds a new page/section with this module to the website. */
// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

// include the module language file depending on the backend language of the current user
if (!include(get_module_language_file($mod_dir))) return;

// update db schema
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_settings");
$setting = $query_content->fetchRow();

// set default values
$header = '<table class="mpform-maintable" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">';
$field_loop = '<tr class="{CLASSES}"><td class=\"mpform_title\">{TITLE}{REQUIRED}:</td><td class="mpform_help">{HELP}</td><td class="mpform_field">{FIELD}{ERRORTEXT}</td></tr>';
$footer = '<tr><td></td><td></td>
<td><input type=\"submit\" name=\"submit\" class=\"mpform_submit\" value=\"'.$LANG['backend']['TXT_SUBMIT'].'\" /></td></tr>
</table>';
$heading_html = "<h3>{HEADING}</h3>";
$short_html = "<b>{TITLE}:</b> {DATA}<br />";
$long_html = "<b>{TITLE}:</b><br />{DATA}<br /><br />";
$email_html = "<b>{TITLE}:</b> <a href=\"mailto:{DATA}\">{DATA}</a><br />";
$uploadfile_html = "<b>{TITLE}:</b> <a href=\"{DATA}\">{DATA}</a><br />";
$date_format = $LANG['backend_adv']['date_format'];
$is_following = 0;
$upload_files_folder = MEDIA_DIRECTORY. "/".$mod_dir;
$email_to = $admin->get_email();
$email_from = '';
$email_replyto = '';
$email_fromname = '';
$email_subject = $LANG['backend']['EMAIL_SUBJECT'];
$success_page = 'none';
$success_text = '<div class="mpform_results">Thank you for submitting your data. We received the following data:<br />{DATA}
<br /></div>';
$submissions_text = '{DATA}
Referer page: {REFERER}
IP address: {IP}
Date: {DATE}';
$email_text = 'The following data was submitted:<br />{DATA}
<br />Referer page: {REFERER}<br />IP address: {IP}';
$success_email_to = '';
$success_email_from = $admin->get_email();
$success_email_fromname = '';
$success_email_text = 'Thank you for submitting your data. We received the following data:<br />{DATA}
<br />';
$success_email_subject = $LANG['backend']['EMAIL_SUC_SUBJ'];
$max_submissions = 50;
$stored_submissions = 1000;
$max_file_size_kb = 1024;
$attach_file = 0;
$upload_file_mask = STRING_FILE_MODE;
$upload_dir_mask = STRING_DIR_MODE;
$upload_only_exts = "jpg,gif,png,tif,bmp,pdf";
if(extension_loaded('gd') AND function_exists('imageCreateFromJpeg')) { /* Make's sure GD library is installed */
        $use_captcha = true;
} else {
        $use_captcha = false;
}
$SQL  = "INSERT INTO ". TABLE_PREFIX. "mod_mpform_settings (";
$SQL .= "                page_id        , ";
$SQL .= "                section_id     , ";
$SQL .= "                header         , ";
$SQL .= "                field_loop     , ";
$SQL .= "                footer         , ";
$SQL .= "                email_to       , ";
$SQL .= "                email_from     , ";
$SQL .= "                email_replyto  , ";
$SQL .= "                email_fromname , ";
$SQL .= "                email_subject  , ";
$SQL .= "                email_text     , ";
$SQL .= "                success_page   , ";
$SQL .= "                success_text   , ";
$SQL .= "                submissions_text, ";
$SQL .= "                success_email_to, ";
$SQL .= "                success_email_from, ";
$SQL .= "                success_email_fromname, ";
$SQL .= "                success_email_text, ";
$SQL .= "                success_email_subject, ";
$SQL .= "                max_submissions, ";
$SQL .= "                stored_submissions, ";
$SQL .= "                heading_html   , ";
$SQL .= "                short_html     , ";
$SQL .= "                long_html      , ";
$SQL .= "                email_html     , ";
$SQL .= "                uploadfile_html, ";
$SQL .= "                date_format    , ";
$SQL .= "                max_file_size_kb, ";
$SQL .= "                attach_file    , ";
$SQL .= "                upload_file_mask,";
$SQL .= "                upload_dir_mask,";
$SQL .= "                use_captcha    , ";
$SQL .= "                upload_files_folder, ";
$SQL .= "                upload_only_exts,";
$SQL .= "                is_following   ,";
$SQL .= "                tbl_suffix";
$SQL .= "        ) ";
$SQL .= "        VALUES ";
$SQL .= "        ( ";
$SQL .= "                '$page_id'              , ";
$SQL .= "                '$section_id'           , ";
$SQL .= "                '$header'               , ";
$SQL .= "                '$field_loop'           , ";
$SQL .= "                '$footer'               , ";
$SQL .= "                '$email_to'             , ";
$SQL .= "                '$email_from'           , ";
$SQL .= "                '$email_replyto'        , ";
$SQL .= "                '$email_fromname'       , ";
$SQL .= "                '$email_subject'        , ";
$SQL .= "                '$email_text'           , ";
$SQL .= "                '$success_page'         , ";
$SQL .= "                '$success_text'         , ";
$SQL .= "                '$submissions_text'     , ";
$SQL .= "                '$success_email_to'     , ";
$SQL .= "                '$success_email_from'   , ";
$SQL .= "                '$success_email_fromname', ";
$SQL .= "                '$success_email_text'   , ";
$SQL .= "                '$success_email_subject', ";
$SQL .= "                '$max_submissions'      , ";
$SQL .= "                '$stored_submissions'   , ";
$SQL .= "                '$heading_html'         , ";
$SQL .= "                '$short_html'           , ";
$SQL .= "                '$long_html'            , ";
$SQL .= "                '$email_html'           , ";
$SQL .= "                '$uploadfile_html'      , ";
$SQL .= "                '$date_format'          , ";
$SQL .= "                '$max_file_size_kb'     , ";
$SQL .= "                '$attach_file'          , ";
$SQL .= "                '$upload_file_mask'     , ";
$SQL .= "                '$upload_dir_mask'      , ";
$SQL .= "                '$use_captcha'          , ";
$SQL .= "                '$upload_files_folder'  , ";
$SQL .= "                '$upload_only_exts'     , ";
$SQL .= "                '$is_following'         , ";
$SQL .= "                '$section_id'             ";
$SQL .= "        )";
$database->query($SQL);


