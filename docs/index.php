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
/* English language Help file. */
// manually include the config.php file (defines the required constants)
require('../../../config.php');

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die(header('Location: ../index.php'));

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

$sUrlToGo='EN/mpform.html';
if (defined("LANGUAGE")){
    if(file_exists(dirname(__FILE__).'/'.LANGUAGE.'/mpform.html')){
        $sUrlToGo=LANGUAGE.'/mpform.html';
    }
}

if(headers_sent())
   echo '<!DOCTYPE HTML>'
         . '<html lang="en-US">'
         . '<head>'
         . '  <meta charset="UTF-8">'
         . '  <meta http-equiv="refresh" content="1;url='.$sUrlToGo.'">'
         . '  <script type="text/javascript">'
         . '    window.location.href = "'.$sUrlToGo.'";'
         . '  </script>'
         . '  <title>mpForm Help</title>'
         . '</head>'
         . '<body>'
         . '  If you are not redirected automatically, please'
         . '  <a href="'.$sUrlToGo.'">click here</a>'
         . '</body>'
         . '</html>';
 else
   header("Location: ". $sUrlToGo);
