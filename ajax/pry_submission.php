<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.3.3
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2016, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @url                 https://forum.wbce.org/viewtopic.php?id=661
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        probably php >= 5.3 ?
 *
 **/
/* This file shows the content of a submission for a page in the backend. */

// manually include the config.php file (defines the required constants)
require('../../../config.php');
//require(WB_PATH.'/modules/admin.php');

// obtain module directory
$mod_dir = 'mpform';

// Include WB admin wrapper script
$admin_header = FALSE;
require(WB_PATH.'/modules/admin.php');


// Get id
if(!isset($_GET['submission_id'])) {
    #header("Location: ".ADMIN_URL."/pages/index.php");
    echo 'irgendwas geht nicht';
    exit(0);
} else {
    $iSubmissionID = $_GET['submission_id'];
}

// Get submission details
$query_content = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_mpform_submissions` WHERE `submission_id` = '".intval($iSubmissionID)."'");
$submission = $query_content->fetchRow();

// Get the user details of whoever did this submission
$query_user = "SELECT username,display_name FROM `".TABLE_PREFIX."users` WHERE `user_id` = '".$submission['submitted_by']."'";
$get_user = $database->query($query_user);
if($get_user->numRows() != 0) {
    $user = $get_user->fetchRow();
} else {
    $user['display_name'] = 'Unknown';
    $user['username'] = 'unknown';
}

?>

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="settings_table">
    <!--<tfoot>
        <tr>
            <td style="text-align:left;">
                <input type="button" value="<?php echo $TEXT['CLOSE']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 110px; margin-top: 5px;" />
            </td>
            <td style="text-align:right;"><input type="button" value="<?php echo $TEXT['DELETE']; ?>" onclick="javascript: confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo WB_URL; ?>/modules/mpform/delete_submission.php?page_id=<?php
                echo $page_id; ?>&section_id=<?php echo $section_id; ?>&submission_id=<?php
                echo $iSubmissionID; ?>');" style="width: 110px; margin-top: 5px;" />
            </td>
        </tr>
    </tfoot>
    -->
    <tbody>
        <tr>
            <th><?php echo ucfirst($TEXT['SUBMITTED']); ?>:</th>
            <td><?php echo gmdate(TIME_FORMAT.', '.DATE_FORMAT, $submission['submitted_when']+TIMEZONE); ?></td>
        </td>
        <tr>
            <th><?php echo $TEXT['USER']; ?>:</th>
            <td><?php echo $user['display_name'].' ('.$user['username'].')'; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="background-color:#ccc; height:10px;">
                
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php
                    $lines = explode("\n",$submission['body']);
                    foreach($lines as $k => $v) {
                        $hr = explode('url]',$v);
                        if (count($hr)>1) {
                            $hr[0] = substr($hr[0],0,-1);
                            $hr[1] = substr($hr[1],0,-2);
                            $v = $hr[0]."[url]".$hr[1]."[/url]".$hr[2];
                            echo str_replace(array('[url]','[/url]'), array('<a href="','" target="_blank">'.$hr[1].'</a>'), $v); 
                        } else {
                            echo $v;
                        }
                        echo "<br />";
                    }
                ?>
            </td>
        </tr>    
    </tbody>
</table>
