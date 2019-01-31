<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.27
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2019, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* This file handles the form in the frontend. */
// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

require_once(dirname(__FILE__).'/constants.php');


// include the module language file depending on the backend language of the current user
if (!include(get_module_language_file($mod_dir))) return;

// check if frontend.css file needs to be included into the <body></body> of view.php
if((!function_exists('register_frontend_modfiles')
    && !defined('MOD_FRONTEND_CSS_REGISTERED'))
    && file_exists(WB_PATH .'/modules/mpform/frontend.css')) {
        echo '<style type="text/css">';
        include_once(WB_PATH .'/modules/mpform/frontend.css');
        echo "\n</style>\n";
}

// check if frontend_body.js file needs to be included into the <body></body> of view.php
if((!function_exists('register_frontend_modfiles_body')
    || !defined('MOD_FRONTEND_BODY_JAVASCRIPT_REGISTERED'))
    && file_exists(WB_PATH .'/modules/mpform/frontend_body.js')) {
        echo '<script src="'.WB_URL.'/modules/mpform/frontend_body.js" type="text/javascript"></script>' . "\n";
}

require_once(WB_PATH.'/include/captcha/captcha.php');

// include private functions, if available
if (file_exists(WB_PATH .'/modules/mpform/private.php')) {
    include_once(WB_PATH .'/modules/mpform/private.php');
}

// for mpForm we need the section anchor  (even if it is suppressed in the framework)
if(!defined( 'SEC_ANCHOR' ) || SEC_ANCHOR == '' || SEC_ANCHOR == 'none'){
    if(!defined( 'MPFORM_NO_ANCHOR' ) || MPFORM_NO_ANCHOR == false ){
        $sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) ? SEC_ANCHOR : '' );
        echo '<a class="section_anchor" id="'.$sSectionIdPrefix.$section_id.'"></a>';
    }
}

// Work-out if the form has been submitted or not
if ($_POST != array()) {
    // some form has been submitted:
    include_once(WB_PATH .'/modules/mpform/evalform.php');
    eval_form($section_id);
} else {
    // the form has not been submitted:
    include_once(WB_PATH .'/modules/mpform/paintform.php');
    paint_form($section_id);
}

