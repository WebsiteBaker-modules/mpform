<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.30
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2019, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* This file defines constants. */
// Must include code to stop this file being access directly
if (!defined('WB_PATH'))  {
    exit("Cannot access this file directly");
}

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;

if(file_exists(dirname(__FILE__).'/constants.user.php')){
    include(dirname(__FILE__).'/constants.user.php');
}

if(!defined('MPFORM_IS_DEFAULT')){
    // marker for default checkboxes or radiobuttons
    define('MPFORM_IS_DEFAULT', '{(*#)}');
}

if(!defined('MPFORM_CLASS_PREFIX')){
    define('MPFORM_CLASS_PREFIX', 'mpform_');
}

// be sure to have a slash at the end of the following constant:
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
    if(!function_exists('draw_asp_honeypots')){
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

if(!defined('MPFORM_HEADER')){
    define('MPFORM_HEADER','');
}


if(!defined('MPFORM_FIELD_LOOP')){
    define('MPFORM_FIELD_LOOP',
      '<div class="questionbox {CLASSES} '.MPFORM_CLASS_PREFIX.'title">'
    . '{TITLE}{REQUIRED}:'
    . '<div class="'.MPFORM_CLASS_PREFIX.'help">'
    . '{HELP}'
    . '</div>'
    . '</div>'
    . '<div class="answerbox '.MPFORM_CLASS_PREFIX.'field">'
    . '{FIELD}{ERRORTEXT}'
    . '</div>'
    . '<div class="newline"></div>');
}


if(!defined('MPFORM_FOOTER')){
    define('MPFORM_FOOTER',
      '<div class="submitbox">'
    . '{SUBMIT}'
    . '</div>');
}


if(!defined('MPFORM_SUBMIT_BUTTON')){
    define('MPFORM_SUBMIT_BUTTON',
      '<input'
    . ' type="submit"'
    . ' name="submit"'
    . ' class="'.MPFORM_CLASS_PREFIX.'submit"'
    . ' value="{SUBMIT_TEXT}" />');
}


// MPForm uses section anchors, but if they are disabled in the WB framework,
// it adds its own anchor. If you even want to suppress this, uncomment the following
/*
if(!defined('MPFORM_NO_ANCHOR')){
    define('MPFORM_NO_ANCHOR',true);
}
*/



// when you face problems with generic security exceptions where you would not expect them,
// you can disable each type of security check separately for debugging purposes and at your own risk

// FTAN was quite often a problem, especially in older WB versions. Anyhow, here you can disable the check
/*
if(!defined('MPFORM_SKIP_FTAN')){
    define('MPFORM_SKIP_FTAN',true);
}
*/


// MPForm has some internal plausibility checks of the submitted ids against the ones in the database.
// If you face problems with theses, you can disable the check in the hope that things dont't get worse...
/*
if(!defined('MPFORM_SKIP_ID_CHECK')){
    define('MPFORM_SKIP_ID_CHECK',true);
}
*/


// similar to FTANS the IDKEYs are a WB built-in security feature. They expire once the data sent by forms
// is verified against them, or the same page is called from another window in the same session.
// You can disable the encoding of the ids and also the decoding and test with the following setting:
/*
if(!defined('MPFORM_SKIP_IDKEY')){
    define('MPFORM_SKIP_IDKEY',true);
}
*/


// MPForm also stores the submission_id in the $_SESSION variable when a form is generated and verifies
// it when the form is submitted. The submission_id of the $_POST data is checked against the value
// stored in the $_SESSION. You can disable this check with the following setting:
/*
if(!defined('MPFORM_SKIP_SUBMISSION_ID')){
    define('MPFORM_SKIP_SUBMISSION_ID',true);
}
*/


// Captchas are usually controlled globally by the corresponding admin setting, but if you want to
// disable it locally for MPForm only, you can do so by uncommenting these lines:
/*
if(!defined('MPFORM_SKIP_CAPTCHA')){
    define('MPFORM_SKIP_CAPTCHA',true);
}
*/

// Related but slightly different are the honeypot fields (see above) used for Anti Spam Protection:
/*
if(!defined('MPFORM_SKIP_ASP')){
    define('MPFORM_SKIP_ASP',true);
}
*/

