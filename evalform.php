<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.1.22
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Quinto, Martin Hecht (mrbaseman)
 * @copyright           (c) 2009 - 2016, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        
 *
 **/
/* This file evaluates the submitted form in the frontend. */

// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include the wrapper for escaping sql queries in old php / WB versions
require_once(WB_PATH.'/modules/'.$mod_dir.'/dbfunctions.php');

if (!function_exists('mpform_upload_one_file')) {
    function mpform_upload_one_file($fieldid, $fileid, $upload_files_folder, $filename, $only_exts, $chmod, $maxbytes) {
        // include strings for this function
        $mod_dir = basename(dirname(__FILE__));
        @include(get_module_language_file($mod_dir));

        // stop if file too large
        if ($_FILES[$fieldid]['size'][$fileid] > $maxbytes) {
            $s = sprintf($LANG['frontend']['err_too_large'], $_FILES[$fieldid][$fileid]['size'], $maxbytes);
            return $s;
        }
        
        // stop after upload error
        if ($_FILES[$fieldid]['error'][$fileid] == 1) {
            $s = sprintf($LANG['frontend']['err_too_large2'], $maxbytes);
            return $s;
        } elseif ($_FILES[$fieldid]['error'][$fileid] == 2) {
            $s = sprintf($LANG['frontend']['err_too_large2'], $maxbytes);
            return $s;
        } elseif ($_FILES[$fieldid]['error'][$fileid] == 3) {
            $s = $LANG['frontend']['err_partial_upload'];
            return $s;
        } elseif ($_FILES[$fieldid]['error'][$fileid] == 4) {
            $s = $LANG['frontend']['err_no_upload'];
            return $s;
        }
    
        $cwd = dirname(__FILE__);
        $old_path = ini_get("include_path");
        ini_set("include_path", $old_path.((strstr($old_path,';')) ? ';' : ':').$cwd."/pear/");
        require_once "Upload.php";
        $lang =  DEFAULT_LANGUAGE;

        $upload = new http_upload(strtolower($lang));
    
        if ($chmod) $upload->setChmod(intval($chmod, 8));
    
        $file = $upload->getFiles($fileid, true);
    
        if ($upload->isError($file)) return $file->getMessage();
    
        if (trim($only_exts)) {
            $a = explode(",",$only_exts);
            $file->setValidExtensions($a,'accept');
        } else {
            $a = array('NOT_POSSIBLE_ONE');
            $file->setValidExtensions($a,'deny');
        }
    
        if (!$file->isMissing()) {
            if ($file->isValid()) {
                $file->setName($filename);
                $dest_name = $file->moveTo($upload_files_folder);
                if ($upload->isError($dest_name)) return $dest_name->getMessage();
            } elseif ($file->isError()) return $file->errorMsg();
        } else {
            return "$fileid - missing... ".$file->errorMsg();
        }
        return false;  // upload did not fail  
    }
}


if (!function_exists('NewWbMailer')) {
    function NewWbMailer()
    {
        if (!class_exists('WbMailer', false)) {
        //its wb <2.8.4
        if (!class_exists('wbmailer', false)) {
            include_once(WB_PATH.'/include/phpmailer/class.phpmailer.php');
            include_once(WB_PATH.'/framework/class.wbmailer.php');
        }
            return new wbmailer();
        } else {
            return new WbMailer();
        }
    }
}

if (!function_exists('mpform_mailx')) {
    // Validate send email
    function mpform_mailx($fromaddress, $replytoaddress, $toaddress, $subject, $message, $fromname='', $file_attached='') {
        /* 
            INTEGRATED OPEN SOURCE PHPMAILER CLASS FOR SMTP SUPPORT AND MORE
            SOME SERVICE PROVIDERS DO NOT SUPPORT SENDING MAIL VIA PHP AS IT DOES NOT PROVIDE SMTP AUTHENTICATION
            NEW WBMAILER CLASS IS ABLE TO SEND OUT MESSAGES USING SMTP WHICH RESOLVE THESE ISSUE (C. Sommer)

            NOTE:
            To use SMTP for sending out mails, you have to specify the SMTP host of your domain
            via the Settings panel in the backend of Website Baker
        */ 

        $fromaddress = preg_replace('/[\r\n]/', '', $fromaddress);
        $subject = preg_replace('/[\r\n]/', '', $subject);
        $htmlmessage = preg_replace('/[\r\n]/', "<br />\n", $message);
        $plaintext = preg_replace(",<br />,", "\r\n", $message);
        $plaintext = preg_replace(",</h.>,", "\r\n", $plaintext);
        $plaintext = htmlspecialchars_decode(preg_replace(",</?\w+>,", " ", $plaintext), ENT_NOQUOTES);

        // create PHPMailer object and define default settings
        $myMail = NewWbMailer();

        // set user defined from address
        if ($fromaddress!='') {
            if($fromname!='') $myMail->FromName = $fromname;     // FROM-NAME
            $myMail->From = $fromaddress;                // FROM:
        }

        // set user defined replyto address
        if ($replytoaddress!='') {
            $myMail->AddReplyTo($replytoaddress);               // REPLY TO:
        } else {
            $myMail->AddReplyTo($fromaddress);               // REPLY TO:
        }
        

        // define recipient(s)
        $emails = explode(",", $toaddress);
        foreach ($emails as $recip) {
            if (trim($recip) != '') {
                if (preg_match("/^bcc\:(.*?)\<(.*?)\>$/i",trim($recip),$matches)) { //bcc whith name
                    $myMail->AddBcc(trim($matches[2]), trim($matches[1]));
                    continue;
                }
                if (preg_match("/^cc\:(.*?)\<(.*?)\>$/i",trim($recip),$matches)) {  //cc whith name
                    $myMail->AddCc(trim($matches[2]), trim($matches[1]));
                    continue;
                }
                if (preg_match("/^(.*?)\<(.*?)\>$/i",trim($recip),$matches)) {      // address whith name
                    $myMail->AddAddress(trim($matches[2]), trim($matches[1]));
                    continue;
                }
                if (strpos( $recip, "BCC:")===0)   {$myMail->AddBcc(trim(substr($recip, 4)));}  // BCC:
                elseif (strpos($recip, "CC:")===0) {$myMail->AddCc(trim(substr($recip, 3)));}   // CC:
                else                               {$myMail->AddAddress(trim($recip)); }        // TO:
            }
        }

        // define information to send out
        $myMail->Subject = $subject;                    // SUBJECT
        $myMail->Body = $htmlmessage;                   // CONTENT (HTML)
        $myMail->AltBody = $plaintext;                  // CONTENT (PLAINTEXT)

        if (is_array($file_attached)) {
            foreach($file_attached as $k => $v) {
                $myMail->AddAttachment($k, $v);         // ATTACHMENT (FILE)
            }
        }

        // check if there are any send mail errors, otherwise say successful
        if (!$myMail->Send()) {
            return false;
        } else {
            return true;
        }
    }
}

////////////////// Main function ///////////////////////

if (!function_exists('eval_form')) {
    function eval_form($section_id) {
        global $database, $MESSAGE, $admin, $TEXT, $LANG;

    /*      // FTAN is not yet implemented for frontend part of modules in WB
        if ((WB_VERSION >= "2.8.2") && (!$admin->checkFTAN()))
        {
            $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], WB_URL);
            exit();
        } */

        (preg_match("/^\d+\.\d+\.\d+\.\d+$/", $_SERVER['REMOTE_ADDR'])) ? $ip = $_SERVER['REMOTE_ADDR'] : $ip = 'unknown';  // IP address of sender

        // obtain the settings of the output filter module
        if (file_exists(WB_PATH.'/modules/output_filter/filter-routines.php')) {
            if(!function_exists('executeFrontendOutputFilter')) {
                include(WB_PATH.'/modules/output_filter/filter-routines.php');
            }
            if (function_exists('get_output_filter_settings')) {
                $filter_settings = get_output_filter_settings();
            } elseif (function_exists("getOutputFilterSettings")) {
                $filter_settings = getOutputFilterSettings();
            }else {
                $filter_settings['email_filter'] = 0;
            }
        } else {
            // no output filter used, define default settings
            $filter_settings['email_filter'] = 0;
        }


        $files_to_attach = array();
        $upload_filename = '';

        // Check that submission ID matches
        if (!isset($_SESSION['submission_id_'.$section_id])
            OR !isset($_POST['submission_id'])
            OR $_SESSION['submission_id_'.$section_id] != $_POST['submission_id']) {
                include_once(WB_PATH .'/modules/mpform/paintform.php');
                paint_form($section_id);
                return;
        }

        if(ENABLED_ASP && ( // form faked? Check the honeypot-fields.
            (!isset($_POST['submitted_when'.$section_id]) OR !isset($_SESSION['submitted_when'.$section_id])) OR 
            ($_POST['submitted_when'.$section_id] != $_SESSION['submitted_when'.$section_id]) OR
            (!isset($_POST['email'])    OR $_POST['email'])    OR
            (!isset($_POST['homepage']) OR $_POST['homepage']) OR
            (!isset($_POST['comment'])  OR $_POST['comment'])  OR
            (!isset($_POST['url'])      OR $_POST['url'])
        )) {
            exit(header("Location: ".WB_URL.PAGES_DIRECTORY.""));
        }

        // Get form settings
        $query_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_settings WHERE section_id = '$section_id'");
        if($query_settings->numRows() > 0) {
            $fetch_settings = $query_settings->fetchRow();

            $email_from = $fetch_settings['email_from'];
            if(substr($email_from, 0, 5) == 'field') {
                // Set the email from field to what the user entered in the specified field
                $email_from = htmlspecialchars($admin->add_slashes($_POST[$email_from]));  
            }
            if ($email_from == 'wbu') {
                $email_from = $admin->get_email();
            }

            $email_replyto = $fetch_settings['email_replyto'];
            if(substr($email_replyto, 0, 5) == 'field') {
                // Set the email replyto field to what the user entered in the specified field
                $email_replyto = htmlspecialchars($admin->add_slashes($_POST[$email_replyto]));  
            }
            if ($email_replyto == 'wbu') {
                $email_replyto = $admin->get_email();
            }

            $email_fromname = $fetch_settings['email_fromname'];
            if(substr($email_fromname, 0, 5) == 'field') {
                // Set the email from field to what the user entered in the specified field
                $email_fromname = htmlspecialchars($admin->get_post_escaped($email_fromname), ENT_QUOTES);
            }
            if ($email_fromname == 'wbu') {
                $email_fromname = $admin->get_display_name();
            }

            $success_email_to = $fetch_settings['success_email_to'];
            if(substr($success_email_to, 0, 5) == 'field') {
                // Set the success_email to field to what the user entered in the specified field
                $success_email_to = htmlspecialchars($admin->add_slashes($_POST[$success_email_to]));
            }
            if ($success_email_to == 'wbu') {
                $success_email_to = $admin->get_email();
            }

            $email_subject =          $fetch_settings['email_subject'];
            $email_text =             $fetch_settings['email_text'];
            $success_page =           $fetch_settings['success_page'];
            $success_text =           $fetch_settings['success_text'];
            $submissions_text =       $fetch_settings['submissions_text'];
            $success_email_from =     $fetch_settings['success_email_from'];
            $success_email_fromname = $fetch_settings['success_email_fromname'];
            $success_email_text =     $fetch_settings['success_email_text'];
            $success_email_subject =  $fetch_settings['success_email_subject'];        
            $max_submissions =        $fetch_settings['max_submissions'];
            $stored_submissions =     $fetch_settings['stored_submissions'];
            $use_captcha =            $fetch_settings['use_captcha'];
            $upload_files_folder =    $fetch_settings['upload_files_folder'];
            $attach_file =            $fetch_settings['attach_file'];
            $upload_only_exts =       $fetch_settings['upload_only_exts'];
            $upload_file_mask =       $fetch_settings['upload_file_mask'];
            $max_file_size =          $fetch_settings['max_file_size_kb'] * 1024;
            $_POST['MAX_FILE_SIZE'] = $max_file_size; // stupid enough, PEAR checks this POST variable for maximum size!
            $suffix =                 $fetch_settings['tbl_suffix'];
            $email_to =               $fetch_settings['email_to'];

            // settings for html output of form input:
            $heading_html =       $fetch_settings['heading_html'];
            $short_html =         $fetch_settings['short_html'];
            $long_html =          $fetch_settings['long_html'];
            $email_html =         $fetch_settings['email_html'];
            $uploadfile_html =    $fetch_settings['uploadfile_html'];
        } else {
            exit($TEXT['UNDER_CONSTRUCTION']);
        }

        // get authenticated user data
        if(isset($admin) AND $admin->is_authenticated() AND $admin->get_user_id() > 0) {
            $submitted_by = $admin->get_user_id();
            $wb_user = $admin->get_display_name();
            $wb_email = $admin->get_email();
        } else {
            $submitted_by = 0;
            $wb_user = '';
            $wb_email = '';
        }

        //$email_body = '';
        $fer = array();
        $err_txt = array();
        $html_data_user = '';
        $html_data_site = '';

        $format = DEFAULT_DATE_FORMAT. " " .DEFAULT_TIME_FORMAT;
        $jetzt = date($format);

        // Captcha
        if($use_captcha) {
            if(isset($_POST['captcha']) AND $_POST['captcha'] != ''){
                // Check for a mismatch patch from http://www.websitebaker2.org/forum/index.php/topic,23986.msg167490.html#msg167490
                if((!isset($_SESSION['captcha'.$section_id]) OR $_POST['captcha'] != $_SESSION['captcha'.$section_id]) && (!isset($_SESSION['captcha']) OR $_POST['captcha'] != $_SESSION['captcha'])) {
                    $err_txt['captcha'.$section_id] = $LANG['frontend']['INCORRECT_CAPTCHA'];
                    $fer[] = 'captcha'.$section_id;
                }
            } else {
                $err_txt['captcha'.$section_id] = $LANG['frontend']['INCORRECT_CAPTCHA'];
                $fer[] = 'captcha'.$section_id;
            }
        }
        if(isset($_SESSION['captcha'.$section_id])) { unset($_SESSION['captcha'.$section_id]); }

        // Create blank "required" array
        $felder = "";    // for results table
        $mailto = "";

        // Get list of fields
        $query_fields = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_fields WHERE section_id = '$section_id' ORDER BY position ASC");
        if($query_fields->numRows() > 0) {
            while($field = $query_fields->fetchRow()) {
                // Loop through fields and add to message body
                $field_id = $field['field_id'];
                $feld = '';
                if($field['type'] != '') {
                    if ((!empty($_POST['field'.$field_id]))
                    or  ($admin->get_post('field'.$field_id) == "0")) { // added Apr 2009
                        $post_field = $_POST['field'.$field_id];

                        // copy user entered data to $_SESSION in case form must be reviewed (for instance because of missing required values)
                        if (is_array($post_field)) {
                            $_SESSION['mpf']['field'.$field_id] = str_replace(array("[[", "]]"), array("&#91;&#91;", "&#93;&#93;"), $post_field);
                        } else {
                            // make sure user does see what he entered:
                            $_SESSION['mpf']['field'.$field_id] = str_replace(array("[[", "]]"), array("&#91;&#91;", "&#93;&#93;"), htmlspecialchars(stripslashes($post_field), ENT_QUOTES));
                        }

                        // no injections, please
                        if (!is_array($post_field)) {
                            $field_value = str_replace(array("[[", "]]"), array("&#91;&#91;", "&#93;&#93;"), htmlspecialchars($admin->get_post_escaped('field'.$field_id), ENT_QUOTES));
                        }

                        // if the output filter is active, we need to revert (dot) to . and (at) to @ (using current filter settings)
                        // otherwise the entered mail will not be accepted and the recipient would see (dot), (at) etc.
                        if ($filter_settings['email_filter']) {
                            $field_value = $post_field;
                            $field_value = str_replace($filter_settings['at_replacement'], '@', $field_value);
                            $field_value = str_replace($filter_settings['dot_replacement'], '.', $field_value);
                            $post_field = $field_value;
                        }
                        if($field['type'] == 'email' AND $admin->validate_email($post_field) == false) {
                            $err_txt[$field_id] = $MESSAGE['USERS']['INVALID_EMAIL'];
                            $fer[] = $field_id;
                        }

                        // check invalid user input
                        if($field['type'] == 'integer_number') {
                            $v = $post_field;
                            if (!preg_match("/^[0-9]+$/", $v)) {  // only allow valid chars
                                $err_txt[$field_id] = $LANG['frontend']['integer_error'];
                                $fer[]=$field_id;
                            }
                        }
                        if ($field['type'] == 'decimal_number') {
                            $v = $post_field;
                            if (!preg_match("/^(\+|\-)?[0-9]+(\,|\.)?[0-9]*$/", $v)) {  // only allow valid chars
                                $err_txt[$field_id] = $LANG['frontend']['decimal_error'];
                                $fer[]=$field_id;
                            }
                        }

                        if ($field['type'] == 'heading') {
                            //$email_body .= $field_value."\n";
                            $html_data_user .= str_replace('{HEADING}', $field['title'], $heading_html);
                            $html_data_site .= str_replace('{HEADING}', $field['title'], $heading_html);
                        } elseif ($field['type'] == 'email_recip') {
                            // the browser will convert umlauts, we need to undo this for compare:
                            $recip = htmlentities  ($post_field[0], ENT_NOQUOTES, 'UTF-8');
                            if ($recip == $LANG['frontend']['select']) {
                                $err_txt[$field_id] = $LANG['frontend']['select_recip'];
                                $fer[]=$field_id;
                            }
                            $recip = htmlspecialchars($post_field[0], ENT_QUOTES);
                            //$email_body .= $field['title'].': '.$recip."\n";
                            $html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $recip), $short_html);
                            $html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $recip), $short_html);
                            if ($mailto == "") { 
                                $mailto = $recip;
                            }

                        } elseif ($field['type'] == 'email_subj') {
                            $email_subject .= " ". $field_value;
                            $success_email_subject .= " ". $field_value;
                            $html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $short_html);
                            $html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $short_html);

                        } elseif (!is_array($post_field)) {
                            if ($field['type'] == 'email') {
                                $html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $email_html);
                                $html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $email_html);
                            } elseif ($field['type'] == 'textarea') {
                                //$zeilen = str_replace("\n", "<br />", $field_value);  // v 1.0
                                $zeilen = str_replace("\r\n", "<br />", $field_value);  // Test doppelte LF
                                $html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $zeilen), $long_html);
                                $html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $zeilen), $long_html);
                            } else {
                                $html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $short_html);
                                $html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $short_html);
                            }
                            $feld = "'" . mpform_escape_string(htmlspecialchars($post_field)) . "'";
                       } else {
                            $feld = "'";
                            $zeilen = '';
                            foreach ($post_field as $k => $v) {
                                $field_value = htmlspecialchars($admin->add_slashes($v), ENT_QUOTES);
                                $feld .= mpform_escape_string($field_value) . ", ";
                                $zeilen .= mpform_escape_string($field_value) . "<br />";
                            }
                            $feld = substr($feld, 0, -2);
                            $feld .= "'";
                            $html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $zeilen), $long_html);
                            $html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $zeilen), $long_html);
                        }
                    } elseif($field['type'] == 'filename') {
                        $err_txt[$field_id] = "";
                        // locally we use a copy of max_file_size 
                        $tmp_max_file_size = $max_file_size;
                        $file_counter=0;
                        if (count($_FILES['field'.$field_id]['name'])){
                            foreach($_FILES['field'.$field_id]['name'] as $f => $name) { 
                                if($name != ""){
                                    if($tmp_max_file_size<=0){
                                        $err_txt[$field_id] .= sprintf($LANG['frontend']['err_upload'], $name, sprintf($LANG['frontend']['err_too_large2'], 0));
                                        $fer[]=$field_id;
                                    } else {
                                        $filename = preg_replace("/[^0-9a-zA-Z_\-\.]/", "", basename($name)); // only allow valid chars in filename
                                        $file_counter++;
                                        // prevent from upload of millions of empty files 
                                        if($file_counter>128) break;
                                        $newfilename = date('YmdHis') . "-" . rand(10000, 99999). "-" . $filename;
                                        $uploadfailed = mpform_upload_one_file('field'.$field_id, $f, WB_PATH.$upload_files_folder, $newfilename, $upload_only_exts, $upload_file_mask, $tmp_max_file_size);
                                        if ($uploadfailed) {
                                            $err_txt[$field_id] .= sprintf($LANG['frontend']['err_upload'], $filename, $uploadfailed);
                                            $fer[]=$field_id;
                                        } else {
                                            $upload_filename = $upload_files_folder . "/". $newfilename;      // for results table only
                                            $file_url = WB_URL . $upload_files_folder . "/" . $newfilename; // for links in email to admin and backend

                                            if ($attach_file == 1) {
                                                $files_to_attach[WB_PATH. $upload_files_folder. "/". $newfilename] = $filename;
                                            }
                                            $feld = "'" . $upload_filename . "'";
                                            $fs = filesize(WB_PATH.$upload_files_folder."/".$newfilename);
                                            // reduce maximum by already consumed space
                                            $tmp_max_file_size -= $fs;
                                            // convert to human readable string for displaying
                                            $fs = sprintf("%.1f", $fs / 1024);  // file size in KB

                                            $html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], "$filename ($fs KB)"), $short_html);
                                            $html_data_site .= str_replace(array('{TITLE}', '{DATA}', '{SIZE}'), array($field['title'], $file_url, $fs), $uploadfile_html);
                                        }
                                    }
                                }
                            }
                        } elseif ($field['required']==1) {
                            $fer[]=$field_id;
                        }
                        // assumption: $_FILES is in the same order as filenames in $[field]
                        // then, we can shift it so that the next time pear's upload class can
                        // handle the next field. Otherwise we always stick to the first record
                        array_shift($_FILES);
                    } elseif ($field['type'] == 'fieldset_start') {
                        $html_data_user .= "<fieldset><legend>". $field['title'] ."</legend>\n";
                        $html_data_site .= "<fieldset><legend>". $field['title'] ."</legend>\n";
                    } elseif ($field['type'] == 'fieldset_end') {
                        $html_data_user .= "</fieldset>\n";
                        $html_data_site .= "</fieldset>\n";
                    } elseif ($field['type'] == 'html') {
                        $html_data_user .= htmlspecialchars_decode($field['value']) . "<br />\n";
                        $html_data_site .= htmlspecialchars_decode($field['value']) . "<br />\n";
                    } elseif($field['required'] == 1) {
                        $fer[]=$field_id;
                    }
                } 
                if ($feld == '') {
                    $feld = "''";
                    if($field['type'] == 'integer_number') $feld = '0';
                    if ($field['type'] == 'decimal_number') $feld = '0.0';
                }
                if (strlen($felder) > 0) {
                    $felder .= ", ";
                }
                $felder .= "field" . $field_id . " = " . $feld . " ";
            } // end of field loop
        }

        // sanitize against any javascript attempts
        $aTags = array( 'script', 'body', 'head', 'html', 'link');
        foreach($aTags as $tag){
            $html_data_user = preg_replace('/<\/?'.$tag.'[^<>]*>/i',"",$html_data_user);
            $html_data_site = preg_replace('/<\/?'.$tag.'[^<>]*>/i',"",$html_data_site);
        }
        // Check if the user forgot to enter values into all the required fields
        if($fer != array()) {
            // paint form again:
            include_once(WB_PATH .'/modules/mpform/paintform.php');
            paint_form($section_id, $fer, $err_txt, false);
        } else {
            // Check how many times form has been submitted in last hour
            $last_hour = time()-3600;
            $query_submissions = $database->query("SELECT submission_id FROM ".TABLE_PREFIX."mod_mpform_submissions WHERE submitted_when >= '$last_hour'");
            if($query_submissions->numRows() > $max_submissions) {
                // Too many submissions so far this hour
                echo $MESSAGE['MOD_FORM']['EXCESS_SUBMISSIONS']. " ";
                $success = false;
            } else {

                // execute private function in private.php, if available
                if (function_exists('private_function_before_email')) {
                    $success = private_function_before_email($section_id);
                } else $success = true;

                // Now send the email + attachment...
                if($success==true AND $email_to != '') {  // $email_to is set in the backend, might consist of lists of addresses
                    $body = str_replace(array('{DATA}', '{REFERER}', '{IP}', '{DATE}', '{USER}'), array($html_data_site, $_SESSION['href'], $ip, $jetzt, $wb_user), $email_text);
                    $q = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_fields WHERE section_id = '$section_id' and type = 'email_recip' LIMIT 1");
                    if ($q->numRows() > 0 and $mailto != "") {  // $mailto contains recipient as selected by user
                        // recipient selectet by user: 
                        // different linebreaks
                        $arrtorep= array("\r\n","\n\r","\r");
                        $email_to = str_replace($arrtorep, "\n", $email_to);
                        $emails = preg_split('/\n/', $email_to);
                        foreach ($emails as $recip) {
                            if (strpos($recip, $mailto) === 0) {
                                $mailto .= $recip.",";
                            }
                        }
                    }
                    if ($mailto =="") {
                        // take all recipients from the list:

                        $arrtorep= array("\r\n","\n\r","\r");
                        $email_to = str_replace($arrtorep, "\n", $email_to);
                        $emails = preg_split('/\n/', $email_to);
                        foreach ($emails as $recip) {  
                            if ($recip != '') {
                                $mailto .= $recip.",";
                            }
                        }
                    }
                    //echo $mailto;


                    if(mpform_mailx($email_from, $email_replyto, $mailto, $email_subject, $body, $email_fromname, $files_to_attach)) {
                        $files_to_attach = array();
                    } else {
                        $success = false;
                        echo $TEXT['WBMAILER_FUNCTION']." (SITE) <br />\n";
                    }
                }

                // execute private function in private.php, if available
                if ($success==true AND function_exists('private_function_after_email')) {
                    $success = private_function_after_email($section_id);
                } 

                if ($success==true AND $success_email_to != '') {
                    $user_body = str_replace(array('{DATA}', '{REFERER}', '{IP}', '{DATE}', '{USER}'), array($html_data_user, $_SESSION['href'], $ip, $jetzt, $wb_user), $success_email_text);
                    if (! mpform_mailx($success_email_from, '', $success_email_to, $success_email_subject, $user_body, $success_email_fromname)) {
                        $success = false;
                        echo $TEXT['WBMAILER_FUNCTION']. " (CONFIRM) ";
                    }
                }

                if ($success==true) {
                    // Write submission to database    
                    $us = $_SESSION['submission_id_'.$section_id];
                    $started_when = $_SESSION['submitted_when'.$section_id];
                    $body = str_replace(array('{DATA}', '{REFERER}', '{IP}', '{DATE}', '{USER}'), array($html_data_site, $_SESSION['href'], $ip, $jetzt, $wb_user), $submissions_text);
                    $body = mpform_escape_string($body);
                    $database->query("INSERT INTO ".TABLE_PREFIX."mod_mpform_submissions
                            (page_id, section_id, submitted_when, submitted_by, upload_filename, ip_addr, body, started_when, session_id)
                            VALUES ('".PAGE_ID."', '$section_id', '".time()."', '$submitted_by', '$upload_filename', '$ip', '$body', '$started_when', '$us')");
                    if($database->is_error()) {
                        $success = false;
                        echo $TEXT['DATABASE']. " ";
                    } else {
                        // Make sure submissions table isn't too full
                        $query_submissions = $database->query("SELECT submission_id FROM ".TABLE_PREFIX."mod_mpform_submissions ORDER BY submitted_when");
                        $num_submissions = $query_submissions->numRows();
                        if($num_submissions > $stored_submissions) {
                            // Remove excess submission
                            $num_to_remove = $num_submissions-$stored_submissions;
                            while($submission = $query_submissions->fetchRow()) {
                                if($num_to_remove > 0) {
                                    $submission_id = $submission['submission_id'];
                                    $database->query("DELETE FROM ".TABLE_PREFIX."mod_mpform_submissions WHERE submission_id = '$submission_id'");
                                    if($database->is_error()) {
                                        $success = false;
                                        echo $TEXT['DATABASE']. " ";
                                    }
                                    $num_to_remove -= 1;
                                }
                            }
                        }

                        $query_submitted = $database->query("SELECT session_id FROM ".TABLE_PREFIX."mod_mpform_results_$suffix WHERE session_id = '$us'");
                        $num_submitted = $query_submitted->numRows();
                        if ($felder != "") $felder .= ", ";
                        $felder .= "submitted_when = '". time() ."'";
                        $lf = array("\r\n", "\n", "\r");
                        if ($num_submitted == 0) {   // new session:
                            $qs = "INSERT INTO ".TABLE_PREFIX."mod_mpform_results_$suffix SET "
                                . "`session_id` = '$us', "
                                . "`started_when` = '$started_when', "
                                . "`referer` = '". $_SESSION['href'] ."', "
                                . str_replace($lf, " ", $felder) ;
                        } else {
                            $qs = "UPDATE ".TABLE_PREFIX."mod_mpform_results_$suffix SET "
                                . str_replace($lf, " ", $felder) 
                                . " WHERE session_id = '$us' LIMIT 1";
                        }
                        $database->query($qs);
                        if($database->is_error()) {
                            echo $TEXT['DATABASE']. " " . $qs."<br />". $database->get_error();
                            $success = false;
                        }

                        // Make sure results table isn't too full
                        $qs = $database->query("SELECT session_id FROM ".TABLE_PREFIX."mod_mpform_results_$suffix ORDER BY submitted_when");
                        $num_submissions = $qs->numRows();
                        if($num_submissions > $stored_submissions) {
                            // Remove excess submission
                            $num_to_remove = $num_submissions-$stored_submissions;
                            while($submission = $query_submissions->fetchRow()) {
                                if($num_to_remove > 0) {
                                    $submission_id = $submission['session_id'];
                                    $database->query("DELETE FROM ".TABLE_PREFIX."mod_mpform_results_$suffix WHERE session_id = '$submission_id'");
                                    if($database->is_error()) {
                                        $success = false;
                                        echo $TEXT['DATABASE']. " ";
                                    }
                                    $num_to_remove -= 1;
                                }
                            }
                        }
                    }
                }
            }    
        }

        // Now check if the email was sent successfully
        if (isset($success) AND $success == true) {
            if (isset($_SESSION['captcha_time'])) unset($_SESSION['captcha_time']);    // can't do this in captcha module when multiple forms on one page!

            // execute private function in private.php, if available
            if (function_exists('private_function_on_success')) {
                $success = private_function_on_success($section_id);
            }

            if ($success == true) {
                if ($success_page=='none') {
                    echo str_replace(array('{DATA}', '{REFERER}', '{IP}', '{DATE}', '{USER}'), array($html_data_user, $_SESSION['href'], $ip, $jetzt, $wb_user), $success_text);
                    // delete the referer page reference after it did its work:
                    unset($_SESSION['href']);
                } else {
                    $query_menu = $database->query("SELECT link,target FROM ".TABLE_PREFIX."pages WHERE `page_id` = '$success_page'");
                    if ($query_menu->numRows() > 0) {
                        $fetch_settings = $query_menu->fetchRow();
                       $link = WB_URL.PAGES_DIRECTORY.$fetch_settings['link'].PAGE_EXTENSION;
                       echo "<script type='text/javascript'>location.href='".$link."';</script>";
                    }
                }
            }

            // delete the referer page reference after it did its work:
            unset($_SESSION['href']);
            unset($success);
            if (isset($_SESSION['mpf'])) {
              unset ($_SESSION['mpf']);
            }
        } else {
            if (isset($success) AND $success == false) {
                echo $TEXT['ERROR'];
                unset($success);
            }
        }
    }
}

