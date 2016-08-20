<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.2.3
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2016, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        probably php >= 5.3 ?
 *
 **/

// initialize json_respond array  (will be sent back)
$aJsonRespond = array();
$aJsonRespond['message'] = 'ajax operation failed';
$aJsonRespond['success'] = FALSE;

    if(!isset($_POST['action']) )
    {
        $aJsonRespond['message'] = '"action" was not set';
        exit(json_encode($aJsonRespond));
    }



    // check if arguments are set
    if (    
        isset($_POST['iRecordID']) && $_POST['iRecordID'] !=0
        && isset($_POST['iSectionID']) && is_numeric($_POST['iSectionID'])
        && isset($_POST['purpose']) && is_string($_POST['purpose'])
        && isset($_POST['DB_RECORD_TABLE']) && is_string($_POST['DB_RECORD_TABLE'])
        && (  ($_POST['DB_RECORD_TABLE'] == 'mpform_fields') 
           || ($_POST['DB_RECORD_TABLE'] == 'mpform_submissions'))
        && isset($_POST['DB_COLUMN']) && is_string($_POST['DB_COLUMN'])
        && (  ($_POST['DB_COLUMN'] == 'field_id') 
           || ($_POST['DB_COLUMN'] == 'submission_id'))
        && isset($_POST['MODULE']) && is_string($_POST['MODULE'])
        && (  ($_POST['MODULE'] == 'mpform'))
    )
    {
        // require config for Core Constants
        require('../../../config.php');
        // retrieve Data from ajax data string
        $sDbRecordTable  = TABLE_PREFIX."mod_".$_POST['DB_RECORD_TABLE'];
        $sDbColumn  = $_POST['DB_COLUMN'];
        $iRecordID = $_POST['iRecordID'];
        $sModuleDIR  = $_POST['MODULE'];    
        
            
        // Check if user has enough rights to do this:
        require_once(WB_PATH.'/framework/class.admin.php');
        $admin = new admin('Modules', 'module_view', FALSE, FALSE);    
        if (!($admin->is_authenticated() && $admin->get_permission($sModuleDIR, 'module'))) 
        {
            $aJsonRespond['message'] = 'You\'re not allowed to make changes to this Module: '.$sModuleDIR;        
            $aJsonRespond['success'] = FALSE;
            exit(json_encode($aJsonRespond));
        }
        
    } else    {
        $aJsonRespond['message'] = 'Post arguments missing';
        $aJsonRespond['success'] = FALSE;
        exit(json_encode($aJsonRespond));
    }

    switch ($_POST['purpose'])
    {
        case 'toggle_status':
            // Check the Parameters
            if(!is_numeric($iRecordID) || !isset($_POST['action']) || !(($_POST['action'] == 'readonly') || !($_POST['action'] == 'required') || !($_POST['action'] == 'optional') || !($_POST['action'] == 'disabled'))) {
                $aJsonRespond['message'] = 'failed';
                exit(json_encode($aJsonRespond));
            }
            
            switch($_POST['action']){
                case 'optional':    $status = 0;    break;
                case 'required':    $status = 1;    break;
                case 'readonly':    $status = 2;    break;
                case 'disabled':    $status = 4;    break;
            } 
            $query = "UPDATE `".$sDbRecordTable."`"
               . " SET `required` = '".$status."'"
               . " WHERE `".$sDbColumn."` = '".$iRecordID."' LIMIT 1";
            $database->query($query);
            if($database->is_error()) {
                $aJsonRespond['message'] = 'db query failed';
                exit(json_encode($aJsonRespond));
            }else{
                $aJsonRespond['message'] = ''.$_POST['action'].'_field';
            }
                    
        break;    
        case 'delete_record':
            // Check the Parameters
            if(isset($_POST['action']) && $_POST['action'] == 'delete')    {
                
                if(!is_numeric($iRecordID)) {
                    if(method_exists( $admin, 'checkIDKEY' ))
                       $iRecordID = $admin->checkIDKEY($iRecordID);
                       else $iRecordID = -1;
                }
            
                $query = "DELETE FROM `".$sDbRecordTable."` WHERE `".$sDbColumn."` = '".$iRecordID."' LIMIT 1";
                $database->query($query);
                if($database->is_error()) 
                {
                    $aJsonRespond['message'] = 'db query failed: '.$database->get_error();
                    exit(json_encode($aJsonRespond));
                } else {            
                    $aJsonRespond['message'] = 'Record deleted successfully ';
                }
                // Clean up ordering after deletion
                require(WB_PATH.'/framework/class.order.php');
                $order = new order($sDbRecordTable, 'position', $sDbColumn, 'section_id');
                $order->clean($_POST['iSectionID']); 
            }
            else{
                $aJsonRespond['message'] = "can't delete from list";
                exit(json_encode($aJsonRespond));
            }                
        break;
    }



// If the script is still running, set success to true
$aJsonRespond['success'] = true;
// and echo the json_respond to the ajax function
exit(json_encode($aJsonRespond));

