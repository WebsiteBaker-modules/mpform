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
/* This file shows the content of a submission for a page in the backend. */
// manually include the config.php file (defines the required constants)
require('../../config.php');
require(WB_PATH.'/modules/admin.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

require_once(dirname(__FILE__).'/constants.php');


// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;

//START HEADER HERE
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');
module_header_footer($page_id,$mod_dir);
//END HEADER HERE

// Get id
if (method_exists( $admin, 'checkIDKEY' )) {
   $submission_id = $admin->checkIDKEY('submission_id', false, 'GET');
} else {
   $submission_id = (int) $_GET['submission_id'];
}
if ((!$submission_id)
    && (!(defined('MPFORM_SKIP_IDKEY')&&(MPFORM_SKIP_IDKEY)))) {
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
        .' (IDKEY) '.__FILE__.':'.__LINE__,
        ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
    exit();
}

// Get submission details
$query_content
    = $database->query(
        "SELECT *"
            . " FROM `".TP_MPFORM."submissions`"
            . " WHERE `submission_id` = '$submission_id'"
    );
$submission = $query_content->fetchRow();

// Get the user details of whoever did this submission
$query_user
    = "SELECT username,display_name"
       . " FROM `".TABLE_PREFIX."users`"
       . " WHERE `user_id` = '".$submission['submitted_by']."'";
$get_user = $database->query($query_user);
if($get_user->numRows() != 0) {
    $user = $get_user->fetchRow();
} else {
    $user['display_name'] = 'Unknown';
    $user['username'] = 'unknown';
}


echo '<table class="settings_table" cellpadding="0" cellspacing="0" border="0" width="100%">'
    . '<caption>'
    . $TEXT['SUBMISSION_ID']
    . ': '
    . $submission['submission_id']
    . '</caption>'
    . '<tr>'
    . '<th>'
    . $TEXT['SUBMITTED']
    . ': '
    . '</th>'
    . '<td>'
    . date(TIME_FORMAT.', '.DATE_FORMAT, $submission['submitted_when'])
    . '</td>'
    . '</td>'
    . '<tr>'
    . '<th>'
    . $TEXT['USER']
    . ': '
    . '</th>'
    . '<td>'
    . $user['display_name']
    . ' ('
    . $user['username']
    . ')'
    . '</td>'
    . '</tr>'
    . '<tr>'
    . '<td colspan="2">';
$lines = explode("\n",$submission['body']);
foreach($lines as $k => $v) {
    $hr = explode('url]',$v);
    if (count($hr)>1) {
        $hr[0] = substr($hr[0],0,-1);
        $hr[1] = substr($hr[1],0,-2);
        $v = $hr[0]."[url]".$hr[1]."[/url]".$hr[2];
        echo str_replace(
            array('[url]','[/url]'),
            array('<a href="','" target="_blank">'.$hr[1].'</a>'),
            $v
        );
    } else {
        echo $v;
    }
    echo "<br>";
}

echo '</td></tr></table>';

$sModuleUrl =  WB_URL.'/modules/'.basename(dirname(__FILE__));
$sIconDir = $sModuleUrl.'/images';

echo '<table cellpadding="0" cellspacing="0" border="0" width="99%">'
    . '<tr>'
    . '<td align="left">'
    . '<button class="mod_mpform_button" onclick="javascript: confirm_link(\''
    . $TEXT['ARE_YOU_SURE']
    . "', '"
    . $sModuleUrl
    . '/delete_submission.php?page_id='
    . $page_id
    . '&section_id='
    . $section_id
    . '&submission_id='
    . ((method_exists( $admin, 'getIDKEY')
       && (!(defined('MPFORM_SKIP_IDKEY')&&(MPFORM_SKIP_IDKEY))))
       ? $admin->getIDKEY($submission_id)
       : $submission_id)
    . '\');">'
    . '<img src="'
    . $sIconDir
    . '/delete.png" alt="" width="16" height="16" border="0" />'
    .  $TEXT['DELETE']
    . '</button>'
    . '</td>'
    . '<td align="right">'
    . '<button class="mod_mpform_button" onclick="javascript: window.location = \''
    . ADMIN_URL
    . '/pages/modify.php?page_id='
    . $page_id
    . '\';">'
    . $TEXT['BACK']
    . '</button>'
    . '</td></tr></table>';

// Print admin footer
$admin->print_footer();

