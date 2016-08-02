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
/* This file is called by view.php.
 * Original file name: /include/jscalendar/wb-setup.php
 * Reason for modification: original file did write css into html body, resulting in invalid html code
*/

if(!defined('WB_URL')) {
    header('Location: ../index.php');
    exit(0);
}

require_once(dirname(__FILE__).'/constants.php');


    date_default_timezone_set('Europe/Berlin');

    echo "<script type=\"text/javascript\" src=\""
        . WB_URL ."/include/jscalendar/calendar.js\"></script>";

    // language
    $jscal_lang = defined('LANGUAGE')?strtolower(LANGUAGE):'en';
    $jscal_lang = $jscal_lang!=''?$jscal_lang:'en';
    if(!file_exists(WB_PATH."/include/jscalendar/lang/calendar-$jscal_lang.js")) {
        $jscal_lang = 'en';
    }
    // today
    $jscal_today = date('Y/m/d');
    // first-day-of-week
    $jscal_firstday = '1'; // monday
    if(LANGUAGE=='EN')
        $jscal_firstday = '0'; // sunday
    // date and time format for the text-field and for jscal's "ifFormat". 
    // We offer dd.mm.yyyy or yyyy-mm-dd or mm/dd/yyyy
    // ATTN: strtotime() fails with "dd.mm.yyyy" and PHP4. 
    // So the string has to be converted to e.g. "yyyy-mm-dd", which will work.
    switch(DATE_FORMAT) {
        case 'd.m.Y':
        case 'd M Y':
        case 'l, jS F, Y':
        case 'jS F, Y':
        case 'D M d, Y':
        case 'd-m-Y':
        case 'd/m/Y':
            $jscal_format = 'd.m.Y'; // dd.mm.yyyy hh:mm
            $jscal_ifformat = '%d.%m.%Y';
            break;
        case 'm/d/Y':
        case 'm-d-Y':
        case 'M d Y':
        case 'm.d.Y':
            $jscal_format = 'm/d/Y'; // mm/dd/yyyy hh:mm
            $jscal_ifformat = '%m/%d/%Y';
            break;
        default:
            $jscal_format = 'Y-m-d'; // yyyy-mm-dd hh:mm
            $jscal_ifformat = '%Y-%m-%d';
            break;
    }
    if(isset($jscal_use_time) && $jscal_use_time==TRUE) {
        $jscal_format .= ' H:i';
        $jscal_ifformat .= ' %H:%M';
    }
    // load scripts for jscalendar

echo '<script type="text/javascript" src="'
    .WB_URL.'/include/jscalendar/lang/calendar-'
    .$jscal_lang.'.js"></script>'
    .'<script type="text/javascript" src="'
    .WB_URL.'/include/jscalendar/calendar-setup.js"></script>';
