<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.3.7
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2017, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @url                 https://forum.wbce.org/viewtopic.php?id=661
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        probably php >= 5.3 ?
 *
 **/
/* This file provides functions and classes for the module */
if (!function_exists('module_header_footer')) {
    function module_header_footer($page_id, $mod_dir) {
        global $admin, $database, $HEADING, $TEXT, $MESSAGE, $section_id;
        require_once(WB_PATH.'/modules/admin.php');
        require_once(WB_PATH.'/framework/functions.php');

        require(WB_PATH.'/modules/'.$mod_dir.'/info.php');
        //START HEADER HERE

        // Get page details
        $results_array=$admin->get_page_details($page_id);

        // Get display name of person who last modified the page
        $user=$admin->get_user_details($results_array['modified_by']);

        // Convert the unix ts for modified_when to human a readable form
        if($results_array['modified_when'] != 0) {
            $modified_ts = date(TIME_FORMAT.', '.DATE_FORMAT, $results_array['modified_when']);
        } else {
            $modified_ts = 'Unknown';
        }
        // Include page info script
        $template = new Template(WB_PATH.'/modules/'.$mod_dir.'/htt/');
        $template->set_file('page', 'modify.htt');
        $template->set_block('page', 'main_block', 'main');
        $template->set_var(array(
            'PAGE_ID'          => $results_array['page_id'],
            'SECTION_ID'       => $section_id,
            'PAGE_TITLE'       => ($results_array['page_title']),
            'MODULE_TITLE'     => $module_name,
            'MODULE_VERSION'       => $module_version,
            'MODIFIED_BY'      => $user['display_name'],
            'MODIFIED_BY_USERNAME' => $user['username'],
            'MODIFIED_WHEN'    => $modified_ts,
            'ADMIN_URL'        => ADMIN_URL,
            'MOD_CLASS'        => $mod_dir
            )
        );
        if($modified_ts == 'Unknown') {
            $template->set_var('DISPLAY_MODIFIED', 'hide');
        } else {
            $template->set_var('DISPLAY_MODIFIED', '');
        }

        // Work-out if we should show the "manage sections" link
        $query_sections 
            = $database->query(
                "SELECT section_id FROM ".TABLE_PREFIX."sections"
                    . " WHERE page_id = '$page_id'"
                    . " AND module = 'menu_link'"
            );
        if($query_sections->numRows() > 0) {
            $template->set_var('DISPLAY_MANAGE_SECTIONS', 'none');
        } elseif(MANAGE_SECTIONS == 'enabled') {
            $template->set_var('TEXT_MANAGE_SECTIONS', $HEADING['MANAGE_SECTIONS']);
        } else {
            $template->set_var('DISPLAY_MANAGE_SECTIONS', 'none');
        }

        // Insert language TEXT
        $template->set_var(array(
            'TEXT_CURRENT_PAGE' => $TEXT['CURRENT_PAGE'],
            'TEXT_CHANGE_SETTINGS' => $TEXT['CHANGE_SETTINGS'],
            'LAST_MODIFIED' => $MESSAGE['PAGES']['LAST_MODIFIED'],
            'HEADING_MODIFY_PAGE' => $HEADING['MODIFY_PAGE']
            )
        );

        // Parse and print header template
        $template->parse('main', 'main_block', false);
        $template->pparse('output', 'page');

        return $admin;
        //END HEADER HERE
    }  // end of: function module_header_footer
}

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
