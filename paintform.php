<?php
/*
   WebsiteBaker CMS module: mpForm
   ===============================
   This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
   
   @module              mpform
   @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Quinto, Martin Hecht (mrbaseman)
   @copyright           (c) 2009 - 2015, Website Baker Org. e.V.
   @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
   @license             GNU General Public License

   Improvements are copyright (c) 2009-2011 Frank Heyne

   For more information see info.php   

*/
/* This file paints the form in the frontend. */
// Must include code to stop this file being access directly
if (!defined('WB_PATH')){
    exit("Cannot access this file directly"); 
}

require_once(dirname(__FILE__).'/constants.php');

// Function for generating an options for a select field
if (!function_exists('make_option')) {
        function make_option(&$n, $k, $params) {
                global $code;
                $values = $params[0];
                $isnew = $params[1];
                $value_option_separator = $params[2];
                $def = strpos($n, IS_DEFAULT);
                ($def > 0) ? $h = substr($n, 0, $def) : $h = $n;
                $vals=explode($value_option_separator,$h);
                if(count($vals)==1) $vals[1]=$vals[0];
                // start option group if it exists
                if (substr($n, 0, 2) == '[=') {
                        $n = '<optgroup label="'.substr($n,2,strlen($n)).'">';
                } elseif ($n == ']') {
                        $n = '</optgroup>';
                } else {
                        if (in_array($h, $values) or ($isnew and $def > 0)) {
                                $n = '<option selected="selected" value="'.$vals[0].'">'.$vals[1].'</option>';
                        } else {
                                $n = '<option value="'.$vals[0].'">'.$vals[1].'</option>';
                        }
                }
        }
}

// Function for generating a checkbox
if (!function_exists('make_checkbox')) {
        function make_checkbox(&$n, $idx, $params) {
                global $code;
                $def = strpos($n, IS_DEFAULT);
                ($def > 0) ? $h = substr($n, 0, $def) : $h = $n;
                $value_option_separator = $params[5];
                $vals=explode($value_option_separator,$h);
                if ($code=="") {
                        $v = $vals[0];
                } else {
                        if(count($vals)==1){
                                $v = $code;
                                $code = chr(ord($code)+1);
                        } else $v = $vals[0];
                }
                if(count($vals)==1) $vals[1]=$vals[0];
                $field_id = $params[0];
                $seperator = $params[1];
                $sErrClass = $params[3];
                $isnew = $params[4];
                $label_i = urlencode($n) . $field_id;
                $bad = array("%", "+");
                $label_id = 'wb_'.str_replace($bad, "", $label_i);
                if (in_array($v, $params[2]) or ($isnew and $def > 0)) {
            $n = '<input class="'.$sErrClass.'checkbox" type="checkbox" id="'.$label_id.'" name="field'.$field_id.'['.$idx.']" value="'.$v.
            '" checked="checked" />'.'<label for="'.$label_id.'" class="'.MPFORM_CLASS_PREFIX.'checkbox_label">'.$vals[1].'</label>'.$seperator.PHP_EOL; 
                } else {
            $n = '<input class="'.$sErrClass.'checkbox" type="checkbox" id="'.$label_id.'" name="field'.$field_id.'['.$idx.']" value="'.$v.
            '" />'.'<label for="'.$label_id.'" class="'.MPFORM_CLASS_PREFIX.'checkbox_label">'.$vals[1].'</label>'.$seperator.PHP_EOL; 
                }        
        }
}

// Function for generating a radio button
if (!function_exists('make_radio')) {
        function make_radio(&$n, $idx, $params) {
                global $code;
                $def = strpos($n, IS_DEFAULT);
                ($def > 0) ? $h = substr($n, 0, $def) : $h = $n;
                $value_option_separator = $params[5];
                $vals=explode($value_option_separator,$h);
                if ($code=="") {
                        $v = $vals[0];
                } else {
                        if(count($vals)==1){
                                $v = $code;
                                $code = chr(ord($code)+1);
                        } else $v = $vals[0];
                }
                if(count($vals)==1) $vals[1]=$vals[0];
                $field_id = $params[0];
                $seperator = $params[1];
                $sErrClass = $params[3];
                $isnew = $params[4];
                $label_i = urlencode($n) . $field_id;
                $bad = array("%", "+");
                $label_id = 'wb_'.str_replace($bad, "", $label_i);
                if (($v == $params[2]) or ($isnew and $def > 0)) {
            $n = '<input class="'.$sErrClass.'radio" type="radio" id="'.$label_id.'" name="field'.$field_id.'" value="'.$v.
            '" checked="checked" />'.'<label for="'.$label_id.'" class="'.MPFORM_CLASS_PREFIX.'radio_label">'.$vals[1].'</label>'.$seperator.PHP_EOL; 
                } else {
            $n = '<input class="'.$sErrClass.'radio" type="radio" id="'.$label_id.'" name="field'.$field_id.'" value="'.$v.
            '" />'.'<label for="'.$label_id.'" class="'.MPFORM_CLASS_PREFIX.'radio_label">'.$vals[1].'</label>'.$seperator.PHP_EOL; 
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
echo <<<JS
        <script language="javascript" type="text/javascript">
        //<![CDATA[
        var theRowOpened = -2;
        var theTableOpened = -2;
        function helpme(id,msg,title,help) {
                var theTableBody = document.getElementById(id).parentNode.parentNode.parentNode.parentNode.tBodies[0];
                var row = 1+document.getElementById(id).parentNode.parentNode.rowIndex;
                if ((theRowOpened == row) && (theTableOpened == theTableBody)) {
                        removeRow(theRowOpened, theTableOpened);
                        theRowOpened = -1;
                } else {
                        if (theRowOpened > 0) {
                                if(theRowOpened<row) row--;
                                removeRow(theRowOpened, theTableOpened);
                        }
                        insertTableRow(row,msg,title,help,theTableBody);
                        theRowOpened = row;
                        theTableOpened = theTableBody;
                }
        }
        function insertTableRow(row,msg,title,help,theTableBody) {
                var newCell;
                var newRow = theTableBody.insertRow(row);
                newCell = newRow.insertCell(0);
                newCell = newRow.insertCell(1);
                newCell.colSpan = 2;
                newCell.className = "mpform_help_box_td";
                newCell.innerHTML = "<div class='mpform_help_box_div'>" 
                    +((title) ? '<h5 class="mpform_help_box_h5">'+help+': '
                                                                +title+'<\/h5><hr class="mpform_help_box_hr" noshade="noshade" size="1" />' : '')
                                                                +'<h6 class="mpform_help_box_h6">'+msg+'<\/h6><\/div>';
        }
        function removeRow(row,theTableBody) {
                theTableBody.deleteRow(row);
        }
        //]]>
        </script>        
JS;
        }
}

////////////////// Main function ///////////////////////

if (!function_exists('paint_form')) {
    function paint_form(
            $iSID, //section_id
            $aMissing = array(), 
            $aErrTxt = array(), 
            $isnew = true
    ) {
        global $database, $MENU, $TEXT, $LANG;
        global $code;
        
        if($aMissing != array()) {
                if(!isset($LANG['frontend']['REQUIRED_FIELDS'])) {
                        $msg = 'Please complete or correct the fields in red color!';
                } else {
                        $msg = $LANG['frontend']['REQUIRED_FIELDS'];
                }
            echo '<div class="'.MPFORM_CLASS_PREFIX.'missing">'.$msg.'</div>';
        }

        // Get settings
        $query_settings = $database->query("SELECT * FROM ".TP_MPFORM."settings WHERE section_id = '$iSID'");
        if($query_settings->numRows() > 0) {
            $aSettings        = $query_settings->fetchRow();
            $header           = str_replace('{WB_URL}', WB_URL, $aSettings['header']);
            $field_loop       = $aSettings['field_loop'];
            $footer           = str_replace('{WB_URL}', WB_URL, $aSettings['footer']);
            $use_captcha      = $aSettings['use_captcha'];
            $is_following     = $aSettings['is_following'];
            $value_option_separator = $aSettings['value_option_separator'];
            if($value_option_separator=="") $value_option_separator=MPFORM_DEFAULT_OPT_SEPARATOR; // fallback
            $max_file_size    = $aSettings['max_file_size_kb'] * 1024;
            $date_format      = $aSettings['date_format'];
            $email_to         = $aSettings['email_to'];
            $enum_start       = $aSettings['enum_start'];
            $success_page     = $aSettings['success_page'];
            $upload_only_exts = $aSettings['upload_only_exts'];
        } else {
                exit($TEXT['UNDER_CONSTRUCTION']);
        }

        $bNeedHelpButton = (strpos($field_loop, "{HELP}") !== false);  // we only need a help button if this variable is used
        // execute private function in private.php, if available
        if (function_exists('private_function_before_new_form')) {
            private_function_before_new_form($iSID);
        }

        // Set new submission ID in session if it is not a follower on a multipage form
        if (!$is_following) {
            $_SESSION['submission_id_'.$iSID] = new_submission_id();
        }
        if ($success_page != 'none') {
                $qs = $database->query("SELECT * FROM ".TABLE_PREFIX."sections WHERE page_id = '$success_page' AND module = 'mpform'");
                if($qs->numRows() > 0) {
                        $s = $qs->fetchRow();
                        $sid = $s['section_id'];
                $_SESSION['submission_id_'.$sid] = substr($_SESSION['submission_id_'.$iSID], 0, 8);
                }
        }

        // remember the referer page:
        if (isset($_SERVER['HTTP_REFERER'])) {
                if (!isset($_SESSION['href'])) $_SESSION['href'] = addslashes(htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES));
        } else {
                $_SESSION['href'] = 'unknown';
        }

        $jscal_today = '';
        // Do i need to include calendar files ?
        $query_fields= $database->query("SELECT * FROM ".TP_MPFORM."fields WHERE section_id = '$iSID' AND type = 'date'");
        if($query_fields->numRows() > 0) {
                // include jscalendar-setup
                $jscal_use_time = true; // whether to use a clock, too
                require_once(dirname(__FILE__) . "/jscalendar.php");
                $jscal_firstday = "1"; // - first-day-of-week (0-sunday, 1-monday, ...) (default: 0(EN) or 1(everything else))
                if ($date_format) $jscal_ifformat = $date_format; //"%Y-%m-%d"; // - format for jscalendar (default: from wb-backend-date-format)
        }

        $sActionAttr = htmlspecialchars(strip_tags($_SERVER['SCRIPT_NAME']));
        $sValueAttr  = $_SESSION['submission_id_'.$iSID];
        
        if(defined('MPFORM_DIV_WRAPPER')){
            echo PHP_EOL.'<div class="'.MPFORM_DIV_WRAPPER.'">'.PHP_EOL;
        }

        echo '<form name="form_'.$iSID.'"  enctype="multipart/form-data" action="'. $sActionAttr .'#wb_section_'.$iSID.'" method="post">'.PHP_EOL;
        echo '<input type="hidden" name="submission_id" value="'. $sValueAttr .'" />'.PHP_EOL; 
       
        if (WB_VERSION >= "2.8.2") { 
            global $admin;
            $admin->getFTAN();
        }
        if(ENABLED_ASP) {
            echo draw_asp_honeypots($iSID);
        }
        // Print header
        echo $header;
        $first_MAX = true;

        // Get list of fields
        $query_fields = $database->query("SELECT * FROM ".TP_MPFORM."fields WHERE section_id = '$iSID' ORDER BY position ASC");
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
                $classes = 'fid'.$iFID; 
                $classes .= ' '.MPFORM_CLASS_PREFIX. $field['type'];
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

                        
                $aReplacements = array();
                
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
                    $aReplacements['{REQUIRED}'] = '<span class="'.MPFORM_CLASS_PREFIX.'required required">*</span>';
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
                        $aReplacements['{FIELD}'] = '<input type="text" name="field'.$iFID.'" id="field'.$iFID.'" '.$maxlength.' value="'.(isset($_SESSION['mpf']['field'.$iFID])?$_SESSION['mpf']['field'.$iFID]:$value).'" class="'.$sErrClass.'text" '."$readonly />";
                        break;
                    
                    case 'email_subj': 
                        $aReplacements['{FIELD}'] = '<input type="text" name="field'.$iFID.'" id="field'.$iFID.'" '.$maxlength.' value="'.(isset($_SESSION['mpf']['field'.$iFID])?$_SESSION['mpf']['field'.$iFID]:$value).'" class="'.$sErrClass.'text" '."$readonly />";
                        break;                    
                     
                    case 'integer_number': 
                                $js = 'onkeypress="if(event.which) {
                                                if((event.which &lt; 48 || event.which > 57) &amp;&amp; event.which != 8){return false;}' // Opera
                                        .'} else if(document.all){ 
                                                if(event.keyCode &lt; 48 || event.keyCode > 57){return false;}' // IE
                                        .'}else{
                                                if((event.charCode &lt; 48 || event.charCode > 57) &amp;&amp; event.charCode != 0){return false;}}"';  // FF
                        $aReplacements['{FIELD}'] = '<input type="text" '.$js.' name="field'.$iFID.'" id="field'.$iFID.'" '.$maxlength.' value="'.(isset($_SESSION['mpf']['field'.$iFID])?$_SESSION['mpf']['field'.$iFID]:$value).'" class="'.$sErrClass.'text" '."$readonly />";
                        break;
                    
                    case 'decimal_number': 
                                $js = 'onkeypress="if(event.which) {
                                                if((event.which &lt; 43 || event.which > 57 || event.which == 47) &amp;&amp; event.which != 8){return false;}' // Opera
                                        .'} else if(document.all){ 
                                                if(event.keyCode &lt; 43 || event.keyCode > 57 || event.keyCode == 47){return false;}' // IE
                                        .'}else{
                                                if((event.charCode &lt; 43 || event.charCode > 57 || event.charCode == 47) &amp;&amp; event.charCode != 0){return false;}}"';  // FF
                        $aReplacements['{FIELD}'] = '<input type="text" '.$js.' name="field'.$iFID.'" id="field'.$iFID.'" '.$maxlength.' value="'.(isset($_SESSION['mpf']['field'.$iFID])?$_SESSION['mpf']['field'.$iFID]:$value).'" class="'.$sErrClass.'text" '."$readonly />";
                        break; 
                    
                    case 'filename': 
                        $vmax = '';
                        if ($first_MAX) {
                           $vmax = '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_file_size.'" />';
                        } 
                        $sMaxFileSize = sprintf($LANG['frontend']['MAX_FILESIZE'], $max_file_size/1024, $upload_only_exts);
                        $sMaxLength = str_replace("maxlength", "size", $maxlength);
                        $sValue = (isset($_SESSION['mpf']['field'.$iFID])?$_SESSION['mpf']['field'.$iFID]:$value);
                        $bFileSizeHintShown;
                        $aReplacements['{FIELD}'] = $vmax;
                        if($bFileSizeHintShown==false){
                            $aReplacements['{FIELD}'] .= '<span class="mpform_small">'.$sMaxFileSize.'<br/></span>';
                            $aReplacements['{TITLE}'] = '<span class="mpform_small">&nbsp;<br/>&nbsp;<br/></span>' . $aReplacements['{TITLE}']; 
                            $aReplacements['{HELP}'] = '<span class="mpform_small">&nbsp;<br/>&nbsp;<br/></span>';

                            $bFileSizeHintShown=true;
                        }
                        $aReplacements['{FIELD}'] .= '<input type="file"  name="field'.$iFID.'[]" multiple="multiple" id="field'.$iFID.'" '.$sMaxLength.' value="'.$sValue.'"'
                                . ' class="'.$sErrClass.'text" />';
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
                        $sContent = (isset($_SESSION['mpf']['field'.$iFID])?$_SESSION['mpf']['field'.$iFID]:$value);
                        $aReplacements['{FIELD}'] = '<textarea name="field'.$iFID.'" id="field'.$iFID.'" class="'.$sErrClass.'textarea" cols="'.$cols.'" rows="'.$rows.'" '.$readonly.'>'.$sContent.'</textarea>';
                        break; 
                    
                    case 'select': 
                                $options = explode(',', $value);
                        array_walk ($options, 'make_option', array((isset($_SESSION['mpf']['field'.$iFID]) ? $_SESSION['mpf']['field'.$iFID] : array()), $isnew, $value_option_separator));
                                $field['extra'] = explode(',',$field['extra']);
                                $extras = '';
                                if (is_numeric($field['extra'][0])) {
                            $extras .=  'size="' .$field['extra'][0]. '" ';
                                }
                                if ($field['extra'][1] == "multiple") {
                                        $extras .= 'multiple="multiple" ';
                                }
                        $aReplacements['{FIELD}'] = '<select name="field'.$iFID.'[]" id="field'.$iFID.'" '. $extras .' class="'.$sErrClass.'select">'.implode($options).'</select>';
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
                        array_walk($options, 'make_option', array((isset($_SESSION['mpf']['field'.$iFID])?$_SESSION['mpf']['field'.$iFID]:array()), false));
                        $aReplacements['{FIELD}'] = '<select name="field'.$iFID.'[]" id="field'.$iFID.'" class="'.$sErrClass.'select">';
                        $aReplacements['{FIELD}'] .= implode($options);
                        $aReplacements['{FIELD}'] .= '</select>';
                        break; 
                    
                    case 'heading': 
                       $aReplacements['{FIELD}'] = '<input type="hidden" name="field'.$iFID.'" id="field'.$iFID.'" value="===['.$field['title'].']===" />';
                                $tmp_field_loop = $field_loop;                // temporarily modify the field loop template
                                $field_loop = $field['extra'];
                        break; 
                    
                    case 'fieldset_start': 
                                $tmp_field_loop = $field_loop;                // temporarily modify the field loop template
                                $field_loop = '';
                        if ($bTableLayout) {
                            $field_loop .= "</table>".PHP_EOL; 
                        }
                        $field_loop .= "<fieldset><legend>". $field['title'] ."</legend>".PHP_EOL; 
                        if ($bTableLayout) { 
                            $field_loop .= $header.PHP_EOL;  
                        }
                        break; 
                    
                    case 'fieldset_end': 
                                $tmp_field_loop = $field_loop;                // temporarily modify the field loop template
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
                                $code = $enum_start;
                        array_walk($options, 'make_checkbox', array($iFID, $field['extra'], (isset($_SESSION['mpf']['field'.$iFID])?$_SESSION['mpf']['field'.$iFID]:array()), $sErrClass, $isnew, $value_option_separator));
                        $options[count($options)-1]=substr_replace($options[count($options)-1],"",-strlen($field['extra'])-strlen(PHP_EOL));
                        $aReplacements['{FIELD}'] = implode($options);                       
                        break; 
                    
                    case 'radio': 
                                $options = explode(',', $value);
                                $code = $enum_start;
                        array_walk($options, 'make_radio', array($iFID, $field['extra'], (isset($_SESSION['mpf']['field'.$iFID])?$_SESSION['mpf']['field'.$iFID]:''), $sErrClass, $isnew, $value_option_separator));
                        $options[count($options)-1]=substr_replace($options[count($options)-1],"",-strlen($field['extra'])-strlen(PHP_EOL));
                        $aReplacements['{FIELD}'] = implode($options);
                        break; 
                    
                    case 'email':                        
                        $aReplacements['{FIELD}'] = '<input type="text" name="field'.$iFID.'" id="field'.$iFID.'" value="'.(isset($_SESSION['mpf']['field'.$iFID])?$_SESSION['mpf']['field'.$iFID]:'').'"'
                                .$maxlength.' class="'.$sErrClass.'email" '."$readonly />";               
                        break; 
                    
                    case 'date':                        
                        $cla['field'.$iFID] = "field".$iFID;
                        $sValue = (isset ($_SESSION['mpf']['field'.$iFID]) ? $_SESSION['mpf']['field'.$iFID] : $value);
                        $aReplacements['{FIELD}'] = '<table cellpadding="0" cellspacing="0" border="0">'.PHP_EOL.'<tr>'.PHP_EOL.'<td>'.PHP_EOL
                            .'<input type="text" name="field'.$iFID.'" id="field'.$iFID.'"'.$maxlength.' value="'.$sValue.'" class="'.$sErrClass.'date" />'
                            .'</td>'.PHP_EOL.'<td>'.PHP_EOL
                            .'<img src="'.MPFORM_ICONS .'/cal.gif" id="field'.$iFID.'_trigger" class="'.MPFORM_CLASS_PREFIX.'date_img" title="'.$TEXT['CALENDAR']
                                .'" alt="'.$TEXT['CALENDAR'].'" />'
                            ."</td>".PHP_EOL."</tr>\n</table>".PHP_EOL; 
                        break;   
                        }
                        
                if(isset($_SESSION['mpf']['field'.$iFID])) { 
                    unset($_SESSION['mpf']['field'.$iFID]);
                }      
                        if ($field['help']) {
                        $sHelp = preg_replace('/[\r\n]/', "<br />", $field['help']);
                        $sHelp = str_replace('&quot;', '\\&quot;', $sHelp);
                        $sHelpText = '<p class="help_txt">'.$sHelp.'</p>'.PHP_EOL;                        
                        $sHelpLink =  '<a id="mpform_a_'. $iFID . '" class="mpform_a_help" href="#"'
                                . ' onclick="javascript:helpme(\'mpform_a_'.$iFID.'\', \''.$sHelp.'\', \''.str_replace("'","\'",$field['title']).'\', \''.$MENU['HELP'].'\'); return false;"'
                                . ' title="'.$MENU['HELP'].'"><img class="mpform_img_help" src="'.MPFORM_ICONS.'/help.gif" alt="'.$MENU['HELP'].'" /></a>';
                        $aReplacements['{HELP}'] .= $sHelpLink;
                        $aReplacements['{HELPTXT}'] = htmlspecialchars_decode($sHelpText); // help text always to show                     
                        
                        if ($bNeedHelpButton) {
                            $bLoadHelpJS = true;
                        }
                        }

                        if ($field['type'] != 'html') {
                    
                    $aReplacements['{CLASSES}'] = $classes;
                    $aReplacements['{ERRORTEXT}'] = (isset($aErrTxt[$iFID])) ? '<p>'.$aErrTxt[$iFID].'</p>' : '';
                                if($field['type'] != '') {
                        echo str_replace(array_keys($aReplacements), array_values($aReplacements), $field_loop).PHP_EOL;
                                }
                        } else {
                                echo htmlspecialchars_decode($field['value']);  // output html field without any translation
                        }
                if (isset($tmp_field_loop)) {
                    $field_loop = $tmp_field_loop;
                }
                }
        }
        
        // Captcha
        if($use_captcha) {
            if (in_array('captcha'.$iSID, $aMissing)) {
                $classes = 'captcha_err';
                } else {
                $classes = 'captcha';
                }

            $aReplacements = array (
                '{FIELD_ID}'  => 'captcha',
                '{TITLE}'     => $LANG['frontend']['VERIFICATION'],
                '{REQUIRED}'  => '<span class="'.MPFORM_CLASS_PREFIX.'required">*</span>',
                '{FIELD}'     => "'; call_captcha('all', '', $iSID); echo '",
                '{HELP}'      => '',
                '{HELPTXT}'   => '',
                '{CLASSES}'   => $classes,
                '{ERRORTEXT}' => (isset($aErrTxt['captcha'.$iSID])) ? $aErrTxt['captcha'.$iSID] : ''
            );

            $sReplacedLoopField = str_replace(array_keys($aReplacements), array_values($aReplacements), $field_loop);
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
            $sJS .= "Calendar.setup( {\n\tinputField  : \"$k\",\n\tifFormat    : \"$jscal_ifformat\",\n\tbutton      : \"$k"."_trigger\",\n\tfirstDay    : $jscal_firstday,\n";
                if (isset($jscal_use_time) && $jscal_use_time==TRUE) { 
                $sJS .= "        showsTime : \"true\",\n\ttimeFormat : \"24\",\n";
                } 
            $sJS .= "        date  : \"$jscal_today\",\n\trange  : [1970, 2037],\n\tstep : 1\n} );\n</script>";
            echo $sJS;
        }
}
}
