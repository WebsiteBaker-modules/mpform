<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.32
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2019, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* This file provides the deinstallation function of the module. */
// Must include code to stop this file from being accessed directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

require_once(dirname(__FILE__).'/constants.php');


$database->query(
    "DELETE FROM ".TABLE_PREFIX."search"
        . " WHERE name = 'module'"
        . " AND value = 'mpform'"
);

$database->query(
    "DELETE FROM ".TABLE_PREFIX."search"
        . " WHERE extra = 'mpform'"
);

$database->query(
    "DROP TABLE IF EXISTS `".TP_MPFORM."fields`"
);
$database->query(
    "DROP TABLE IF EXISTS `".TP_MPFORM."settings`"
);
$database->query(
    "DROP TABLE IF EXISTS `".TP_MPFORM."submissions`"
);

$results = TP_MPFORM."results_%";
$t = $database->query("SHOW TABLES LIKE '".$results."'");
if ($t->numRows() > 0 ) {
    while ($tn = $t->fetchRow()) {
        $database->query("DROP TABLE IF EXISTS `".$tn[0]."`");
    }
}


