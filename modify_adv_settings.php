<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.17
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
/*  This file prints the advanced settings form of the module in the backend.*/
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

require_once(dirname(__FILE__).'/constants.php');

// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;

//START HEADER HERE
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');
module_header_footer($page_id, $mod_dir);
//END HEADER HERE

// Get header and footer
$query_content
    = $database->query(
        "SELECT *"
            . " FROM `".TP_MPFORM."settings`"
            . " WHERE `section_id` = '$section_id'"
    );

$setting = $query_content->fetchRow();

// protect from cross page reading
if (($setting['page_id'] != $page_id)
    && (!(defined('MPFORM_SKIP_ID_CHECK')&&(MPFORM_SKIP_ID_CHECK)))) {
    $sUrlToGo = ADMIN_URL."/pages/index.php";
    if(headers_sent())
      $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
      .' (ID_CHECK) '.__FILE__.':'.__LINE__,
      $sUrlToGo);
    else
      header("Location: ". $sUrlToGo);
    exit(0);
}

// include template parser class and set template
if (file_exists(WB_PATH . '/include/phplib/template.inc'))
    require_once(WB_PATH . '/include/phplib/template.inc');
$tpl = new Template(dirname(__FILE__) . '/htt/');

// define how to handle unknown variables
// (default:='remove', during development use 'keep' or 'comment')
$tpl->set_unknowns('keep');

// define debug mode (default:=0 (disabled), 1:=variable assignments,
//                    2:=calls to get variable, 4:=show internals)
$tpl->debug = 0;

$tpl->set_file('page', 'backend_modify_adv_settings.htt');
$tpl->set_block('page', 'main_block', 'main');

// replace all placeholder {xxx} of the template file with values from language file
foreach($LANG['backend_adv'] as $key => $value) {
    $tpl->set_var($key, $value);
}

// obtain display option from the database table
$table = TP_MPFORM.'settings';
$sql = "SELECT *"
     . " FROM `$table`"
     . " WHERE `section_id` = '$section_id'";
$sql_result = $database->query($sql);
$settings = $sql_result->fetchRow();
if($settings['value_option_separator']=="")
    $settings['value_option_separator']=MPFORM_DEFAULT_OPT_SEPARATOR; // fallback
$settings['value_option_separator']=
    htmlspecialchars($settings['value_option_separator'],ENT_QUOTES);

// replace all placeholder {xxx} of the template file with values from the db
foreach($settings as $key => $value) {
    $tpl->set_var($key, $value);
}
$sCaptchaType = $database->get_one(
    'SELECT `captcha_type` FROM `'.TABLE_PREFIX.'mod_captcha_control`'
);
$sImgSrc = '/include/captcha/captchas/'.$sCaptchaType.'.png';
$sCaptchaImgSrc = (file_exists(WB_PATH.$sImgSrc)) ? WB_URL.$sImgSrc : '';



// replace static template placeholders with values from language file
$tpl->set_var(
    array(
        // variables from Website Baker framework
        'PAGE_ID'                   => (int) $page_id,
        'SECTION_ID'                => (int) $section_id,
        'MOD_CLASS'                 => strtolower(basename(dirname(__FILE__))),
        'MODULE_URL'                => WB_URL . "/modules/mpform",
        'FTAN'                      => method_exists( $admin, 'getFTAN' ) ? $admin->getFTAN() : '',

        // variables from global WB language files
        'des_use_captcha'           => '',
        'txt_use_captcha'           => $TEXT['CAPTCHA_VERIFICATION'],
        'CAPTCHA_TYPE_IMAGE'        => $sCaptchaImgSrc != ''
                                        ? '<img style="border:1px dotted #ccc; "'
                                            . ' alt="captcha_example"'
                                            . '  id="captcha_example"'
                                            . '  src="'.$sCaptchaImgSrc.'" />'
                                        : '',
        'use_captcha_true_checked'  => (($settings['use_captcha']==true)
                                        ? 'checked="checked"' : ''),
        'use_captcha_false_checked' => (($settings['use_captcha']==true)
                                        ? '' : 'checked="checked"'),
        'des_max_submissions'       => '',
        'txt_max_submissions'       => $TEXT['MAX_SUBMISSIONS_PER_HOUR'],
        'des_stored_submissions'    => '',
        'txt_stored_submissions'    => $TEXT['SUBMISSIONS_STORED_IN_DATABASE'],
        'des_upload_files_folder'   => '',
        'MEDIA_DIRECTORY'           => WB_PATH . MEDIA_DIRECTORY,
        'des_attach_file'           => '',
        'attach_file_true_checked'  => (($settings['attach_file']==true)
                                        ? 'checked="checked"' : ''),
        'attach_file_false_checked' =>(($settings['attach_file']==true)
                                        ? '' : 'checked="checked"'),
        'des_max_file_size_kb'      => '',
        'TXT_ENABLED'               => $TEXT['ENABLED'],
        'TXT_DISABLED'              => $TEXT['DISABLED'],
        'TXT_SAVE'                  => $TEXT['SAVE'],
        'TXT_CANCEL'                => $TEXT['CANCEL'],

        // module settings
        'MOD_SAVE_URL'              => WB_URL
                                        . str_replace("\\","/",
                                            substr(
                                                dirname(__FILE__),
                                                strlen(WB_PATH)
                                            )
                                        )
                                        . '/save_adv_settings.php',
        'MOD_CANCEL_URL'            => ADMIN_URL.'/pages/modify.php?page_id='.$page_id
    )
);

// Parse template objects output
$tpl->parse('main', 'main_block', false);
$tpl->pparse('output', 'page',false, false);

$admin->print_footer();

