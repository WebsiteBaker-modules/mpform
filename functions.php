<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.36
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2020, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* This file provides functions and classes for the module */

if (!function_exists('mpform_escape_string')) {
    function mpform_escape_string($sQuery) {
        global $database;
        $sReturn = $sQuery;
        if(isset($database)&&method_exists($database,"escapeString")) {
            $sReturn = $database->escapeString($sQuery);
        } else {
            if (is_object($database->db_handle)
                 && (get_class($database->db_handle) === 'mysqli'))
                     $sReturn = mysqli_real_escape_string($database->db_handle,$sQuery);
            else
                 $sReturn = mysql_real_escape_string($sQuery);
        }
        return $sReturn;
    }
}
