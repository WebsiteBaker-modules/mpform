<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.3.2
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2016, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @url                 https://forum.wbce.org/viewtopic.php?id=661
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        probably php >= 5.3 ?
 *
 **/
/* This file exports the whole section (excluding the submissions) to an xml file.
   The code was taken form the export_section module and integrated into mpform now  */

unset($_GET['page_id']);
unset($_GET['section_id']);

// manually include the config.php file (defines the required constants)
require('../../config.php');

// Include WB admin wrapper script
//require(WB_PATH.'/modules/admin.php');

// include core functions of WB 2.7 to edit the optional module CSS files (frontend.css, backend.css)
@include_once(WB_PATH .'/framework/module.functions.php');

require_once(dirname(__FILE__).'/constants.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));


// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;  


// include WB admin wrapper script to check permissions
$admin_header = false;
require(WB_PATH . '/modules/admin.php');
if ( method_exists( $admin, 'checkFTAN' )  && (!$admin->checkFTAN())) {
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'],
    ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
    $admin->print_footer();
    exit();
}

// protect from cross site scripting
$query_content = $database->query(
    "SELECT *"
    . " FROM ".TABLE_PREFIX."sections"
    . " WHERE section_id = '$section_id'");
    
$res = $query_content->fetchRow();
if ($res['page_id'] != $page_id) {  
    $sUrlToGo = ADMIN_URL."/pages/index.php";
    if(headers_sent())
      $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'],$sUrlToGo);
    else 
      header("Location: ". $sUrlToGo);
    exit(0);
}

// obtain module directory
$curr_dir = dirname(__FILE__);

/*  code originally in config.inc.php, not needed anymore here:

// groups of known modules:
$smooth_modules = array('wysiwyg', 'guestbook');
$warn_modules = array(
    'code'   => 'Make sure to check whether you need to change '
              . 'variable names used in the code of this section!',
    'bakery' => 'Make sure to move and rename all image files used '
              . 'for the articles of this section!',
    'form'   => 'Submissions have been omitted from export!',
    'formx'  => 'Submissions have been omitted from export!<br />'
              . 'It is highly recommended to use the module '
              . '"Migrate formx" to migrate the page to mpform!',
    'mpform' => 'Submissions and results have been omitted from export!'
);
$blocked_modules = array('section_picker', 'foldergallery');

// extract path separator and detect this module name
$path_sep = strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? '\\' : '/';
$module_folder 
    = str_replace(
        WB_PATH 
        . $path_sep 
        . 'modules' 
        . $path_sep,
        '', 
        dirname(__FILE__)
     );
$url_admintools = ADMIN_URL . '/admintools/tool.php?tool=' . $module_folder;

*/

$lines = array();
$lines[] = '<?xml  version="1.0" encoding="'. DEFAULT_CHARSET .'" ?>';

$sql = "SELECT * FROM ". TABLE_PREFIX ."sections where section_id = '$section_id'";
$results = $database->query($sql);
if ($results && $row = $results->fetchRow()) {
    // if ( in_array($row['module'], $blocked_modules)) 
    // after integrating into mpform we restrict this to mpform sections instead
    // we should not arrive here anyway...
    if ($row['module'] != 'mpform'){ 
        $admin->print_header();
        $admin->print_error("Export of sections of type ".$row['module']." is not possible",
            ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
        $admin->print_footer();
        exit;
    } else {
        $lines[] = "<export_section>";
        $lines[] = "\t<module>";
        $lines[] = "\t\t<name>".$row['module']."</name>";
        $sql = "SELECT * FROM ".TABLE_PREFIX ."addons where directory = '".$row['module']."'";
        $results = $database->query($sql);
        if ($results && $row2 = $results->fetchRow()) {
            $lines[] = "\t\t<version>".$row2['version']."</version>";
        }
        /* we don't include the warning anymore
        // look for known issues and warn:
        if (array_key_exists($row['module'], $warn_modules)) {
            $lines[] = "\t\t<warning><![CDATA[". $warn_modules[$row['module']] ."]]></warning>";
        }
        */
        $lines[] = "\t</module>";

        $sql = "SHOW TABLES";
        $result = $database->query($sql);
        while ($row = $result->fetchRow()) {
            // skip non-module tables:
            if (strpos($row[0], TABLE_PREFIX.'mod_') !== 0) continue;    
            // skip submissions from form module:
            if (strpos($row[0], TABLE_PREFIX.'mod_form_submissions') === 0) continue;  
            // skip submissions from formx module:
            if (strpos($row[0], TABLE_PREFIX.'mod_formx_submissions') === 0) continue;  
            // skip submissions from mpform module:
            if (strpos($row[0], TP_MPFORM.'submissions') === 0) continue;  
            $sql = "SHOW COLUMNS FROM " . $row[0] . " LIKE 'section_id'";
            $results = $database->query($sql);
            if ($results && $exists = $results->fetchRow()) {
                $sql2 = "SELECT * FROM " . $row[0] . " WHERE section_id = '$section_id'";
                $results2 = $database->query($sql2);
                $inside_tab = false;
                while ($results2 && $row2 = $results2->fetchRow()) {
                    if (!$inside_tab) {
                        $tn = substr($row[0], strlen(TABLE_PREFIX));
                        $lines[] = "\t<export_section_table>";
                        $lines[] = "\t\t<tablename>$tn</tablename>";
                        $inside_tab = true;
                    }
                    $lines[] = "\t\t<export_section_row>";
                    $i = 0;
                    foreach ($row2 as $k => $v) {
                        $i++;
                        if ($i > 1) {
                            if ($i % 2 == 0) {
                                $cv = addslashes($v);
                                $lines[] 
                                    = "\t\t\t<export_section_field>"
                                    . "<fieldn>$k</fieldn>"
                                    . "<fieldv><![CDATA[" .$cv. "]]></fieldv>"
                                    . "</export_section_field>";
                            }
                        }
                    }
                    $lines[] = "\t\t</export_section_row>";
                }
                if ($inside_tab) {
                    $lines[] = "\t</export_section_table>";
                    $inside_tab = false;
                }
            }
        }    

        $lines[] = "</export_section>";
    }
}
header("Content-Type: text/plain");
header("Content-Disposition: attachment; filename=section_$section_id.xml");
foreach ($lines as $l) echo "$l\r\n";
