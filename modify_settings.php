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
/* This file prints the settings form of the module in the backend. */
// unset page/section IDs defined via GET before including the admin file (we expect POST here)
unset($_GET['page_id']);
unset($_GET['section_id']);

// manually include the config.php file (defines the required constants)
require('../../config.php');

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// include core functions of WB 2.7 to edit the optional module CSS files (frontend.css, backend.css)
@include_once(WB_PATH .'/framework/module.functions.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;  

//START HEADER HERE
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');
module_header_footer($page_id, $mod_dir);
//END HEADER HERE

// Get header and footer
$query_content = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_mpform_settings` WHERE `section_id` = '$section_id'");
$setting = $query_content->fetchRow();

// protect from cross page reading
if ($setting['page_id'] != $page_id) {  
        header("Location: ".ADMIN_URL."/pages/index.php");
        exit(0);
}

// include template parser class and set template
require_once(WB_PATH . '/include/phplib/template.inc');
$tpl = new Template(dirname(__FILE__) . '/htt/');

// define how to handle unknown variables (default:='remove', during development use 'keep' or 'comment')
$tpl->set_unknowns('keep');

// define debug mode (default:=0 (disabled), 1:=variable assignments, 2:=calls to get variable, 4:=show internals)
$tpl->debug = 0;

$tpl->set_file('page', 'backend_modify_settings.htt');
$tpl->set_block('page', 'main_block', 'main');

// replace all placeholder {xxx} of the template file with values from language file
foreach($LANG['backend'] as $key => $value) {
        $tpl->set_var($key, $value);
}

// obtain display option from the database table
$table = TABLE_PREFIX . 'mod_mpform_settings';
$sql = "SELECT * FROM `$table` WHERE `section_id` = '$section_id'";
$sql_result = $database->query($sql);
$settings = $sql_result->fetchRow();

// replace all placeholder {xxx} of the template file with values from the db
foreach($settings as $key => $value) {
        $tpl->set_var($key, $value);
}

$email_from_value = $setting['email_from'];
$email_fromname_value = $setting['email_fromname']; 

// replace static template placeholders with values from language file
$tpl->set_var(
        array(
                // variables from Website Baker framework
                'PAGE_ID'                    => (int) $page_id,
                'SECTION_ID'                 => (int) $section_id,
                'ADMIN_URL'                  => ADMIN_URL,
                'WB_URL'                     => WB_URL,
                'MOD_CLASS'                  => strtolower(basename(dirname(__FILE__))),
                'MODULE_URL'                 => WB_URL . "/modules/mpform",
                'FTAN'                       => (WB_VERSION >= "2.8.2") ? $admin->getFTAN() : '',
                //'FTAN'                     => '',

                // variables from global WB language files
                'TXT_SAVE'                   => $TEXT['SAVE'],
                'TXT_CANCEL'                 => $TEXT['CANCEL'],

                'is_following_true_checked'  => (($settings['is_following']==true) ? 'checked="checked"' : ''),
                'is_following_false_checked' => (($settings['is_following']==true) ? '' : 'checked="checked"'),
                'txt_header'                 => $TEXT['HEADER'],
                'txt_field_loop'             => $TEXT['FIELD'].' '.$TEXT['LOOP'],
                'txt_footer'                 => $TEXT['FOOTER'],
                'email_from'                 => ((substr($settings['email_from'], 0, 5) != 'field') && ($settings['email_from'] != 'wbu') ? $settings['email_from'] : ''),
                'des_email_from_field'       => '',
                'email_fromname'             => ((substr($settings['email_fromname'], 0, 5) != 'field') && ($settings['email_fromname'] != 'wbu') ? $settings['email_fromname'] : ''),
                'des_email_fromname_field'   => '',
                'des_email_fromname'         => '',
                'des_email_subject'          => '',
                'txt_email_subject'          => $TEXT['SUBJECT'],
                'des_success_email_to'       => '',
                'des_success_email_from'     => '',
                'des_success_email_fromname' => '',
                'des_success_email_subject'  => '',
                'txt_success_email_subject'  => $TEXT['SUBJECT'],
                'txt_success_email_text'     => $TEXT['TEXT'],
                'txt_email_text'             => $TEXT['TEXT'],
                'NONE'                       => $TEXT['NONE'],
                'TXT_YES'                    => $TEXT['YES'],
                'TXT_NO'                     => $TEXT['NO'],
                // module settings
                'MOD_SAVE_URL'               => WB_URL. str_replace("\\","/",substr(dirname(__FILE__),strlen(WB_PATH))).'/save_settings.php',
                'MOD_CANCEL_URL'             => ADMIN_URL.'/pages/modify.php?page_id='.$page_id
        )
);

function give_me_address_list($curr_value, $java=true, $fname = '', $wbt){        // returns list of email fields from the form
        global $database, $section_id, $TEXT, $tpl;
        $tpl->set_block('main_block', $fname.'_block' , $fname);
        $rt = false;
        
        // add authenticated user:
        $s = "<option value=\"wbu\"";
        if($curr_value == 'wbu') {
                $s .= " selected='selected'";
                $rt = true;
        }
        if ($java) $s .= " onclick=\"javascript: document.getElementById('". $fname."_slave').style.display = 'none';\"";
        $s .= ">$wbt</option>";
        $tpl->set_var('options_'.$fname, $s);
        $tpl->parse($fname, $fname.'_block', true);
        $s = '';

        $query_email_fields = $database->query("SELECT `field_id`,`title` FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `section_id` = '$section_id' AND (`type` = 'email') ORDER BY `position` ASC");
        if($query_email_fields->numRows() > 0) {
                while($field = $query_email_fields->fetchRow()) {
                        $s = "<option value=\"field".$field['field_id']."\"";
                        if($curr_value == 'field'.$field['field_id']) {
                                $s .= " selected='selected'";
                                $rt = true;
                        }
                        if ($java) $s .= " onclick=\"javascript: document.getElementById('". $fname."_slave').style.display = 'none';\"";
                        $s .= ">".$TEXT['FIELD'].': '.$field['title']. "</option>";
                        $tpl->set_var('options_'.$fname, $s);
                      $tpl->parse($fname, $fname.'_block', true);
                }
        } else {
                $tpl->set_var('options_'.$fname, $s);
                $tpl->parse($fname, $fname.'_block', true);
        }
        return $rt;
}

function give_me_name_list($curr_value, $java=true, $fname = '', $wbt){  // returns list of text fields from the form
        global $database, $section_id, $TEXT, $tpl;
        $tpl->set_block('main_block', $fname.'_block' , $fname);
        $rt = false;
        
        // add authenticated user:
        $s = "<option value=\"wbu\"";
        if($curr_value == 'wbu') {
                $s .= " selected='selected'";
                $rt = true;
        }
        if ($java) $s .= " onclick=\"javascript: document.getElementById('". $fname."_slave').style.display = 'none';\"";
        $s .= ">$wbt</option>";
        $tpl->set_var('options_'.$fname, $s);
        $tpl->parse($fname, $fname.'_block', true);
        $s = '';
        
        $query_email_fields = $database->query("SELECT `field_id`,`title` FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `section_id` = '$section_id' AND (`type` = 'textfield') ORDER BY `position` ASC");
        if($query_email_fields->numRows() > 0) {
                while($field = $query_email_fields->fetchRow()) {
                        $s = "<option value=\"field".$field['field_id']."\"";
                        if($curr_value == 'field'.$field['field_id']) {
                                $s .= " selected='selected'";
                                $rt = true;
                        }
                        if ($java) $s .= " onclick=\"javascript: document.getElementById('". $fname."_slave').style.display = 'none';\"";
                        $s .= ">".$TEXT['FIELD'].': '.$field['title']. "</option>";
                        $tpl->set_var('options_'.$fname, $s);
                      $tpl->parse($fname, $fname.'_block', true);
                }
        } else {
                $tpl->set_var('options_'.$fname, $s);
                $tpl->parse($fname, $fname.'_block', true);
        }
        return $rt;
}

function give_me_pages_list($page, $fname){        // returns list of possible success pages
        global $database, $admin, $tpl;
        $tpl->set_block('main_block', $fname.'_block' , 'schleife');
        $s = '';
        // Get exisiting pages and show the pagenames
        $query = $database->query("SELECT * FROM `".TABLE_PREFIX."pages` WHERE `visibility` <> 'deleted'");
        while($mail_page = $query->fetchRow()) {
                if(!$admin->page_is_visible($mail_page)) continue;
                $mail_pagename = $mail_page['menu_title'];
                $success_page = $mail_page['page_id'];
                //$rt .= $success_page.':'.$success_page.':';
                if($page == $success_page) {
                        $selected = ' selected="selected"';
                } else {
                        $selected = '';
                }
                $s = '<option value="'.$success_page.'"'.$selected.'>'.$mail_pagename.'</option>';
                $tpl->set_var('options_'.$fname, $s);
                  $tpl->parse('schleife', $fname.'_block', true);
        } 
}

// fill some fields with lists
$rt1 = give_me_address_list($email_from_value, true, 'email_from_f', $LANG['backend']['TXT_USER_ADDR']);
$tpl->set_var('display_email_from_field', (($rt1) ? 'none' : 'block'));
$rt2 = give_me_name_list($email_fromname_value, true, 'email_fromname_f', $LANG['backend']['TXT_USER_NAME']);
$tpl->set_var('display_email_fromname_field', (($rt2) ? 'none' : 'block'));
give_me_address_list($settings['success_email_to'], false, 'success_email_to', $LANG['backend']['TXT_USER_ADDR']);
give_me_pages_list($settings['success_page'],'success_page');

// Parse template objects output
$tpl->parse('main', 'main_block', false);
$tpl->pparse('output', 'page',false, false);

$admin->print_footer();

