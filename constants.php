<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.2.1
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2016, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        probably php >= 5.3 ?
 *
 **/
/* This file defines constants. */
// Must include code to stop this file being access directly
if (!defined('WB_PATH'))  { 
    exit("Cannot access this file directly"); 
}

if(!defined('IS_DEFAULT')){  
    // marker for default checkboxes or radiobuttons    
    define('IS_DEFAULT', '{(*#)}');
}

if(!defined('MPFORM_CLASS_PREFIX')){ 
    define('MPFORM_CLASS_PREFIX', 'mpform_');
}

if(!defined('MPFORM_ICONS')){ 
    define('MPFORM_ICONS', WB_URL.'/modules/mpform/images/');
}

if(!defined('TP_MPFORM')){ 
    define('TP_MPFORM', TABLE_PREFIX."mod_mpform_");
} 

if(!defined('MPFORM_DIV_WRAPPER')){
    // you may define another class for the wrapper <div class="mpfom"> here
    // or, if you want to remove this wrapper completely, comment the next line like this:
    // define('MPFORM_DIV_WRAPPER', "mpform");
    define('MPFORM_DIV_WRAPPER', "mpform");
}

if(ENABLED_ASP) { 
    if(!function_exists('print_asp_honeypots')){
        function draw_asp_honeypots($iSectionID) {
            $sTimeStamp = time();
            $_SESSION['submitted_when'.$iSectionID] = $sTimeStamp; 
            $sHoneyPots 
                = '<input'
                . ' type="hidden"'
                . ' name="submitted_when'.$iSectionID.'"'
                . ' value="'.$sTimeStamp.'" />'
                . '<p class=nixhier>'
                
                . 'email address:'
                . '<label for="email_'.$iSectionID.'">'
                . 'Leave this field email-address blank:'
                . '</label>'
                . '<input id="email_'.$iSectionID.'"'
                . ' name="email"'
                . ' size="56"'
                . ' value="" />'
                . '<br />'
                
                . 'Homepage:'
                . '<label'
                . ' for="homepage_'.$iSectionID.'">'
                . 'Leave this field homepage blank:'
                . '</label>'
                . '<input'
                . ' id="homepage_'.$iSectionID.'"'
                . ' name="homepage"'
                . ' size="55"'
                . ' value="" />'
                . '<br />'
                . 'URL:'
                
                . '<label'
                . ' for="url_'.$iSectionID.'">'
                . 'Do not fill out this field url:'
                . '</label>'
                . '<input'
                . ' id="url_'.$iSectionID.'"'
                . ' name="url"'
                . ' size="63"'
                . ' value="" />'
                . '<br />'
                
                . 'Comment:'
                . '<label'
                . ' for="comment_'.$iSectionID.'">'
                . 'Leave this field comment blank:'
                . '</label>'
                . '<textarea id="comment_'.$iSectionID.'"'
                . ' name="comment"'
                . ' cols="50"'
                . ' rows="10">'
                . '</textarea>'
                . '<br />'
                . '</p>';
            return $sHoneyPots;
        }
    }
}

if(!defined('MPFORM_DEFAULT_OPT_SEPARATOR')){
    define('MPFORM_DEFAULT_OPT_SEPARATOR', "&#0;");
}
