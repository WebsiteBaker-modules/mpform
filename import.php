<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.3.0
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
/* This file allows to import a section (previously saved by means of export.php
   The code was taken form the import_section module and integrated into mpform now   */

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


// tell the admin wrapper to update the DB settings when this page was last updated
$update_when_modified = true;
// include WB admin wrapper script to check permissions
$admin_header = false;
require(WB_PATH . '/modules/admin.php');
if ( method_exists( $admin, 'checkFTAN' )  && (!$admin->checkFTAN())) {
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'],
    ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
    $admin->print_footer();
    exit();
} else {
    $admin->print_header();
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

// convert page/section id to numbers 
// (already checked by /modules/admin.php but kept for consistency)
$page_id = (isset($_POST['page_id'])) ? (int) $_POST['page_id'] : '';
$section_id = (isset($_POST['section_id'])) ? (int) $_POST['section_id'] : '';

$err = "";
$xml = '';
$import_module_name = "";

if(empty($_FILES['importfile']['name'])){
   $err = $LANG['frontend']['err_no_upload'];
   $import_module_name = "mpform"; // skip next test
} else {
   $xml = simplexml_load_file($_FILES['importfile']['tmp_name']);
   $import_module_name = $xml->module->name;
}

// instead of the version checks below we introduce other checks here,
// first one: restrict to mpform exports only:

if($import_module_name != "mpform"){
   $err = $LANG['backend']['txt_import_err_wrong_module'];
}

// check if there are some fields already:

$query_fields 
    = $database->query(
        "SELECT *"
        . " FROM `".TP_MPFORM."fields`"
        . " WHERE `section_id` = '$section_id'"
        . " ORDER BY `position` ASC");
if($query_fields->numRows() > 0) {
    $err = $LANG['backend']['txt_import_err_not_empty'];
}
   
/* skip these version checks now, once this is integrated into mpform

echo "<p>DEBUG: Importing content into section "
     . $section_id 
     . " on page "
     . $page_id 
     . " <br />Module: $import_module_name - Version "
     . $xml->module->version 
     . "</p>\n"; 

$sql = "SELECT *"
     . " FROM ".TABLE_PREFIX ."addons"
     . " where directory = '".$xml->module->name."'";
$results = $database->query($sql);

if ($results && $row = $results->fetchRow()) {
    if ($row['version'] < $xml->module->version) {
        $err = "Old version <b>"
             . $row['version']
             . "</b> of module <b>"
             . $xml->module->name
             . "</b> installed, update to version <b>"
             . $xml->module->version
             . "</b> first!";
    } elseif ($row['version'] > $xml->module->version) {
        $err = "Newer version <b>"
             . $row['version']
             . "</b> of module <b>"
             . $xml->module->name
             . "</b> installed, update source system from version <b>"
             . $xml->module->version
             . "</b> to this version and redo the export!";
    }
} else $err = "Module <b>"
            . $xml->module->name
            . "</b> - Version <b>"
            . $xml->module->version 
            . "</b> is required to be installed before you can import this section!";
*/

if ($err!=""){
    $admin->print_error($err,
    ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);
    $admin->print_footer();
    exit;
}

/* this is not included anymore in current exports:
if (isset($xml->module->warning)) 
    echo "<p>Warning: <b>"
         . $xml->module->warning
         . "</b></p>\n";
// instead we include a fixed mpform warning: */
 
echo "<p>"
     . $LANG['backend']['txt_import_warning']
     . "</p>\n";

// before we insert new tables/rows into the database we should drop the old ones
require_once(WB_PATH.'/modules/'.$mod_dir.'/delete.php');

$num_tables = count($xml->export_section_table);
// echo "<p>DEBUG: Number of tables to change: <b>$num_tables</b></p>\n";

$mpform_import_fields = '';

// looping through tables
$i = 0;
$ok = true;
while ($i < $num_tables) {
    $n = $i+1;
    $tn = TABLE_PREFIX . $xml->export_section_table[$i]->tablename;
    // echo "<p>DEBUG: Table $n: <b>$tn</b></p>\n";
    
    // does table have an autoincrement field??
    $sql = "SHOW COLUMNS FROM `$tn` WHERE extra LIKE 'auto_increment'";
    $results = $database->query($sql);
    if ($results && $row = $results->fetchRow()) {
        $aif = $row['Field'];
        // echo "<p>DEBUG: This table has the auto_increment field <b>$aif</b></p>\n";
    } else {
        $aif = "";
        // echo "<p>DEBUG: This table has no auto_increment field.</p>\n";
    }
    
    // mpform might create an own table for each section
    $mpform_extra_f 
        = ($xml->export_section_table[$i]->tablename == 'mod_mpform_fields') 
        ? true : false;
    
    $num_rows = count($xml->export_section_table[$i]->export_section_row);
    // echo "<p>DEBUG: This table has <b>$num_rows</b> rows</p>\n";
    
    // looping through table rows
    $j = 0;
    while ($j < $num_rows) {
        $num_fields 
            = count(
                $xml
                ->export_section_table[$i]
                ->export_section_row[$j]
                ->export_section_field
            );
        // echo "<p>DEBUG: Row $j has <b>$num_fields</b> fields</p>\n";

        // looping through fields
        $field_names = "";
        $field_values = "";
        $k = 0;
        while ($k < $num_fields) {
            $fn = $xml
                ->export_section_table[$i]
                ->export_section_row[$j]
                ->export_section_field[$k]
                ->fieldn;
            $fv = $xml
                ->export_section_table[$i]
                ->export_section_row[$j]
                ->export_section_field[$k]
                ->fieldv;
            if ($fn == 'page_id') $fv = $page_id;
            if ($fn == 'section_id') $fv = $section_id;
            if ($fn != $aif) {  // skip auto_increment field!
                if ($field_names == "") {
                    $field_names  = "`$fn`";
                    $field_values = "'$fv'";
                } else {
                    $field_names  .= ", `$fn`";
                    $field_values .= ", '$fv'";
                }
            }
            $k++;
        }
        $sql = "INSERT INTO $tn ($field_names) VALUES ($field_values)";
        $i_result = $database->query($sql);
        if ($database->is_error()) {
            echo "<br />ERROR: " . $database->get_error() . "<br />\n";
            $ok = false;
        } else {
            // echo "DEBUG: Inserted row succesfully<br />\n";
            if ($mpform_extra_f) {
                // Get the new field id
                $field_id = $database->get_one("SELECT LAST_INSERT_ID()");
                if ($mpform_import_fields != "") $mpform_import_fields .= ", ";
                $mpform_import_fields .= "ADD `field$field_id` TEXT NOT NULL";
            }
        }
        $j++;
    }
    $i++;
}

// mpform only:
if ($mpform_import_fields != "") {
    // Check whether results table exists, create it if not
    $ts = $database->query(
        "SELECT `tbl_suffix`"
            . " FROM `".TABLE_PREFIX."mod_mpform_settings`"
            . " WHERE `section_id` = '$section_id'"
        );
    $setting = $ts->fetchRow();
    $suffix = $setting['tbl_suffix'];
    if ($suffix != "DISABLED"){
        $results = TABLE_PREFIX . "mod_mpform_results_" . $suffix;
        $t = $database->query("SHOW TABLES LIKE '".$results."'");
        if ($t->numRows() < 1 ) {
            $s = "CREATE TABLE `$results` ( `session_id` VARCHAR(20) NOT NULL,"
                    // time when first form was sent to browser:
                . ' `started_when` INT NOT NULL DEFAULT \'0\' ,'
                    // time when last form was sent back to server:
                . ' `submitted_when` INT NOT NULL DEFAULT \'0\' ,'
                    // referer page:
                . ' `referer` VARCHAR( 255 ) NOT NULL, '
                . ' PRIMARY KEY ( `session_id` ) '
                . ' )';
            $database->query($s);
        }
    }
    
    // Insert new column into database
    $sql = "ALTER TABLE `$results` $mpform_import_fields";
    $database->query($sql);
}

/* we do not have to change the module type anymore since the import 
   is already inside of a mpform section
   
if ($ok) {
    $sql = "UPDATE "
         . TABLE_PREFIX."sections"
         . " SET `module` = '$import_module_name'"
         . " WHERE `section_id` = '$section_id' LIMIT 1";
    $database->query($sql);
    if ($database->is_error()) {
        echo "<br />ERROR: " . $database->get_error() . "<br />\n";
        $ok = false;
    } else {
        echo "<br />Changed section succesfully to type $import_module_name<br />\n";
    }
}

*/

$admin->print_success($TEXT['SUCCESS'],    
ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id);

$admin->print_footer();

