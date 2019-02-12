<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.28
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2019, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
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

        /**
         *      A simple mini-validator function
         */
        function mpFormTestPost( $aFields, &$respose ) {
                foreach ($aFields as $key=>$options) {
                        if( !isset($_POST[ $key ]) ) {
                                $respose['message'] = "key not submitted";
                                return false;
                        }

                        switch( $options['type'] ) {
                                case 'not_0':
                                        if( $_POST[ $key ] === 0 ) return false;
                                        break;

                                case 'int':
                                        if(!is_numeric($_POST[ $key ])) return false;
                                        break;

                                case 'str':
                                        if(!is_string( $_POST[ $key ]))  return false;
                                        if(isset($options['values'])) {
                                                if(!in_array( $_POST[ $key ], $options['values'] )) return false;
                                        }
                                        break;

                                default:
                                        // no "type" match
                                        return false;
                        }
                }
                return true;
        }

        /**
         *      A list for the $_POST values/keys we want to test.
         */
        $fields = array(
                'iRecordID'             => array( 'type' => 'not_0' ),
                'iSectionID'            => array( 'type' => 'int' ),
                'purpose'                       => array( 'type' => 'str' ),
                'DB_RECORD_TABLE'       => array( 'type' => 'str' , 'values' => array( 'mpform_fields' , 'mpform_submissions' ) ),
                'DB_COLUMN'                     => array( 'type' => 'str' , 'values' => array( 'field_id', 'submission_id' ) ),
                'MODULE'                        => array( 'type' => 'str' , 'values' => array( 'mpform' ) )
        );


        // check if arguments are set
        if ( true === mpFormTestPost( $fields, $aJsonRespond ) )
    {
        // require config for Core Constants
        require('../../../config.php');
        // retrieve Data from ajax data string
        $sDbRecordTable  = TABLE_PREFIX."mod_".$_POST['DB_RECORD_TABLE'];
        $sDbColumn  = $_POST['DB_COLUMN'];
        $iRecordID = $_POST['iRecordID'];
        $sModuleDIR  = $_POST['MODULE'];

        require_once(WB_PATH.'/framework/class.admin.php');
        $admin = new admin('Modules', 'module_view', FALSE, FALSE);
        if(!is_numeric($iRecordID)) {
            if(method_exists( $admin, 'checkIDKEY' ))
               $iRecordID = $admin->checkIDKEY($iRecordID,-1,'key',true);
               else $iRecordID = -1;
        }

        // Check if user has enough rights to do this:
        if (!($admin->is_authenticated() && $admin->get_permission($sModuleDIR, 'module')))
        {
            $aJsonRespond['message'] = 'You\'re not allowed to make changes to this Module: '.$sModuleDIR;
            $aJsonRespond['success'] = FALSE;
            exit(json_encode($aJsonRespond));
        }

    } else {
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

