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
if (( method_exists( $admin, 'checkFTAN' )  && (!$admin->checkFTAN()))
    && (!(defined('MPFORM_SKIP_FTAN')&&(MPFORM_SKIP_FTAN)))) {
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']
        .' (FTAN) '.__FILE__.':'.__LINE__,
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
if (($res['page_id'] != $page_id)
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

// get settings
$query_content
    = $database->query(
        "SELECT *"
            . " FROM `".TP_MPFORM."settings`"
            . " WHERE `section_id` = '$section_id'"
    );

$fetch_settings = $query_content->fetchRow();
$suffix = $fetch_settings['tbl_suffix'];

$res = $database->query("SELECT field_id, title, position FROM `".TP_MPFORM."fields` WHERE `section_id` = $section_id");
if($database->is_error()) {
    $admin->print_header();
    $admin->print_error($database->get_error(),
        ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
    $admin->print_footer();
    exit;
}

$fields = array();
while ($row = $res->fetchRow()) {
        $fields['field'.$row['field_id']] = array(
                "title" => $row['title'],
                "position" => $row['position']
        );
}

$res = $database->query("SHOW COLUMNS"
    . " FROM ".TP_MPFORM."results_$suffix"
    );

if($database->is_error()) {
    $admin->print_header();
    $admin->print_error($database->get_error(),
        ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
    $admin->print_footer();
    exit;
}

$columns = array();
$counter = 0;
while($results_col = $res->fetchRow()) {
        $colname = $results_col['Field'];
        $position = 0 - $res->numRows() + $counter++;
        $title = $colname;

        if (isset($fields[$results_col['Field']])) {
                $field = $fields[$results_col['Field']];
                $title = $field["title"];
                $position = $field["position"];
        }

        $columns[$colname] = array(
                "title" => $title,
                "position" => $position
        );
}

uasort($columns, function($a, $b) {
        return $a['position'] - $b['position'];
});

$column_names = array();
foreach ($columns as $key => $elem) {
        $column_names[$key] = $elem["title"];
}

$qs= "SELECT ".join(',', array_keys($column_names))." FROM ".TP_MPFORM."results_$suffix";

if($database->is_error()) {
    $admin->print_header();
    $admin->print_error($database->get_error(),
        ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
    $admin->print_footer();
    exit;
}


$q = $database->query($qs);

$lines = array();
$lines[] = '"'.join('","', $column_names).'"';


// print rest of file:
while ($r=$q->fetchRow()) {
    $line="";
    // print first data row:
    $i = 0;
    foreach ($r as $k) {
        $i++;
        if ($i > 1) {
            if ($i % 2 == 0) {
                if($line!="") $line .= ",";
                $line .= '"'.preg_replace(array('/[\r\n]/','/"/'), array(' ','""'), $k).'"';
            }
        }
    }
    $lines[]=$line;
}


header("Content-Type: text/plain");
header("Content-Disposition: attachment; filename=results_$section_id.csv");
foreach ($lines as $l) echo "$l;\r\n";
