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
/*      Drag'N'Drop Position
 *      This file is based on the mechanism used in Module "Members" by Chio (www.beesign.com)
 *      Big thanks to Ivan (CrnoGorak) for further hints and help on implementation
**/

$aJsonRespond = array();
$aJsonRespond['success'] = false;
$aJsonRespond['message'] = 'hallo';
$aJsonRespond['icon'] = '';
        
        
if(!isset($_POST['action']) || !isset($_POST['field_id']) )        
//if(!isset($_POST['action']))
{         
        $aJsonRespond['message'] = 'eins von den parametern gibts nicht';
        exit(json_encode($aJsonRespond));
}
 else 
{        
        $aRows = $_POST['field_id'];
        require_once('../../../config.php');        
        // check if user has permissions to access the Bakery module
        require_once(WB_PATH.'/framework/class.admin.php');
        $admin = new admin('Modules', 'module_view', false, false);
        if (!($admin->is_authenticated() && $admin->get_permission('mpform', 'module'))) {
                $aJsonRespond['message'] = 'unsuficcient rights';
                exit(json_encode($aJsonRespond));
        }
        
        // Sanitize variables
        $action = $admin->add_slashes($_POST['action']);        
        if ($action == "updatePosition")
        {         
                $i = 1;
                foreach ($aRows as $recID) {
                        // not we sanitize array
                        $database->query("UPDATE `".TABLE_PREFIX."mod_mpform_fields` SET `position` = ".$i." WHERE `field_id` = ".intval($recID)." ");
                        $i++;        
                        
                }
                if($database->is_error()) {
                        $aJsonRespond['success'] = false;
                        $aJsonRespond['message'] = 'db query failed: '.$database->get_error();
                        $aJsonRespond['icon'] = 'trash.gif';
                        exit(json_encode($aJsonRespond));
                }        
        }else{
                $aJsonRespond['message'] = 'wrong arguments "$action"';
                exit(json_encode($aJsonRespond));
        }
        
        $aJsonRespond['icon'] = 'ajax-loader.gif';
        $aJsonRespond['message'] = 'seems everything is fine';
        $aJsonRespond['success'] = true;
        exit(json_encode($aJsonRespond));
} 

