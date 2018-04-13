<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.22
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2018, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @url                 https://forum.wbce.org/viewtopic.php?id=661
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* This file adds a new page/section with this module to the website. */
// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

require_once(dirname(__FILE__).'/constants.php');

// include the module language file depending on the backend language of the current user
if (!include(get_module_language_file($mod_dir))) return;

// update db schema
$query_content
    = $database->query(
        "SELECT *"
            . " FROM ".TP_MPFORM."settings"
    );

$setting = $query_content->fetchRow();

// set default values
$header = MPFORM_HEADER;
$field_loop = MPFORM_FIELD_LOOP;
$footer = MPFORM_FOOTER;
$heading_html = "<div class={CLASSES}><h3>{HEADING}</h3></div>";
$short_html = "<div class={CLASSES}><b>{TITLE}:</b> {DATA}</div>";
$long_html = "<div class={CLASSES}><b>{TITLE}:</b><br />{DATA}<br /></div>";
$email_html = "<div class={CLASSES}><b>{TITLE}:</b> <a href=\"mailto:{DATA}\">{DATA}</a></div>";
$uploadfile_html = "<div class={CLASSES}><b>{TITLE}:</b> <a href=\"{DATA}\">{DATA}</a></div>";
$date_format = $LANG['backend_adv']['date_format'];
$is_following = 0;
$upload_files_folder = MEDIA_DIRECTORY. "/".$mod_dir;
$email_to = 'SERVER_EMAIL';
$email_from = '';
$email_replyto = $admin->get_email();
$email_fromname = '';
$email_subject = $LANG['backend']['EMAIL_SUBJECT'];
$success_page = 'none';
$success_text
    = '<div'
    . ' class="'.MPFORM_CLASS_PREFIX.'results">'
    . 'Thank you for submitting your data. We received the following data:'
    . '<br />'
    . '{DATA}'
    . '<br />'
    . '</div>';
$submissions_text = '{DATA}'."\n"
    . 'Referer page: {REFERER}'."\n"
    . 'IP address: {IP}'."\n"
    . 'Date: {DATE}';
$email_text = 'The following data was submitted:<br />{DATA}'
    . '<br />'
    . 'Referer page: {REFERER}'
    . '<br />'
    . 'IP address: {IP}';
$email_css = '';
$success_email_to = '';
$success_email_from = 'SERVER_EMAIL';
$success_email_fromname = '';
$success_email_text = 'Thank you for submitting your data.'
    . ' We received the following data:'
    . '<br />'."\n".'{DATA}'
    . '<br />'."\n";
$success_email_css = '';
$success_email_subject = $LANG['backend']['EMAIL_SUC_SUBJ'];
$max_submissions = 50;
$stored_submissions = 1000;
$max_file_size_kb = 1024;
$attach_file = 0;
$upload_file_mask = STRING_FILE_MODE;
$upload_dir_mask = STRING_DIR_MODE;
$upload_only_exts = "jpg,gif,png,tif,bmp,pdf";
if(extension_loaded('gd') AND function_exists('imageCreateFromJpeg')) {
    /* Make's sure GD library is installed */
    $use_captcha = true;
} else {
    $use_captcha = false;
}

$SQL = "INSERT INTO `".TP_MPFORM."settings`"
     . " SET"
     . " `page_id` = '".$page_id."', "
     . " `section_id` = '".$section_id."', "
     . " `header` = '".$header."', "
     . " `field_loop` = '".$field_loop."', "
     . " `footer` = '".$footer."', "
     . " `email_to` = '".$email_to."', "
     . " `email_from` = '".$email_from."', "
     . " `email_replyto` = '".$email_replyto."', "
     . " `email_fromname` = '".$email_fromname."', "
     . " `email_subject` = '".$email_subject."', "
     . " `email_text` = '".$email_text."', "
     . " `email_css` = '".$email_css."', "
     . " `success_page` = '".$success_page."', "
     . " `success_text` = '".$success_text."', "
     . " `submissions_text` = '".$submissions_text."', "
     . " `success_email_to` = '".$success_email_to."', "
     . " `success_email_from` = '".$success_email_from."', "
     . " `success_email_fromname` = '".$success_email_fromname."', "
     . " `success_email_text` = '".$success_email_text."', "
     . " `success_email_css` = '".$success_email_css."', "
     . " `success_email_subject` = '".$success_email_subject."', "
     . " `max_submissions` = '".$max_submissions."', "
     . " `stored_submissions` = '".$stored_submissions."', "
     . " `heading_html` = '".$heading_html."', "
     . " `short_html` = '".$short_html."', "
     . " `long_html` = '".$long_html."', "
     . " `email_html` = '".$email_html."', "
     . " `uploadfile_html` = '".$uploadfile_html."', "
     . " `date_format` = '".$date_format."', "
     . " `max_file_size_kb` = '".$max_file_size_kb."', "
     . " `attach_file` = '".$attach_file."', "
     . " `upload_file_mask` = '".$upload_file_mask."', "
     . " `upload_dir_mask` = '".$upload_dir_mask."', "
     . " `use_captcha` = '".$use_captcha."', "
     . " `upload_files_folder` = '".$upload_files_folder."', "
     . " `upload_only_exts` = '".$upload_only_exts."', "
     . " `is_following` = '".$is_following."', "
     . " `tbl_suffix` = '".$section_id."'";

$database->query($SQL);
