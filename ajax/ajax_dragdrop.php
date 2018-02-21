<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.19
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2018, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @url                 https://forum.wbce.org/viewtopic.php?id=661
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/*      Drag'N'Drop Position
 *      This file is based on the mechanism used in Module "Members" by Chio (www.beesign.com)
 *      Big thanks to Ivan (CrnoGorak) for further hints and help on implementation
**/

$aJsonRespond = array();
$aJsonRespond['success'] = false;
$aJsonRespond['message'] = '';
$aJsonRespond['icon'] = '';


if(!isset($_POST['action']) || !isset($_POST['field_id']) )
{
    $aJsonRespond['message'] = 'one of the parameters does not exist';
    exit(json_encode($aJsonRespond));
}
 else
{
    $aRows = $_POST['field_id'];
    require_once('../../../config.php');
    // check if user has permissions to access the mpform module
    require_once(WB_PATH.'/framework/class.admin.php');
    $admin = new admin('Pages', 'pages_modify', false, false);
    if (!($admin->is_authenticated() && $admin->get_permission('mpform', 'module'))) {
        $aJsonRespond['message'] = 'insuficcient rights';
        exit(json_encode($aJsonRespond));
    }

    // Sanitize variables
    $action = $admin->add_slashes($_POST['action']);
    if ($action == "updatePosition")
    {
        $i = 1;
        foreach ($aRows as $recID) {
            $id = $admin->checkIDKEY($recID,0,'key',true);
            // now we sanitize array
            $database->query("UPDATE `".TABLE_PREFIX."mod_mpform_fields`"
               . " SET `position` = '".$i."'"
               . " WHERE `field_id` = ".intval($id)." ");
            $i++;

        }
        if($database->is_error()) {
            $aJsonRespond['success'] = false;
            $aJsonRespond['message'] = 'db query failed: '.$database->get_error();
            $aJsonRespond['icon'] = 'cancel.gif';
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

