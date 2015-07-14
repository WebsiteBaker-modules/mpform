<?php
/*
   WebsiteBaker CMS module: mpForm
   ===============================
   This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
   
   @module              mpform
   @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Quinto, Martin Hecht (mrbaseman)
   @copyright           (c) 2009 - 2015, Website Baker Org. e.V.
   @url                 http://www.websitebaker.org/
   @license             GNU General Public License

   Improvements are copyright (c) 2009-2011 Frank Heyne

   For more information see info.php   

*/
/* This file provides a wrapper for quoting database queries (backport for older 
   php versions and wb before 2.8.4 sp3 */

// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

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

