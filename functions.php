<?php
/*
   WebsiteBaker CMS module: mpForm
   ===============================
   This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
   
   @module              mpform
   @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman), Quinto
   @copyright           (c) 2009 - 2015, Website Baker Org. e.V.
   @url                 http://www.websitebaker.org/
   @license             GNU General Public License

   Improvements are copyright (c) 2009-2011 Frank Heyne

   For more information see info.php   

*/
/* This file provides functions and classes for the module */
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
                'PAGE_ID'              => $results_array['page_id'],
                'SECTION_ID'           => $section_id,
                'PAGE_TITLE'           => ($results_array['page_title']),
                'MODULE_TITLE'         => $module_name,
                'MODULE_VERSION'       => $module_version,
                'MODIFIED_BY'          => $user['display_name'],
                'MODIFIED_BY_USERNAME' => $user['username'],
                'MODIFIED_WHEN'        => $modified_ts,
                'ADMIN_URL'            => ADMIN_URL,
                'MOD_CLASS'            => $mod_dir
                )
        );
        if($modified_ts == 'Unknown') {
                $template->set_var('DISPLAY_MODIFIED', 'hide');
        } else {
                $template->set_var('DISPLAY_MODIFIED', '');
        }

        // Work-out if we should show the "manage sections" link
        $query_sections = $database->query("SELECT section_id FROM ".TABLE_PREFIX."sections WHERE page_id = '$page_id' AND module = 'menu_link'");
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

require_once (WB_PATH.'/framework/class.order.php');
class orderx extends order {

// Clean ordering (should be called if a row in the middle has been deleted)
        function move_to($cf_value,$field_id,$position) {
                global $database;

                // Get current index
                $order = $this->get_position($field_id);
                if ($order === false) return false;
                if ($order == $position) return true;

                if($order < $position)
                        $sql = "UPDATE `".$this->table ."` SET `".$this->order_field."` = `".$this->order_field."` - 1 ".
                                                 " WHERE `".$this->common_field."` = '".$cf_value."' ".
                                                 " AND `".$this->order_field."` > '".$order ."' AND `".$this->order_field."` <= '".$position."'";
                else
                        $sql = "UPDATE `".$this->table ."` SET `".$this->order_field."` = `".$this->order_field."` + 1 ".
                                                 " WHERE `".$this->common_field."` = '".$cf_value."' ".
                                                 " AND `".$this->order_field."` >= '".$position ."' AND `".$this->order_field."` < '".$order."'";
                                                 
                $database->query($sql);
                if($database->is_error()) {
                        echo $sql."<br>".$database->get_error();
                        return false;
                }
                $sql = "UPDATE `".$this->table ."` SET `".$this->order_field."` = '".$position."'".
                                         " WHERE `".$this->id_field."` = '".$field_id. "'";

                $database->query($sql);
                if($database->is_error()) {
                        echo $sql."<br>".$database->get_error();
                        return false;
                }
                return true;
        }
        
        function get_position($field_id) {
                global $database;

                // Get current index
                $query_order = "SELECT `".$this->order_field."` FROM `".$this->table."` WHERE `".$this->id_field."` = '$field_id'";
                $get_order = $database->query($query_order);
                if($database->is_error()) {
                        echo $query_order."<br>".$database->get_error();
                        return false;
                }                
                $fetch_order = $get_order->fetchRow();
                $order = $fetch_order[$this->order_field];
                
                return $order;
        }
}  // end of: class orderx extends order

function insert_drag_drop($button_up_cell) {
        return;
}

