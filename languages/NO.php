<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.12
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2017, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @url                 https://forum.wbce.org/viewtopic.php?id=661
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/

/* The file NO.php provides strings in Norwegian language.
 * Filen NO.php inneholder Norske spr�k strenger.
 * Oversatt til Norsk av/ Translated ti Norwegian by: Odd Egil Hansen (oeh)
*/

// declare module language array
global $LANG;
$LANG = array();

// Text outputs for the backend
$LANG['backend'] = array(
    'SETTINGS'                    => 'Skjema innstillinger',
    'TXT_TYP'                     => 'Type',
    'TXT_LIST'                    => 'Vis', //List *uklar
    'TXT_DEFAULT'                 => 'Standard verdi',
    'TXT_ISFOLLOWING'             => 'Har etterf&oslash;lgende skjema',
                                    //Has a predecessing form", *uklar
    'des_is_following'            => 'Anngir hvor vidt dette er f&oslash;rste side  '
                                   . 'av et skjema med flere sider eller ikke.<br /> '
                                   . 'For forms consisting of a single page  '
                                   . 'No is required!',
    'TXT_MP_SETTINGS'             => 'Workflow Innstillinge',
    'TXT_LAYOUT_SETTINGS'         => 'Layout',
    'EMAIL_SETTINGS_1'            => 'Innstillinge for e-post'
                                   . '(e-post til nettstedets eier)',
    'EMAIL_SETTINGS_2'            => 'Innstillinge for e-post(e-post til bruker)',
    'TXT_STATIC'                  => 'Statisk adresse deffineres p&aring; neste '
                                   . 'linje',
                                    //Static address defined in the next line', *uklar
    'EMAIL_SUBJECT'               => 'Resultat fra skjema p&aring; nettstedet...',
                                    //Results from form on website...', *uklar
    'EMAIL_SUC_TEXT'              => 'Vi takker s&aring; mye for at du sendte  '
                                   . 'inn skjemaet p&aring;', // *uklar
    'EMAIL_SUC_SUBJ'              => 'Du har sendt inn et skjema',
    'VERIFICATION'                => 'Verifisering (SPAM beskyttelse)',
    'TXT_COPY_FIELD'              => 'Kopier felt',
    'TXT_ADD_FIELD'               => 'Legg til felt',
    'ROWS'                        => 'Rader',
    'TXT_TBLSFX'                  => 'Endelse for resultat tabellen(_skjema01)',
    'DES_TXT_TBLSFX'              => 'Resultatene fra skjema med samme endelse  '
                                   . 'ender i den samme tabellen.',
    'textfield'                   => 'Tekst felt (en linje)',
    'hiddenfield'                 => 'skjult felt',
    'textarea'                    => 'Tekst felt (flere linjer)',
    'date'                        => 'Dato',
    'fileupload'                  => 'Filopplasting',
    'txt_email_to'                => '&quot;Til&quot; Adresse(r)',
    'des_email_to'                => 'Legg kun inn en &quot;Til&quot; adresse per  '
                                   . 'linje, formatert som i eksemplet under:<br /> '
                                   . '&nbsp; Support staff '
                                   . '&lt;support@yourbussines.com&gt;<br /> '
                                   . '&nbsp; Marketing staff '
                                   . '&lt;marketing@yourbussines.com,'
                                   . 'marketing2@yourbussines.com&gt;<br /> '
                                   . 'Du kan ogs&aring; legge inn bare e-post  '
                                   . 'adressen (men da uten < foran og > etter), '
                                   . 'men det vil da v&aelig;re mulig for brukeren '
                                   . '&aring; se e-post adressen, hvis brukeren kan '
                                   . ' velge mottaker for e-posten.',
    'txt_success_email_to'        => '&quot;Til&quot; Adresse',
    'txt_email_fromname_field'    => '&quot;Fra&quot; Navn',
    'txt_success_email_fromname'  => '&quot;Fra&quot; Navn',
    'txt_email_from_field'        => '&quot;Fra&quot; e-post adresse', // *uklar
    'txt_email_replyto_field'     => '&quot;Svar til&quot; e-post adresse', // *uklar
    'des_email_replyto'           => 'Skriv inn en statisk Svar til-adresse. <br/> '
                                   . 'Hvis dette feltet er tomt, er den samme  '
                                   . 'adressen som Fra-feltet brukes. <br/> '
                                   . 'Når et felt av typen e er valgt,  '
                                   . 'blir dette feltet brukes i stedet. <br/> '
                                   . 'Alternativt kan e-post av den påloggede '
                                   . 'brukeren automatisk bli valgt.', // *uklar
    'txt_success_email_from'      => '&quot;Fra&quot; e-post adresse', // *uklar
    'txt_success_page'            => 'Neste side',
    'des_success_page'            => 'The "Following page" will sent to the  '
                                   . 'browser after the form has been filled  '
                                   . 'out without any failure.<br /> '
                                   . 'If no "Following page" is set, the text '
                                   . 'specified in the next field will be sent.',
    'txt_success_text'            => 'Success text',
    'des_success_text'            => 'If no "Following page" is set, this text will '
                                   . 'be shown after the form has been filled out '
                                   . ' without any failure.',
    'txt_submissions_text'        => 'Data transfer to mpform_submissions table',
    'des_submissions_text'        => 'this text will be written to the field '
                                   . '<b>body</b> of the mpform_submissions table.',
    'success_options'             => 'The following place holders are possible:<br /> '
                                   . '&nbsp; {DATA} - all data entered in the form<br />'
                                   . '&nbsp; {REFERER} - if known, the page from  '
                                   . 'where the form has been called<br /> '
                                   . '&nbsp; {IP} - the IP address of the sender<br />'
                                   . '&nbsp; {DATE} - date and time<br />'
                                   . '&nbsp; {USER} - name of the logged on user<br />'
                                   . '&nbsp; {EMAIL} - Email entered in the form or '
                                   . 'the one of the logged on user<br />',
    'des_success_email_text'      => 'Content of the confirmation email  '
                                   . 'to the sender',
    'des_email_text'              => 'Content of the email to the site owner',
    'des_success_email_css'       => 'CSS for the confirmation email to the sender',
    'des_email_css'               => 'CSS for the email to the site owner',
    'txt_email_css'               => 'Email Style',
    'txt_success_email_css'       => 'Success Email Style',
    'fieldset_start'              => 'Feltomr&aring;de  (start)',
    'fieldset_end'                => 'Feltomr&aring;de (slutt)',
    'integer_number'              => 'Heltalls verdi ',
    'decimal_number'              => 'Desimal verdi ',
    'email_recip'                 => 'e-post mottaker',
    'email_subj'                  => 'e-post vedr&oslash;rende (endelse)', // *uklar
    'module_name'                 => 'mpForm',
    'TXT_SUBMIT'                  => 'Submit Form',
    'HTML'                        => "HTML-Code",
    'TXT_WHERE_USE_HTML'          => "activated in",
    'TXT_USE_IN_FORM'             => "the form",
    'TXT_USE_IN_SITE_HTML'        => "in the notification to the site owner",
    'TXT_USE_IN_USER_HTML'        => "in the notification to the user",
    'conditional'                 => "conditionally displayed block",
    'dependency'                  => "depending on",
    'entry'                       => "Entry is",
    'compulsory_entry'            => "compulsory",
    'optional_entry'              => "optional",
    'ro_entry'                    => "read only",
    'disabled_entry'              => "deaktivert",
    'des_field_loop'              => 'The following place holders are possible:<br /> '
                                   . '&nbsp; {CLASSES} - css class  '
                                   . '(dependent from field type and error status)<br /> '
                                   . '&nbsp; {TITLE} - title of the form field<br /> '
                                   . '&nbsp; {FIELD} - the field itself<br /> '
                                   . '&nbsp; {FORMATTED_FIELD} - allows additional CSS classes<br /> '
                                   . '&nbsp; {REQUIRED} - mark for mandatory fields<br /> '
                                   . '&nbsp; {HELP} - hidden help text,  '
                                   . 'requires Javascript<br /> '
                                   . '&nbsp; {HELPTXT} - help text always visible<br /> '
                                   . '&nbsp; {ERRORTEXT} - error text  '
                                   . '(only for certain field types)<br />'
                                   . '&nbsp; {TEMPLATE} - content of the field template '
                                   . 'for the current field, {TEMPLATE1} for the first '
                                   . 'line, {TEMPLATE2} the second one...<br />',
    'des_field_template'          => 'in the field loop this is inserted for the place holder {TEMPLATE}. '
                                   . 'The following place holders are possible:<br /> '
                                   . '&nbsp; {CLASSES} - css class  '
                                   . '(dependent from field type and error status)<br /> '
                                   . '&nbsp; {TITLE} - title of the form field<br /> '
                                   . '&nbsp; {FIELD} - the field itself<br /> '
                                   . '&nbsp; {FORMATTED_FIELD} - allows additional CSS classes<br /> '
                                   . '&nbsp; {REQUIRED} - mark for mandatory fields<br /> '
                                   . '&nbsp; {HELP} - hidden help text,  '
                                   . 'requires Javascript<br /> '
                                   . '&nbsp; {HELPTXT} - help text always visible<br /> '
                                   . '&nbsp; {ERRORTEXT} - error text  '
                                   . '(only for certain field types)<br />',
    'txt_extraclasses'            => 'CSS classes',
    'des_extraclasses'            => 'these CSS classes are additionally applied inside of the field',
    'des_footer'                  => 'The following place holder is possible:<br /> '
                                   . '&nbsp; {SUBMIT} - Submit button',
    'TXT_MODIFY_FIELD'            => 'Edit Field (ID: %s)',
    'TXT_ADD_FIELD'               => 'Add Field',
    'TXT_SETTINGS'                => 'General Options',
    'TXT_EDIT_CSS'                => 'CSS Settings',
    'TXT_EXPORT_FORM'             => 'Export form',
    'TXT_EXPORT_SUBMISSIONS'      => 'Export submissions',
    'TXT_IMPORT_FORM'             => 'Import form',
    'txt_import_err_wrong_module' => 'only import of module type &quot;mpform&quot; is supported',
    'txt_import_err_not_empty'    => 'there are already some fields in this form. '
                                   . 'Import is only supported for empty sections',
    'txt_import_warning'          => 'Warning: Submissions are not transferred during import/export',
    'txt_you_have_selected'       => 'You have selected',
    'des_conditional_div'         => "generated code - you probably want to change the content\n"
                                   . "     of the div sections and you might want to move the closing\n"
                                   . "     div-tag and everything that follows to another html section.\n"
                                   . "     If you change the options of %s you have to\n"
                                   . "     update this generated code manually (or you start again by\n"
                                   . "     changing the type back to conditionally display block)"
);

$LANG['backend_adv'] = array(
    'adv_settings'                => 'Advanced Settings',
    'CAPTCHA_CONTROL_HINT'        => 'Captcha choice and settings can be  '
                                   . 'performed with the Admin-Tool '
                                   . ' "Captcha-Control"',
    'TXT_NOTIFICATION_SETTINGS'   => 'Field Formatting for Notifications',
    'txt_heading_html'            => 'Headings',
    'des_heading_html'            => 'Used for type: Heading<br /> '
                                   . 'Place holder: {HEADING}',
    'txt_short_html'              => 'Single row input fields',
    'des_short_html'              => 'Used for types:  '
                                   . 'Short Text,  '
                                   . 'Radio button group,  '
                                   . 'E-Mail Recipient,  '
                                   . 'E-Mail Subject,  '
                                   . 'Date,  '
                                   . 'Number<br /> '
                                   . 'Place holders: {TITLE}, {DATA}, {CLASSES} and {TEMPLATE}',
    'txt_long_html'               => 'Potentially multi row input fields',
    'des_long_html'               => 'Used for types:  '
                                   . 'Long Text,  '
                                   . 'Checkbox group,  '
                                   . 'Select box<br /> '
                                   . 'Place holders: {TITLE}, {DATA}, {CLASSES} and {TEMPLATE}',
    'txt_email_html'              => 'E-Mail Address',
    'des_email_html'              => 'Used for type:  '
                                   . 'E-Mail Address<br /> '
                                   . 'Place holders: {TITLE}, {DATA}, {CLASSES} and {TEMPLATE}',
    'txt_uploadfile_html'         => 'Upload File',
    'des_uploadfile_html'         => 'Used for type:  '
                                   . 'Upload File<br /> '
                                   . 'Place holders: {TITLE}, {DATA}, {CLASSES}, {SIZE} and {TEMPLATE}',
    'SPECIAL_SETTINGS'            => 'Special Settings',
    'txt_enum'                    => 'Start value for Radio and Checkbox',
    'des_enum'                    => 'Default behaviour: If this field is empty,  '
                                   . 'Radio buttons and checkboxes return  '
                                   . 'the visible text.<br /> '
                                   . 'If this field contains a character  '
                                   . '(useful characters might be, for instance,  '
                                   . '0, 1 or a), then this value will be  '
                                   . 'incremented for every option and  '
                                   . 'returned instead of the text.',
    'TXT_VALUE_OPTION_SEPARATOR'  => "Verdi opsjon separator string",
    'TXT_DATE_FORMAT'             => 'Dato format',
    'TXT_ASP_SETTINGS'            => 'SPAM Protection',
     'des_date_format'            => 'Bash Date Format (eg. %m/%d/%Y)',
    'TXT_UPLOAD_FILE_FOLDER'      => 'Folder for opplastede filer',
    'TXT_UPLOAD_ONLY_EXTS'        => 'Last kun opp filtypen(e)',
    'TXT_UPLOAD_SETTINGS'         => 'Settings for File Upload',
    'TXT_UPLOAD_FILEMASK'         => 'Rettigheter for opplastede fil',
    'TXT_UPLOAD_DIRMASK'          => 'Permissions for upload directory',
    'TXT_ATTACH_FILE'             => 'Legg ved opplastede fil i e-post',
    'TXT_MAX_FILE_SIZE_KB'        => 'Maksimal st&oslash;rrelse for fil opplasting i Kb',
    'date_format'                 => '%m/%d/%Y',
    'help_extensions'             => 'file extensions comma separated, eg. pdf,xls',
    'help_filemask'               => 'permissions for uploaded file, eg. 0640',
    'help_dirmask'                => 'permissions for upload directory, eg. 0750'

);

// Text outputs for the frontend
$LANG['frontend'] = array(
    'MAX_FILESIZE'                => 'Maksimal fil st&oslash;rrelse:  '
                                   . '%d kilobyte<br />Allowed file types: %s',
    'integer_error'               => 'Heltall kan bare v&aelig;re tall (1,2,3 osv)',
    'decimal_error'               => 'Please enter a valid decimal number',
    'err_too_large'               => 'File size is %d byte,  '
                                   . 'only %d byte are allowed!',
    'err_too_large2'              => 'File size is too large,  '
                                   . 'only %d byte are allowed!',
    'err_partial_upload'          => 'Only part of the file has been uploaded!',
    'err_no_upload'               => 'File has not been uploaded!',
    'err_upload'                  => 'There was an error uploading the file:  '
                                   . '%s (%s), please try again!',
    'select'                      => "Please select...",
    'select_recip'                => "You need to select a recipient for the form",
    'REQUIRED_FIELDS'             => 'Please complete or correct the fields  '
                                   . 'in red color!',
    'INCORRECT_CAPTCHA'           => 'The verification number (also known as  '
                                   . 'Captcha) that you entered is incorrect.',
    'VERIFICATION'                => "Verification code (SPAM protection)"

);
