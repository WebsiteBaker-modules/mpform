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
/* The file EN.php provides strings in English language. */

// declare module language array
global $LANG;
$LANG = array();

// Text outputs for the backend
$LANG['backend'] = array(
        'SETTINGS' => 'Form Settings',
        'TXT_TYP'        => 'Type',
        'TXT_LIST'        => "List",
        'TXT_DEFAULT'        => "Default value",
        'TXT_ISFOLLOWING' => "Has a predecessing form",
        'des_is_following'=> 'Specifies whether the page is the first page of a multi page form or not.<br />
For forms consisting of a single page No is required!',
        'TXT_MP_SETTINGS' => 'Workflow Settings',
        'TXT_LAYOUT_SETTINGS' => 'Layout',
        'EMAIL_SETTINGS_1' => 'E-Mail Settings (Mail to site owner)',
        'EMAIL_SETTINGS_2' => 'E-Mail Settings (Confirmation mail to user)',
        'TXT_STATIC_ADDR' => 'Address as defined in the next line',
        'TXT_STATIC_NAME' => 'Name as defined in the next line',
        'TXT_USER_ADDR'        => 'Email Address of the logged on user',
        'TXT_USER_NAME'        => 'Name of the logged on user',
        'EMAIL_SUBJECT' => 'Results from form on website...',
        'EMAIL_SUC_TEXT' => 'Thank you for submitting your form on ',
        'EMAIL_SUC_SUBJ' => 'You have submitted a form',
        'VERIFICATION' => 'Verification (SPAM protection)',
        'HELP' => 'Help for',
        'TXT_COPY_FIELD' => 'Copy Field',
        'TXT_ADD_FIELD'        => 'Add Field',
        'ROWS'        => 'Rows',
        'TXT_TBLSFX' => 'Name suffix for results table',
        'DES_TXT_TBLSFX' => 'Results from all pages with the same suffix go into the same table.',
        'textfield'        => 'Text field (one line)',
        'textarea'        => 'Text area (multi line)',
        'date'        => 'Date',
        'fileupload'        => 'File upload',
        'txt_email_to'        => '&quot;To&quot; Address(es)',
        'des_email_to'        => 'Put only one &quot;To&quot; address on a line, formatted as in the following example:<br />
 &nbsp; Support stuff &lt;support@yourbussines.com&gt;<br />
 &nbsp; Marketing stuff &lt;marketing@yourbussines.com,marketing2@yourbussines.com&gt;<br />
 You can also let the email address alone (without the name part and without brackets),
 but in this case the user will see the email address if he is allowed to choose the recipient of the email.',
 
        'txt_success_email_to'        => '&quot;To&quot; Address',
        'txt_email_fromname_field'        => '&quot;From&quot; Name',
        'txt_success_email_fromname'        => '&quot;From&quot; Name',
        'txt_email_from_field'        => '&quot;From&quot; Address',
        'txt_success_email_from'        => '&quot;From&quot; Address',
        'txt_success_page'        => 'Following page',
        'des_success_page'        => 'The "Following page" will sent to the browser after the form has been filled out without any failure.<br />
If no "Following page" is set, the text specified in the next field will be sent.
',
        'txt_success_text'        => 'Success text',
        'des_success_text'        => 'If no "Following page" is set, this text will be shown after the form has been filled out without any failure.',
        'txt_submissions_text'        => 'Data transfer to mpform_submissions table',
        'des_submissions_text'        => 'this text will be written to the field <b>body</b> of the mpform_submissions table.',
        'success_options'        => 'The following place holders are possible:<br />
 &nbsp; {DATA} - all data entered in the form<br />
 &nbsp; {REFERER} - if known, the page from where the form has been called<br />
 &nbsp; {IP} - the IP address of the sender<br />
 &nbsp; {DATE} - date and time<br />
 &nbsp; {USER} - name of the logged on user<br />
',
        'des_success_email_text'        => 'Content of the confirmation email to the sender',
        'des_email_text'        => 'Content of the email to the site owner',
        'fieldset_start'        => 'Fieldset (start)',
        'fieldset_end'        => 'Fieldset (end)',
        'integer_number'        => 'Integer value',
        'decimal_number'        => 'Decimal value',
        'email_recip'        => 'E-Mail Recipient',
        'email_subj'        => 'E-Mail Subject (suffix)',
        'module_name'        => 'Multi Purpose Form',
        'TXT_SUBMIT'        => 'Submit Form',
        'HTML'        => "HTML-Code",
        'entry'        => "Entry is",
        'compulsory_entry'        => "mandatory",
        'optional_entry'        => "optional",
        'ro_entry'        => "read only",
        'des_field_loop'        => 'The following place holders are possible:<br />
 &nbsp; {CLASSES} - css class (dependent from field type and error status)<br />
 &nbsp; {TITLE} - title of the form field<br />
 &nbsp; {FIELD} - the field itself<br />
 &nbsp; {REQUIRED} - mark for mandatory fields<br />
 &nbsp; {HELP} - hidden help text, requires Javascript<br />
 &nbsp; {HELPTXT} - help text always visible<br />
 &nbsp; {ERRORTEXT} - error text (only for certain field types)<br />',
        'des_footer'        => 'The following place holder is possible:<br />
 &nbsp; {SUBMIT} - Submit button',
 'TXT_MODIFY_FIELD'        => 'Edit Field (ID: %s)',
 'TXT_ADD_FIELD'        => 'Add Field',
 'TXT_SETTINGS'                => 'General Options',
 'TXT_EDIT_CSS'                => 'CSS Settings'    
);

$LANG['backend_adv'] = array(
        'adv_settings'        => 'Advanced Settings',
        'CAPTCHA_CONTROL_HINT'        => 'Captcha choice and settings can be performed with the Admin-Tool "Captcha-Control"',
        'TXT_NOTIFICATION_SETTINGS'        => 'Field Formatting for Notifications',
        'txt_heading_html'        => 'Headings',
        'des_heading_html'        => 'Used for type: Heading<br />Place holder: {HEADING}',
        'txt_short_html'        => 'Single row input fields',
        'des_short_html'        => 'Used for types: Short Text, Radio button group, E-Mail Recipient, E-Mail Subject, Date, Number<br />Place holders: {TITLE} and {DATA}',
        'txt_long_html'        => 'Potentially multi row input fields',
        'des_long_html'        => 'Used for types: Long Text, Checkbox group, Select box<br />Place holders: {TITLE} and {DATA}',
        'txt_email_html'        => 'E-Mail Address',
        'des_email_html'        => 'Used for type: E-Mail Address<br />Place holders: {TITLE} and {DATA}',
        'txt_uploadfile_html'        => 'Upload File',
        'des_uploadfile_html'        => 'Used for type: Upload File<br />Place holders: {TITLE} and {DATA}',
        'SPECIAL_SETTINGS'        => 'Special Settings',
        'txt_enum'        => 'Start value for Radio and Checkbox',
        'des_enum'        => 'Default behaviour: If this field is empty, Radio buttons and checkboxes return the visible text.<br />
 If this field contains a character (useful characters might be, for instance, 0, 1 or a), then this value will be incremented for every option and returned instead of the text.',
        'TXT_DATE_FORMAT' => "Date format",
        'TXT_ASP_SETTINGS'        => 'SPAM Protection', 
         'des_date_format'        => 'Bash Date Format (eg. %m/%d/%Y)',
        'TXT_UPLOAD_FILE_FOLDER' => "Upload file folder",
        'TXT_UPLOAD_ONLY_EXTS' => "Upload only file type(s)",
        'TXT_UPLOAD_SETTINGS'        => 'Settings for File Upload',
        'TXT_UPLOAD_FILEMASK' => "Permissions for uploaded file",
        'TXT_UPLOAD_DIRMASK' => "Permissions for upload directory",
        'TXT_ATTACH_FILE' => "Attach uploaded file to email",
        'TXT_MAX_FILE_SIZE_KB' => "Max upload file size Kb",
        'date_format'        => '%m/%d/%Y',
        'help_extensions'        => 'file extensions comma separated, eg. pdf,xls',
        'help_filemask'        => 'permissions for uploaded file, eg. 0204',
        'help_dirmask'        => 'permissions for upload directory, eg. 0705'

);

// Text outputs for the frontend
$LANG['frontend'] = array(
        'MAX_FILESIZE' => "Maximum filesize: %d Kilobyte<br />Allowed file types: %s",
        'integer_error'        => 'Integer numbers must be figures only',
        'decimal_error'        => 'Please enter a valid decimal number',
        'err_too_large'        => 'File size is %d byte, only %d byte are allowed!',
        'err_too_large2'        => 'File size is too large, only %d byte are allowed!',
        'err_partial_upload' => 'Only part of the file has been uploaded!',
        'err_no_upload' => 'File has not been uploaded!',
        'err_upload' => 'There was an error uploading the file: %s (%s), please try again!',
        'select' => "Please select...",
        'select_recip' => "You need to select a recipient for the form",
        'REQUIRED_FIELDS' => "Please complete or correct the fields in red color!",
        'INCORRECT_CAPTCHA' => "The verification number (also known as Captcha) that you entered is incorrect.",
        'VERIFICATION' => "Verification code (SPAM protection)"
        
);

?>
