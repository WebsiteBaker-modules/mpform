<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.10
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

/* This file deletes section specific entries in the module tables in the backend. */

require_once(dirname(__FILE__).'/constants.php');


$ts = $database->query("SELECT "
    . "`tbl_suffix` FROM `".TP_MPFORM."settings` "
    . "WHERE `section_id` = '".$section_id."'"
    );
$setting = $ts->fetchRow();
$suffix = $setting['tbl_suffix'];
$results = TP_MPFORM."results_" . $suffix;
$oTestQuery = $database->query("SELECT "
    . "`section_id` FROM `".TP_MPFORM."settings` "
    . "WHERE `tbl_suffix` = '".$suffix."'"
    );


// Delete section
$database->query(
    "DELETE FROM ".TP_MPFORM."fields"
        . " WHERE section_id = '$section_id'"
);

$database->query(
    "DELETE FROM ".TP_MPFORM."settings"
        . " WHERE section_id = '$section_id'"
);

/* remove submissions for this section as well */

$database->query(
    "DELETE FROM ".TP_MPFORM."submissions"
        . " WHERE section_id = '$section_id'"
);

/* also delete results-table if this was the last section using that table */

if (!($oTestQuery->numRows() > 1 )) {
    $database->query("DROP TABLE IF EXISTS $results");
}

