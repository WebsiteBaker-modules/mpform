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
/* This file paints the form in the frontend. */
// Must include code to stop this file being access directly
if (!defined('WB_PATH')){
    exit("Cannot access this file directly"); 
}

require_once(dirname(__FILE__).'/constants.php');

// Function for generating an options for a select field
if (!function_exists('make_option')) {
    function make_option(
        &$option, 
        $idx, 
        &$mpform_code, 
        $values, 
        $isnew, 
        $value_option_separator
    ) {
        $def = strpos($option, MPFORM_IS_DEFAULT);
        ($def > 0) ? $h = substr($option, 0, $def) : $h = $option;
        $vals=explode($value_option_separator,$h);
        if(count($vals)==1) $vals[1]=$vals[0];
        // start option group if it exists
        if (substr($option, 0, 2) == '[=') {
            $option = '<optgroup label="'.substr($option,2,strlen($option)).'">';
        } elseif ($option == ']') {
            $option = '</optgroup>';
        } else {
            if (in_array($h, $values) or ($isnew and $def > 0)) {
                $option = '<option selected="selected" value="'.$vals[0].'">'.$vals[1].'</option>';
            } else {
                $option = '<option value="'.$vals[0].'">'.$vals[1].'</option>';
            }
        }
    }
}

// Function for generating a checkbox
if (!function_exists('make_checkbox')) {
    function make_checkbox(
        &$option, 
        $idx, 
        &$mpform_code,
        $field_id, 
        $seperator, 
        $value, 
        $sErrClass, 
        $isnew, 
        $value_option_separator
    ) {
        $def = strpos($option, MPFORM_IS_DEFAULT);
        ($def > 0) ? $h = substr($option, 0, $def) : $h = $option;
        $vals=explode($value_option_separator,$h);
        if ($mpform_code=="") {
            $v = $vals[0];
        } else {
            if(count($vals)==1){
            $v = $mpform_code;
            $mpform_code = chr(ord($mpform_code)+1);
            } else $v = $vals[0];
        }
        if(count($vals)==1) $vals[1]=$vals[0];
        $label_i = urlencode($option) . $field_id;
        $bad = array("%", "+");
        $label_id = 'wb_'.str_replace($bad, "", $label_i);
        if (in_array($v, $value) or ($isnew and $def > 0)) {
            $option = '<input '
               . ' class="'.$sErrClass.'checkbox"'
               . ' type="checkbox"'
               . ' id="'.$label_id.'"'
               . ' name="field'.$field_id.'['.$idx.']"'
               . ' value="'.$v.'"'
               . ' checked="checked" />'
               . '<label for="'.$label_id.'"'
               . 'class="'.$sErrClass.'checkbox_label">'
               . $vals[1]
               . '</label>'.$seperator.PHP_EOL; 
        } else {
            $option = '<input '
            . ' class="'.$sErrClass.'checkbox"'
            . ' type="checkbox"'
            . ' id="'.$label_id.'"'
            . ' name="field'.$field_id.'['.$idx.']"'
            . ' value="'.$v.'" />'
            . '<label for="'.$label_id.'"'
            . ' class="'.$sErrClass.'checkbox_label">'
            . $vals[1]
            . '</label>'.$seperator.PHP_EOL; 
        }    
    }
}

// Function for generating a radio button
if (!function_exists('make_radio')) {
    function make_radio(
        &$option, 
        $idx, 
        &$mpform_code, 
        $field_id, 
        $seperator, 
        $value, 
        $sErrClass, 
        $isnew, 
        $value_option_separator
    ) {
        $def = strpos($option, MPFORM_IS_DEFAULT);
        ($def > 0) ? $h = substr($option, 0, $def) : $h = $option;
        $vals=explode($value_option_separator,$h);
        if ($mpform_code=="") {
            $v = $vals[0];
        } else {
            if(count($vals)==1){
                $v = $mpform_code;
                $mpform_code = chr(ord($mpform_code)+1);
            } else $v = $vals[0];
        }
        if(count($vals)==1) $vals[1]=$vals[0];
        $label_i = urlencode($option) . $field_id;
        $bad = array("%", "+");
        $label_id = 'wb_'.str_replace($bad, "", $label_i);
        if (($v == $value) or ($isnew and $def > 0)) {
            $option = '<input'
            . ' class="'.$sErrClass.'radio"'
            . ' type="radio"'
            . ' id="'.$label_id.'"'
            . ' name="field'.$field_id.'"'
            . ' value="'.$v.'"'
            . ' checked="checked" />'
            . '<label'
            . ' for="'.$label_id.'"'
            . ' class="'.$sErrClass.'radio_label">'
            . $vals[1]
            . '</label>'.$seperator.PHP_EOL; 
        } else {
            $option = '<input'
            . ' class="'.$sErrClass.'radio"'
            . ' type="radio"'
            . ' id="'.$label_id.'"'
            . ' name="field'.$field_id.'"'
            . ' value="'.$v.'" />'
            . '<label'
            . ' for="'.$label_id.'" '
            . 'class="'.$sErrClass.'radio_label">'
            .  $vals[1]
            . '</label>'.$seperator.PHP_EOL; 
        }
    }
}

// Generate temp submission id
if (!function_exists('new_submission_id')) {
    function new_submission_id() {
        $sSubmissionID = '';
        $sSalt = "abchefghjkmnpqrstuvwxyz0123456789";
        srand((double)microtime()*1000000);
        $i = 0;
        while ($i <= 7) {
            $num = rand() % 33;
        $sSubmissionID .= substr($sSalt, $num, 1);
            $i++;
        }
        return $sSubmissionID;
    }
}

if (!function_exists('js_for_help')) {
    function js_for_help() {
        echo '<script language="javascript" type="text/javascript">';
        echo "//<![CDATA[\n";
        echo "    var theRowOpened = -2;\n";
        echo "    var theTableOpened = -2;\n";
        echo '    var MPFORM_CLASS_PREFIX = "'.MPFORM_CLASS_PREFIX.'";'."\n";
        echo "//]]>\n";
        echo "</script>\n";    
    }
}

// from html blocks remove special comments
if (!function_exists('remove_comments')) {
    function remove_comments($content) {
        $pattern = '/(?:<!--\/\*.*?\*\/-->)/si';
        while(preg_match($pattern,$content,$matches)==1){
            $toremove=$matches[0];
            $content = str_replace($toremove, '', $content);
        }
        return($content);
    }
}

////////////////// Main function ///////////////////////

if (!function_exists('paint_form')) {
    function paint_form( $iSID /*section_id*/, $aMissing = array(), 
                         $aErrTxt = array(), $isnew = true) {
        global $database, $MENU, $TEXT, $LANG, $admin;
        $mpform_code="";

        if($aMissing != array()) {
            if(!isset($LANG['frontend']['REQUIRED_FIELDS'])) {
                $msg = 'Please complete or correct the fields in red color!';
            } else {
                $msg = $LANG['frontend']['REQUIRED_FIELDS'];
            }
            echo '<div class="'.MPFORM_CLASS_PREFIX.'missing">'.$msg.'</div>';
        }

        // Get settings
        $query_settings 
            = $database->query(
                "SELECT *"
                    . " FROM ".TP_MPFORM."settings"
                    . " WHERE section_id = '$iSID'"
            );
        if($query_settings->numRows() > 0) {
            $aSettings              = $query_settings->fetchRow();
            $header                 = str_replace('{WB_URL}', WB_URL, $aSettings['header']);
            $field_loop             = $aSettings['field_loop'];
            $footer                 = str_replace(array('{WB_URL}', '{SUBMIT}', '{SUBMIT_TEXT}'), 
                                                      array(WB_URL, MPFORM_SUBMIT_BUTTON, $LANG['backend']['TXT_SUBMIT']), 
                                                  $aSettings['footer']);
            $use_captcha            = $aSettings['use_captcha'];
            $is_following           = $aSettings['is_following'];
            $value_option_separator = $aSettings['value_option_separator'];
            if($value_option_separator=="") $value_option_separator=MPFORM_DEFAULT_OPT_SEPARATOR; // fallback
            $max_file_size          = $aSettings['max_file_size_kb'] * 1024;
            $date_format            = $aSettings['date_format'];
            $email_to               = $aSettings['email_to'];
            $enum_start             = $aSettings['enum_start'];
            $success_page           = $aSettings['success_page'];
            $upload_only_exts       = $aSettings['upload_only_exts'];
        } else {
            exit($TEXT['UNDER_CONSTRUCTION']);
        }

        $bNeedHelpButton = (strpos($field_loop, "{HELP}") !== false);  // we only need a help button if this variable is used
        // execute private function in private.php, if available
        if (function_exists('private_function_before_new_form')) {
           private_function_before_new_form($iSID);
        }

        // Set new submission ID in session if it is not a follower on a multipage form
        if ($is_following) {
            if (!isset($_SESSION['following_page_'.PAGE_ID])
                || ($_SESSION['following_page_'.PAGE_ID]!=$_SESSION['submission_id_'.$iSID])) {
                $sUrlToGo = WB_URL.PAGES_DIRECTORY;
                if(headers_sent())
                  $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'],$sUrlToGo);
                else 
                  header("Location: ". $sUrlToGo);
                exit(0);
            }
        } else {
            $new_SID = new_submission_id();
            $_SESSION['submission_id_'.$iSID] = $new_SID;
        }
        
        
        if ($success_page != 'none') {
            $qs 
                = $database->query(
                    "SELECT *"
                        . " FROM ".TABLE_PREFIX."sections"
                        . " WHERE page_id = '$success_page'"
                        . " AND module = 'mpform'"
                );
            if($qs->numRows() > 0) {
                $s = $qs->fetchRow();
                $sid = $s['section_id'];
                $new_SID = substr($_SESSION['submission_id_'.$iSID], 0, 8);
                $_SESSION['submission_id_'.$sid] = $new_SID;
                //$_SESSION['submission_id'] = $new_SID;                
            }
        }

        // remember the referer page:
        if (isset($_SERVER['HTTP_REFERER'])) {
            if (!isset($_SESSION['href'])) 
                $_SESSION['href'] 
                    = addslashes(htmlspecialchars(
                        $_SERVER['HTTP_REFERER'], 
                        ENT_QUOTES)
                    );
        } else {
            $_SESSION['href'] = 'unknown';
        }

        $jscal_today = '';
        // Do i need to include calendar files ?
        $query_fields
            = $database->query(
                "SELECT *"
                    . " FROM ".TP_MPFORM."fields"
                    . " WHERE section_id = '$iSID'"
                    . " AND type = 'date'"
            );
        if($query_fields->numRows() > 0) {
            // include jscalendar-setup
            $jscal_use_time = true; // whether to use a clock, too
            require_once(dirname(__FILE__) . "/jscalendar.php");
            // - first-day-of-week (0-sunday, 1-monday, ...) 
            // (default: 0(EN) or 1(everything else))
            $jscal_firstday = "1"; 
            //"%Y-%m-%d"; // - format for jscalendar 
            // (default: from wb-backend-date-format)
            if ($date_format) $jscal_ifformat = $date_format; 
        }

        $sActionAttr = htmlspecialchars(strip_tags($_SERVER['SCRIPT_NAME']));
        $sValueAttr  = $_SESSION['submission_id_'.$iSID];

        if(defined('MPFORM_DIV_WRAPPER')){
            echo PHP_EOL.'<div class="'.MPFORM_DIV_WRAPPER.'">'.PHP_EOL;
        }

        echo '<form name="form_'.$iSID.'"  enctype="multipart/form-data" action="'
            . $sActionAttr .'#wb_section_'.$iSID.'" method="post">'.PHP_EOL;
        echo '<input type="hidden" name="submission_id" value="'. $sValueAttr .'" />'.PHP_EOL; 

        if(ENABLED_ASP) {
           echo draw_asp_honeypots($iSID);
        }
        // Print header
        echo $header;
        $first_MAX = true;

        // Get list of fields
        $query_fields 
            = $database->query(
                "SELECT *"
                     . " FROM ".TP_MPFORM."fields"
                     . " WHERE section_id = '$iSID'"
                     . " ORDER BY position ASC"
            );
        $bFileSizeHintShown=false;
        $cla= array();
        $bLoadHelpJS = false;
        $bTableLayout = (stripos($header, "<table") !== false);  
        if($query_fields->numRows() > 0) {
            // Loop through fields
            while($field = $query_fields->fetchRow()) {
                // Set field values
                $iFID = $field['field_id'];
                $value = $field['value'];
                $extraclasses = $field['extraclasses'];
                if(! (preg_match('/{FORMATTED_FIELD}/',$aSettings['field_loop']) ||
                   ( preg_match('/{TEMPLATE/',$aSettings['field_loop'])
                   && preg_match('/{FORMATTED_FIELD}/',$field['template'])) )) 
                        $extraclasses = '';
                if($extraclasses!='') $extraclasses.=' ';
                $classes = 'fid'.$iFID.' '.MPFORM_CLASS_PREFIX. $field['type'];
                $field_classes = $extraclasses
                               .MPFORM_CLASS_PREFIX.'field_'.$iFID.' '
                               .MPFORM_CLASS_PREFIX.'field_'.$field['type'];
                //echo $field['extra'];

                if ($field['extra'] == '') {
                    $maxlength = '';
                } else {
                    $maxlength = ' maxlength="'.$field['extra'].'" '; 
                }

                $sErrClass = MPFORM_CLASS_PREFIX;
                if (in_array($iFID, $aMissing)) {
                    $sErrClass = MPFORM_CLASS_PREFIX.'err_';
                    $classes .= ' '.MPFORM_CLASS_PREFIX.'error';
                }

                // skip disabled fields
                if (($field['required'] & 4) != 0) continue;

                // skip conditional blocks as well (they are not ready yet...)
                if ($field['type'] == 'conditional') continue; 


                $aReplacements = array();

                // put the template in the first index of the replacements
                $aReplacements['{TEMPLATE}'] = $field['template'];
                $aReplacements['{TEMPLATE0}'] 
                    = preg_replace(array("/\n/","/\r/"),'',$field['template']);
                $tmp_tpl = explode("\n", $field['template']);
                for($tpl_idx = 1; $tpl_idx < 10; $tpl_idx++){
                    $aReplacements['{TEMPLATE'.$tpl_idx.'}'] = "";
                }
                $tpl_idx=1;
                foreach ($tmp_tpl as $curr_idx){
                    $aReplacements['{TEMPLATE'.$tpl_idx.'}'] = trim($curr_idx);
                    $tpl_idx++;
                }
                
                // FieldID        
                $aReplacements['FIELD_ID'] = $iFID;

                // Title:
                if (($field['type'] == "radio") || ($field['type'] == "checkbox")) {
                    $field_title = $field['title'];
                } else {
                    $field_title = '<label for="field'.$iFID.'">'.$field['title'].'</label>';
                }
                $aReplacements['{TITLE}'] = $field_title;        

                // mark required fields:
                $aReplacements['{REQUIRED}'] = '';
                if ($field['required'] == 1) {
                    $aReplacements['{REQUIRED}'] 
                        = '<span class="'.MPFORM_CLASS_PREFIX
                        .'required required">*</span>';
                    $classes .= ' '.MPFORM_CLASS_PREFIX.'required';
                }

                // mark read only fields:
                $readonly = '';
                if ($field['required'] == 2) {
                    $readonly = ' readonly="readonly"';
                    $classes .= ' '.MPFORM_CLASS_PREFIX.'readonly';
                }

                $aReplacements['{HELP}'] = '';
                $aReplacements['{HELPTXT}'] = ''; 

                switch ($field['type']){
                    case 'textfield': 
                        $aReplacements['{FIELD}'] 
                            = '<input'
                            . ' type="text"'
                            . ' name="field'.$iFID.'"'
                            . ' id="field'.$iFID.'" '
                            . $maxlength
                            . ' value="'
                            .(isset($_SESSION['mpf']['field'.$iFID])
                                ? $_SESSION['mpf']['field'.$iFID]:$value).'" '
                            . ' class="'.$field_classes.' '.$sErrClass.'text" '
                            . "$readonly />";
                    break;

                    case 'hiddenfield': 
                        $aReplacements['{FIELD}'] 
                            = '<input'
                            . ' type="hidden"'
                            . ' name="field'.$iFID.'"'
                            . ' id="field'.$iFID.'" '
                            . ' value="'
                            .(isset($_SESSION['mpf']['field'.$iFID])
                                ? $_SESSION['mpf']['field'.$iFID]:$value).'" '
                            . ' class="'.$field_classes.' '.$sErrClass.'text" '
                            . "$readonly />";
                        $aReplacements['{TITLE}'] = '';
                    break;

                    case 'email_subj': 
                        $aReplacements['{FIELD}'] 
                            = '<input'
                            . ' type="text"'
                            . ' name="field'.$iFID.'"'
                            . ' id="field'.$iFID.'" '
                            . $maxlength
                            . ' value="'
                            .(isset($_SESSION['mpf']['field'.$iFID])
                                ?$_SESSION['mpf']['field'.$iFID]:$value).'" '
                            . ' class="'.$field_classes.' '.$sErrClass.'text" '
                            . "$readonly />";
                    break;        

                    case 'integer_number': 
                        $js = 'onkeypress="if(event.which) {
                            if((event.which &lt; 48 || event.which > 57)'
                            .' &amp;&amp; event.which != 8){return false;}' // Opera
                            .'} else if(document.all){ 
                            if(event.keyCode &lt; 48 || event.keyCode > 57){return false;}' // IE
                            .'}else{
                            if((event.charCode &lt; 48 || event.charCode > 57)'
                            .' &amp;&amp; event.charCode != 0){return false;}}"';  // FF
                        $aReplacements['{FIELD}'] 
                            = '<input'
                            . ' type="text" '
                            . $js
                            . ' name="field'.$iFID.'"'
                            . ' id="field'.$iFID.'" '
                            . $maxlength
                            .' value="'
                            .(isset($_SESSION['mpf']['field'.$iFID])
                                ?$_SESSION['mpf']['field'.$iFID]:$value).'"'
                            . ' class="'.$field_classes.' '.$sErrClass.'text" '
                            . "$readonly />";
                    break;

                    case 'decimal_number': 
                        $js = 'onkeypress="if(event.which) {
                            if((event.which &lt; 43 || event.which > 57 || event.which == 47)'
                            .' &amp;&amp; event.which != 8){return false;}' // Opera
                            .'} else if(document.all){ 
                            if(event.keyCode &lt; 43 || event.keyCode > 57'
                            .' || event.keyCode == 47){return false;}' // IE
                            .'}else{
                            if((event.charCode &lt; 43 || event.charCode > 57 || event.charCode == 47)'
                            .' &amp;&amp; event.charCode != 0){return false;}}"';  // FF
                        $aReplacements['{FIELD}'] 
                            = '<input'
                            . ' type="text" '
                            . $js
                            . ' name="field'.$iFID.'" '
                            . 'id="field'.$iFID.'" '
                            . $maxlength
                            . ' value="'
                            .(isset($_SESSION['mpf']['field'.$iFID])
                                ?$_SESSION['mpf']['field'.$iFID]:$value).'"'
                            . ' class="'.$field_classes.' '.$sErrClass.'text" '
                            . "$readonly />";
                    break; 

                    case 'filename': 
                        $vmax = '';
                        if ($first_MAX) {
                            $vmax = '<input'
                                . ' type="hidden"'
                                . ' name="MAX_FILE_SIZE"'
                                . ' value="'.$max_file_size.'" />';
                        } 
                        $sMaxFileSize 
                            = sprintf($LANG['frontend']['MAX_FILESIZE'], 
                                $max_file_size/1024, 
                                $upload_only_exts
                            );
                        $sMaxLength 
                            = str_replace(
                                "maxlength", 
                                "size", 
                                $maxlength
                            );
                        $sValue = 
                           (isset($_SESSION['mpf']['field'.$iFID])
                            ?$_SESSION['mpf']['field'.$iFID]
                            :$value
                        );
                        $bFileSizeHintShown;
                        $aReplacements['{FIELD}'] = $vmax;
                        if($bFileSizeHintShown==false){
                            $aReplacements['{FIELD}'] 
                                .= '<span class="'.MPFORM_CLASS_PREFIX.'small">'.$sMaxFileSize.'<br/></span>';
                            $aReplacements['{TITLE}'] 
                                = '<span class="'.MPFORM_CLASS_PREFIX.'small">&nbsp;<br/>&nbsp;<br/></span>'
                                . $aReplacements['{TITLE}']; 
                            $aReplacements['{HELP}'] 
                                = '<span class="'.MPFORM_CLASS_PREFIX.'small">&nbsp;<br/>&nbsp;<br/></span>';
                            $bFileSizeHintShown=true;
                        }
                        $aReplacements['{FIELD}'] 
                            .= '<input'
                            . ' type="file"'
                            . '  name="field'.$iFID.'[]"'
                            . ' multiple="multiple"'
                            . ' id="field'.$iFID.'" '.$sMaxLength
                            . ' value="'.$sValue.'"'
                            . ' class="'.$field_classes.' '.$sErrClass.'text" />'
                            . (isset($_SESSION['mpf']['datafield'.$iFID]['filenames'])?
                                ($_SESSION['mpf']['datafield'.$iFID]['filenames']):'');
                        $first_MAX = false;
                    break; 

                    case 'textarea': 
                        $cr = explode(",", $field['extra']);
                        if (isset($cr[0]) and is_numeric($cr[0])) {
                            $cols = $cr[0];
                        } else {
                            $cols = 25;
                        }
                        if (isset($cr[1]) and is_numeric($cr[1])) {
                            $rows = $cr[1];
                        } else {
                            $rows = 5;
                        }
                        if (isset($cr[2]) and is_numeric($cr[2])) {
                            $maxlength = ' maxlength='.$cr[2];
                        } else {
                            $maxlength = '';
                        }
                        $sContent 
                            = (isset($_SESSION['mpf']['field'.$iFID])
                                ?$_SESSION['mpf']['field'.$iFID]
                                :$value
                            );
                        $aReplacements['{FIELD}'] 
                            = '<textarea'
                                . ' name="field'.$iFID.'"'
                                . ' id="field'.$iFID.'"'
                                . ' class="'.$field_classes.' '.$sErrClass.'textarea" '
                                . ' cols="'.$cols.'"'
                                . ' rows="'.$rows.'" '
                                . $maxlength
                                . $readonly
                                . '>'
                                . $sContent
                                . '</textarea>';
                    break; 

                    case 'select': 
                        $options = explode(',', $value);
                        foreach ($options as $idx => $option){
                            make_option(
                                $option, 
                                $idx, 
                                $mpform_code,                            
                                (isset($_SESSION['mpf']['field'.$iFID]) 
                                    ? $_SESSION['mpf']['field'.$iFID] 
                                    : array()
                                ), 
                                $isnew, 
                                $value_option_separator
                            );
                            $options[$idx]=$option;
                        }
                        $field['extra'] = explode(',',$field['extra']);
                        $extras = '';
                        if (is_numeric($field['extra'][0])) {
                            $extras .=  'size="' .$field['extra'][0]. '" ';
                        }
                        if ($field['extra'][1] == "multiple") {
                            $extras .= 'multiple="multiple" ';
                        }
                        $aReplacements['{FIELD}'] 
                             = '<select'
                                 . ' name="field'.$iFID.'[]"'
                                 . ' id="field'.$iFID.'" '
                                 . $extras 
                                 . ' class="'.$field_classes.' '.$sErrClass.'select">'
                                 . implode($options)
                                 . '</select>';
                    break; 

                    case 'email_recip': 
                        $options = array();
                        array_push($options, $LANG['frontend']['select']);
                        $emails = preg_split('/[\r\n]/', $email_to);
                        foreach ($emails as $recip) {
                            $teil = explode("<", $recip);
                            if (trim($teil[0])!='')
                            array_push($options, htmlspecialchars($teil[0], ENT_QUOTES));
                        }
                        foreach ($options as $idx => $option){
                            make_option(
                                $option, 
                                $idx, 
                                $mpform_code,                    
                                (isset($_SESSION['mpf']['field'.$iFID])
                                    ?$_SESSION['mpf']['field'.$iFID]
                                    :array()
                                ), 
                                false, 
                                $value_option_separator
                            );
                            $options[$idx]=$option;                            
                        }
                        $aReplacements['{FIELD}'] 
                            = '<select'
                            . ' name="field'.$iFID.'[]"'
                            . ' id="field'.$iFID.'"'
                            . ' class="'.$field_classes.' '.$sErrClass.'select">';
                        $aReplacements['{FIELD}'] .= implode($options);
                        $aReplacements['{FIELD}'] .= '</select>';
                    break; 

                    case 'heading': 
                       $aReplacements['{FIELD}']
                           = '<input'
                            . ' type="hidden"'
                            . ' name="field'.$iFID.'"'
                            . ' id="field'.$iFID.'"'
                            . ' value="===['.$field['title'].']===" />';
                        // temporarily modify the field loop template
                        $tmp_field_loop = $field_loop;    
                        $field_loop = $field['extra'];
                    break; 

                    case 'fieldset_start': 
                        // temporarily modify the field loop template
                        $tmp_field_loop = $field_loop;    
                        $field_loop = '';
                        if ($bTableLayout) {
                            $field_loop .= "</table>".PHP_EOL; 
                        }
                        $field_loop 
                            .= "<fieldset><legend>"
                            . $field['title'] 
                            ."</legend>"
                            .PHP_EOL; 
                        if ($bTableLayout) { 
                            $field_loop .= $header.PHP_EOL;  
                        }
                    break; 

                    case 'fieldset_end': 
                        // temporarily modify the field loop template
                        $tmp_field_loop = $field_loop;    
                        $field_loop = '';
                        if ($bTableLayout) {
                            $field_loop .= "</table>".PHP_EOL; 
                        }
                        $field_loop .= "</fieldset>".PHP_EOL; 
                        if ($bTableLayout) {
                            $field_loop .= $header.PHP_EOL; 
                        }
                    break; 

                    case 'checkbox': 
                        $options = explode(',', $value);
                        $mpform_code = $enum_start;
                        foreach ($options as $idx => $option){
                            make_checkbox(
                                $option, 
                                $idx, 
                                $mpform_code,                            
                                $iFID, 
                                $field['extra'], 
                                (isset($_SESSION['mpf']['field'.$iFID])
                                    ?$_SESSION['mpf']['field'.$iFID]
                                    :array()
                                ), 
                                $field_classes." ".$sErrClass, 
                                $isnew, 
                                $value_option_separator
                            );
                            $options[$idx]=$option;                            
                        }
                        $options[count($options)-1]
                            =substr_replace(
                                $options[count($options)-1],
                                "",
                                -strlen($field['extra'])-strlen(PHP_EOL)
                            );
                        $aReplacements['{FIELD}'] = implode($options);           
                    break; 

                    case 'radio': 
                        $options = explode(',', $value);
                        $mpform_code = $enum_start;
                        foreach ($options as $idx => $option){
                            make_radio(
                                $option, 
                                $idx, 
                                $mpform_code,                    
                                $iFID, 
                                $field['extra'], 
                                (isset($_SESSION['mpf']['field'.$iFID])
                                    ?$_SESSION['mpf']['field'.$iFID]
                                    :''
                                ), 
                                $field_classes." ".$sErrClass, 
                                $isnew, 
                                $value_option_separator
                            );
                            $options[$idx]=$option;                            
                        }
                        $options[count($options)-1]
                            =substr_replace(
                                $options[count($options)-1],
                                "",
                                -strlen($field['extra'])-strlen(PHP_EOL)
                            );
                        $aReplacements['{FIELD}'] = implode($options);
                    break; 

                    case 'email':        
                        $aReplacements['{FIELD}'] 
                        = '<input'
                        . ' type="text"'
                        . ' name="field'.$iFID.'"'
                        . ' id="field'.$iFID.'"'
                        . ' value="'
                        .(isset($_SESSION['mpf']['field'.$iFID])
                            ?$_SESSION['mpf']['field'.$iFID]
                            :'')
                        .'"'
                        . $maxlength
                        . 'class="'.$field_classes.' '.$sErrClass.'email" '
                        ."$readonly />";       
                    break; 

                    case 'date':        
                        $cla['field'.$iFID] = "field".$iFID;
                        $sValue 
                            = (isset ($_SESSION['mpf']['field'.$iFID]) 
                                ? $_SESSION['mpf']['field'.$iFID] 
                                : $value
                            );
                        $aReplacements['{FIELD}'] 
                            = '<table cellpadding="0" cellspacing="0" border="0">'
                            . PHP_EOL
                            . '<tr>'
                            . PHP_EOL
                            . '<td>'
                            . PHP_EOL
                            . '<input type="text" name="field'.$iFID.'"'
                            . ' id="field'.$iFID.'"'
                            . $maxlength
                            . ' value="'
                            . $sValue
                            . '" class="'.$field_classes.' '.$sErrClass.'date" />'                            
                            . '</td>'
                            . PHP_EOL
                            .'<td>'
                            .PHP_EOL
                            .'<img src="'.MPFORM_ICONS 
                            .'/cal.gif"'
                            . ' id="field'
                            .$iFID.'_trigger"'
                            . ' class="'.MPFORM_CLASS_PREFIX.'date_img"'
                            . ' title="'.$TEXT['CALENDAR']
                            .'" alt="'.$TEXT['CALENDAR'].'" />'
                            ."</td>"
                            .PHP_EOL
                            ."</tr></table>".PHP_EOL; 
                    break;   
                } // switch...case

                if(isset($_SESSION['mpf']['field'.$iFID])) { 
                    unset($_SESSION['mpf']['field'.$iFID]);
                }      
                if ($field['help']) {
                    $sHelp = preg_replace('/[\r\n]/', "<br />", $field['help']);
                    $sHelp = str_replace('&quot;', '\\&quot;', $sHelp);
                    $sHelpText = '<p class="help_txt">'.$sHelp.'</p>'.PHP_EOL;        
                    $sHelpLink 
                        =  '<a id="mpform_a_'
                        . $iFID 
                        . '" class="mpform_a_help"'
                        . ' href="#"'
                        . ' onclick="javascript:helpme(\'mpform_a_'.$iFID.'\', \''
                        .$sHelp.'\', \''
                        .str_replace("'","\'",$field['title'])
                        .'\', \''
                        .$MENU['HELP']
                        .'\'); return false;"'
                        . ' title="'.$MENU['HELP'].'"><img'
                        . ' class="mpform_img_help"'
                        . ' src="'.MPFORM_ICONS.'/help.gif"'
                        . ' alt="'.$MENU['HELP']
                        .'" /></a>';
                    $aReplacements['{HELP}'] .= $sHelpLink;
                    $aReplacements['{HELPTXT}'] 
                        = htmlspecialchars_decode($sHelpText); // help text always to show         

                    if ($bNeedHelpButton) {
                        $bLoadHelpJS = true;
                    }
                }

                if(($field['type'] == 'hiddenfield') && (!$bTableLayout)){
                    echo $aReplacements['{FIELD}'].PHP_EOL;
                } else {
                    if ($field['type'] != 'html') {

                        $aReplacements['{CLASSES}'] = $classes;
                        if(isset($aReplacements['{FIELD}']))
                            $aReplacements['{FORMATTED_FIELD}'] = $aReplacements['{FIELD}'];
                        $aReplacements['{ERRORTEXT}'] 
                            = (isset($aErrTxt[$iFID])) ? '<p>'.$aErrTxt[$iFID].'</p>' : '';
                        if($field['type'] != '') {
                            echo str_replace(
                                array_keys($aReplacements), 
                                array_values($aReplacements), 
                                $field_loop
                            ).PHP_EOL;
                        }
                    } else {
                        if(($field['extra'] == '') or (preg_match('/form/',$field['extra'])))
                            echo remove_comments(htmlspecialchars_decode($field['value']));  
                    }
                }
                if (isset($tmp_field_loop)) {
                    $field_loop = $tmp_field_loop;
                }
            } // while loop
        } // if ... numRows > 0

        // Captcha
        if($use_captcha) {
            if (in_array('captcha'.$iSID, $aMissing)) {
                $classes = 'captcha_err';
            } else {
                $classes = 'captcha';
            }
            
            $aReplacements = array (
                '{TEMPLATE}'  => '',
                '{FIELD_ID}'  => 'captcha',
                '{TITLE}'     => $LANG['frontend']['VERIFICATION'],
                '{REQUIRED}'  => '<span class="'.MPFORM_CLASS_PREFIX.'required">*</span>',
                '{FIELD}'     => "'; call_captcha('all', '', $iSID); echo '",
                '{FORMATTED_FIELD}' => "'; call_captcha('all', '', $iSID); echo '",
                '{HELP}'      => '',
                '{HELPTXT}'   => '',
                '{CLASSES}'   => $classes,
                '{ERRORTEXT}' => (isset($aErrTxt['captcha'.$iSID])) 
                               ? $aErrTxt['captcha'.$iSID] : ''
            );
            for($tpl_idx = 0; $tpl_idx < 10; $tpl_idx++){
                $aReplacements['{TEMPLATE'.$tpl_idx.'}'] = "";
            }
            $sReplacedLoopField 
                = str_replace(
                    array_keys($aReplacements), 
                    array_values($aReplacements), 
                    $field_loop
                );
            $cmd = "{echo '" . $sReplacedLoopField . "';}";
            eval($cmd);
        }

        // Print footer
        echo $footer;
        echo PHP_EOL.'</form>'.PHP_EOL;
        if(defined('MPFORM_DIV_WRAPPER')){
            echo '</div>'.PHP_EOL;    
        }

        if($bLoadHelpJS) {
            js_for_help();
        }
        foreach($cla as $k => $v) {
            $sJS = '<script type="text/javascript">'.PHP_EOL; 
            $sJS .= "Calendar.setup( {
                inputField  : \"$k\",
                ifFormat    : \"$jscal_ifformat\",
                button      : \"$k"."_trigger\",
                firstDay    : $jscal_firstday,
                ";
            if (isset($jscal_use_time) && $jscal_use_time==TRUE) { 
                $sJS .= "showsTime : \"true\",
                timeFormat : \"24\",
                ";
            } 
            $sJS .= "date  : \"$jscal_today\",
                range  : [1970, 2037],
                step : 1
            } );
            </script>";
            echo $sJS;
        }
    }
}
