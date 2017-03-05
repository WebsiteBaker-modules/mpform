<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.3.8
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
/* The file DE.php provides strings in German language. */

// German module description
$module_description = 'Dieses Modul erm&ouml;glicht es, Formulare f&uuml;r verschiedene Zwecke anzulegen, siehe Hilfedatei.';

// declare module language array
global $LANG;
$LANG = array();

// Text outputs for the backend
$LANG['backend'] = array(
    'SETTINGS'                    => 'Formular-Einstellungen',
    'TXT_TYP'                     => 'Typ',
    'TXT_LIST'                    => "Liste",
    'TXT_DEFAULT'                 => "Vorgabewert",
    'TXT_ISFOLLOWING'             => "Ist ein Folgeformular",
    'des_is_following'            => 'Gibt an, ob es sich um die Erst- oder eine  '
                                   . 'Folgeseite eines mehrseitigen Formulars '
                                   . 'handelt.<br />'
                                   . 'Bei einseitigen Formularen muss  '
                                   . 'Nein eingestellt sein!',
    'TXT_MP_SETTINGS'             => 'Workflow-Einstellungen',
    'TXT_LAYOUT_SETTINGS'         => 'Layout',
    'EMAIL_SETTINGS_1'            => 'E-Mail-Einstellungen (Mail an Sitebetreiber)',
    'EMAIL_SETTINGS_2'            => 'E-Mail-Einstellungen  '
                                   . '(Best&auml;tigungsmail an Formularabsender)',
    'TXT_STATIC_ADDR'             => 'Feste Adresse aus der n&auml;chsten Zeile',
    'TXT_STATIC_NAME'             => 'Fester Name aus der n&auml;chsten Zeile',
    'TXT_USER_ADDR'               => 'Email-Adresse des angemeldeten Benutzers',
    'TXT_USER_NAME'               => 'Name des angemeldeten Benutzers',
    'EMAIL_SUBJECT'               => 'Formularereintragung von der Website...',
    'EMAIL_SUC_TEXT'              => 'Danke f&uuml;r das Ausf&uuml;llen  '
                                   . 'des Formulars auf ',
    'EMAIL_SUC_SUBJ'              => 'Sie haben ein Formular abgeschickt',
    'VERIFICATION'                => 'Pr&uuml;fziffer (SPAM-Schutz)',
    'HELP'                        => 'Hilfe zu',
    'TXT_COPY_FIELD'              => 'Feld kopieren',
    'TXT_ADD_FIELD'               => 'Feld hinzuf&uuml;gen',
    'ROWS'                        => 'Zeilen',
    'TXT_TBLSFX'                  => 'Namensendung f&uuml;r die Ergebnistabelle',
    'DES_TXT_TBLSFX'              => 'Die Ergebnisse von allen Seiten mit der gleichen  '
                                   . 'Namensendung landen in der gleichen Tabelle.',        
    'textfield'                   => 'Textfeld (einzeilig)',
    'hiddenfield'                 => 'verborgenes Feld',
    'textarea'                    => 'Textarea (mehrzeilig)',
    'date'                        => 'Datum',
    'fileupload'                  => 'Datei-Upload',
    'txt_email_to'                => 'Empf&auml;ngeradresse(n)',
    'des_email_to'                => 'Verwenden Sie f&uuml;r jede  '
                                   . 'Empf&auml;ngeradresse eine Zeile,  '
                                   . 'formatiert wie im folgenden Beispiel:<br /> '
                                   . '&nbsp; Abteilung Support '
                                   . '&lt;support@yourbussines.com&gt;<br /> '
                                   . '&nbsp; Abteilung Verkauf  '
                                   . '&lt;marketing@yourbussines.com, '
                                   . 'marketing2@yourbussines.com&gt;<br /> '
                                   . 'Sie k&ouml;nnen auch nur die email-Adresse '
                                   . 'angeben (ohne den Namensteil und ohne die '
                                   . 'Klammern), aber in diesem Fall sieht der '
                                   . 'Benutzer die email-Adresse, falls er den '
                                   . 'Empf&auml;nger w&auml;hlen kann.',
    'txt_success_email_to'        => 'Empf&auml;ngeradresse',
    'txt_email_fromname_field'    => 'Absendername',
    'txt_success_email_fromname'  => 'Absendername',
    'txt_email_from_field'        => 'Absenderadresse',
    'txt_email_replyto_field'     => 'Antwortadresse',
    'des_email_replyto'           => 'Geben Sie hier eine statische  '
                                   . 'Antwort-Adresse an. <br/> '
                                   . 'Wenn dieses Feld leer ist, wird die gleiche '
                                   . 'Adresse wie im Absender-Feld verwendet<br /> '
                                   . 'Wenn ein Feld vom Typ email ausgew&auml;hlt ist, '
                                   . 'wird dieses Feld anstatt der hier eingetragenen '
                                   . ' Adresse verwendet.<br /> '
                                   . 'Alternativ kann die email des angemeldeten '
                                   . 'Benutzers automatisch ausgew&auml;hlt werden.',
    'txt_success_email_from'      => 'Absenderadresse',
    'txt_success_page'            => 'Folgeseite',
    'des_success_page'            => 'Die Folgeseite wird aufgerufen, nachdem das  '
                                   . 'Formular fehlerfrei ausgef&uuml;llt wurde.<br /> '
                                   . 'Falls keine Folgeseite angegeben wurde,  '
                                   . 'wird der Text ausgegeben,  '
                                   . 'der im folgenden Feld festgelegt ist.',
    'txt_success_text'            => 'Dankestext',
    'des_success_text'            => 'Falls keine Folgeseite angegeben wurde,  '
                                   . 'wird dieser Text angezeigt, nachdem das  '
                                   . 'Formular fehlerfrei ausgef&uuml;llt wurde.',
    'txt_submissions_text'        => 'Daten&uuml;bergabe an '
                                   . 'mpform_submissions-Tabelle',
    'des_submissions_text'        => 'Dieser Text wird in das Feld <b>body</b>'
                                   . 'der mpform_submissions-Tabelle geschrieben.',
    'success_options'             => 'Folgende Platzhalter sind m&ouml;glich:<br />'
                                   . '&nbsp; {DATA} - die eingegebenen Daten<br />'
                                   . '&nbsp; {REFERER} - falls bekannt, die Seite, '
                                   . 'von der aus das Formular aufgerufen wurde<br />'
                                   . '&nbsp; {IP} - die IP-Adresse des Absenders<br /> '
                                   . '&nbsp; {DATE} - Datum und Uhrzeit<br />'
                                   . '&nbsp; {USER} - Name des '
                                   . 'angemeldeten Benutzers<br />'
                                   . '&nbsp; {EMAIL} - eingegebene Email oder die '
                                   . 'des angemeldeten Benutzers<br />',
    'des_success_email_text'      => 'Inhalt der Best&auml;tigungs-email  '
                                   . 'an den Absender',
    'des_success_email_css'       => 'CSS f&uuml;r die Best&auml;tigungs-email  '
                                   . 'an den Absender',
    'des_email_css'               => 'CSS f&uuml;r die email an den Sitebetreiber',
    'txt_email_css'               => 'Email Style',
    'txt_success_email_css'       => 'Success Email Style',
    'des_email_text'              => 'Inhalt der email an den Sitebetreiber',
    'fieldset_start'              => 'Fieldset (Start)',
    'fieldset_end'                => 'Fieldset (Ende)',
    'integer_number'              => 'Ganze Zahl',
    'decimal_number'              => 'Dezimalzahl',
    'email_recip'                 => 'E-Mail-Empf&auml;nger',
    'email_subj'                  => 'E-Mail-Betreff (Suffix)',
    'module_name'                 => 'Mehrzweckformular',
    'TXT_SUBMIT'                  => 'Formular absenden',
    'HTML'                        => "HTML-Code",
    'TXT_WHERE_USE_HTML'          => "aktiviert unter",
    'TXT_USE_IN_FORM'             => "im Formular",
    'TXT_USE_IN_SITE_HTML'        => "in der Benachrichtigung an den Seitenbetreiber",
    'TXT_USE_IN_USER_HTML'        => "in der Benachrichtigung an den Benutzer",
    'conditional'                 => "bedingt angezeigter Block",
    'dependency'                  => "in Abh&auml;ngigkeit von",
    'entry'                       => "Eingabetyp",
    'compulsory_entry'            => "Pflichtfeld",
    'optional_entry'              => "freiwillig",
    'ro_entry'                    => "nicht ver&auml;nderbar",
    'disabled_entry'              => "deaktiviert",
    'des_field_loop'              => 'Folgende Platzhalter sind m&ouml;glich:<br /> '
                                   . '&nbsp; {CLASSES} - css-Klasse  '
                                   . '(abh&auml;ngig von Feldtyp und Fehlerstatus)<br />'
                                   . '&nbsp; {TITLE} - Bezeichnung des  '
                                   . 'Formularfeldes<br /> '
                                   . '&nbsp; {FIELD} - Das Formularfeld<br /> '
                                   . '&nbsp; {FORMATTED_FIELD} - erlaubt zus&auml;tzliche CSS Klassen<br /> '
                                   . '&nbsp; {REQUIRED} - Markierung f&uuml;r '
                                   . 'Pflichtfelder<br /> '
                                   . '&nbsp; {HELP} - Hilfetext verdeckt,  '
                                   . 'Javascript erforderlich<br /> '
                                   . '&nbsp; {HELPTXT} - Hilfetext immer sichtbar<br /> '
                                   . '&nbsp; {ERRORTEXT} - Fehlertext  '
                                   . '(nur bei bestimmten Feldtypen)<br /> '
                                   . '&nbsp; {TEMPLATE} - Inhalt des '
                                   . 'Feldtemplate f&uuml;r das jeweilige Feld, '
                                   . '{TEMPLATE1} f&uuml;r die erste Zeile, '
                                   . '{TEMPLATE2} f&uuml;r die zweite.... <br />',
    'des_field_template'          => 'dies wird in der Feldschleife f&uuml;r den Platzhalter {TEMPLATE} '
                                   . 'eingesetzt, folgende Platzhalter sind m&ouml;glich:<br /> '
                                   . '&nbsp; {CLASSES} - css-Klasse  '
                                   . '(abh&auml;ngig von Feldtyp und Fehlerstatus)<br />'
                                   . '&nbsp; {TITLE} - Bezeichnung des  '
                                   . 'Formularfeldes<br /> '
                                   . '&nbsp; {FIELD} - Das Formularfeld<br /> '
                                   . '&nbsp; {FORMATTED_FIELD} - erlaubt zus&auml;tzliche CSS Klassen<br /> '
                                   . '&nbsp; {REQUIRED} - Markierung f&uuml;r '
                                   . 'Pflichtfelder<br /> '
                                   . '&nbsp; {HELP} - Hilfetext verdeckt,  '
                                   . 'Javascript erforderlich<br /> '
                                   . '&nbsp; {HELPTXT} - Hilfetext immer sichtbar<br /> '
                                   . '&nbsp; {ERRORTEXT} - Fehlertext  '
                                   . '(nur bei bestimmten Feldtypen)<br />',                                   
    'txt_extraclasses'            => 'CSS Klassen',
    'des_extraclasses'            => 'diese CSS Klassen werden zus&auml;thlich innerhalb des Feldes verwendet',
    'des_footer'                  => 'Folgender Platzhalter ist m&ouml;glich:<br /> '
                                   . '&nbsp; {SUBMIT} - Abschicken-Button',
    'TXT_MODIFY_FIELD'            => 'Feld (ID: %s) bearbeiten',
    'TXT_ADD_FIELD'               => 'Feld hinzuf&uuml;gen',
    'TXT_SETTINGS'                => 'Allgemeine Optionen',
    'TXT_EDIT_CSS'                => 'CSS-Einstellungen',
    'TXT_EXPORT_FORM'             => 'Formular exportieren',
    'TXT_EXPORT_SUBMISSIONS'      => 'Eintragungen exportieren',
    'TXT_IMPORT_FORM'             => 'Formular Importieren',
    'txt_import_err_wrong_module' => 'Import ist nur f&uuml;r '
                                   . 'Modul Typ &quot;mpform&quot; unterst&uuml;tzt',
    'txt_import_err_not_empty'    => 'Es gibt bereits Formularfelder in diesem Formular.  '
                                   . 'Import ist nur f&uuml;r leere Abschnitte unterst&uuml;tzt.',
    'txt_import_warning'          => 'Warnung: Formulareintragungen werden beim Import/Export '
                                   . 'nicht mit &uuml;bernommen.',
    'txt_you_have_selected'       => 'Sie haben ausgew&auml;hlt:',
    'des_conditional_div'         => "Generierter Code - Sie wollen wahrscheinlich den Inhalt der \n"
                                   . "     div Abschnitte &auml;ndern und Sie k&ouml;nnten das \n"
                                   . "     schlie&szlig;ende div-Tag und alles was folgt zu einem anderen\n"
                                   . "     HTML-Abschnitt verschieben. Wenn Sie die Optionen von %s\n"
                                   . "     &auml;ndern m&ouml;chten, m&uuml;ssen Sie den Code manuell\n"
                                   . "     anpassen (oder Sie starten von vorne indem Sie den Feldtyp\n"
                                   . "     zur&uuml;ck auf bedingt angezeigter Block setzen)"                                   
);

$LANG['backend_adv'] = array(
    'adv_settings'                => 'Erweiterte Optionen',
    'CAPTCHA_CONTROL_HINT'        => 'Captcha-Auswahl und Einstellungen k&ouml;nnen  '
                                   . 'global, mit dem Admin-Tool "Captcha-Control" '
                                   . 'eingestellt werden.',
    'TXT_NOTIFICATION_SETTINGS'   => 'Feldformatierungen f&uuml;r Benachrichtigungen',
    'txt_heading_html'            => '&Uuml;berschriften',
    'des_heading_html'            => 'Wird verwendet f&uuml;r Typ:  '
                                   . '&Uuml;berschrift<br /> '
                                   . 'Platzhalter: {HEADING}',
    'txt_short_html'              => 'Einzeilige Eingaben',
    'des_short_html'              => 'Wird verwendet f&uuml;r Typen:  '
                                   . 'Kurztext,  '
                                   . 'Optionsfeld,  '
                                   . 'E-Mail-Empf&auml;nger,  '
                                   . 'E-Mail-Betreff,  '
                                   . 'Datum,  '
                                   . 'Zahl<br /> '
                                   . 'Platzhalter: {TITLE}, {DATA} und {TEMPLATE}',
    'txt_long_html'               => 'Potentiell mehrzeilige Eingaben',
    'des_long_html'               => 'Wird verwendet f&uuml;r Typen:  '
                                   . 'Langtext,  '
                                   . 'Kontrollk&auml;stchen,  '
                                   . 'Auswahlliste<br /> '
                                   . 'Platzhalter: {TITLE}, {DATA} und {TEMPLATE}',
    'txt_email_html'              => 'E-Mail-Adresse',
    'des_email_html'              => 'Wird verwendet f&uuml;r Typ:  '
                                   . 'E-Mail-Adresse<br /> '
                                   . 'Platzhalter: {TITLE}, {DATA} und {TEMPLATE}',
    'txt_uploadfile_html'         => 'Datei-Upload',
    'des_uploadfile_html'         => 'Wird verwendet f&uuml;r Typ:  '
                                   . 'Datei-Upload<br /> '
                                   . 'Platzhalter: {TITLE}, {DATA}, {SIZE} und {TEMPLATE}',
    'SPECIAL_SETTINGS'            => 'Spezielle Einstellungen',
    'txt_enum'                    => 'Startwert f&uuml;r Radio und Checkbox',
    'des_enum'                    => 'Standardverhalten:  '
                                   . 'Optionsfelder und Kontrollk&auml;stchen '
                                   . '&uuml;bergeben den angezeigten Text,  '
                                   . 'wenn dieses Feld leer bleibt.<br /> '
                                   . 'Falls hier ein Zeichen eingetragen wird '
                                   . '(sinnvoll k&ouml;nnten z.B. 0, 1 oder a sein), '
                                   . 'wird dieser Wert f&uuml;r jede Option '
                                   . 'hochgez&auml;hlt und dann statt des Textes '
                                   . 'zur&uuml;ckgegeben.',
    'TXT_VALUE_OPTION_SEPARATOR' => "Value-Option Trenn-String",
    'TXT_DATE_FORMAT'            => "Datumsformat",
    'TXT_ASP_SETTINGS'           => 'SPAM-Schutz',
    'des_date_format'            => 'Bash Date Format (z.B. %d.%m.%Y)',
    'TXT_UPLOAD_FILE_FOLDER'     => "Dateiuploadverzeichnis",
    'TXT_UPLOAD_ONLY_EXTS'       => "Akzeptierte Dateitypen",
    'TXT_UPLOAD_SETTINGS'        => 'Einstellungen f&uuml;r Datei-Upload',
    'TXT_UPLOAD_FILEMASK'        => "Zugriffsrechte f&uuml;r hochgeladene Datei",
    'TXT_UPLOAD_DIRMASK'         => "Zugriffsrechte f&uuml;r Upload-Verzeichnis",
    'TXT_ATTACH_FILE'            => "Versende die Datei per Mail",
    'TXT_MAX_FILE_SIZE_KB'       => "Max Dateigr&ouml;sse f&uuml;r den Upload Kb",
    'date_format'                => '%d.%m.%Y',
    'help_extensions'            => 'Dateierweiterungen, getrennt durch Komma,  '
                                   . 'z.B.: pdf,xls',
    'help_filemask'              => 'Berechtigungen f&uuml;r hochgeladene Dateien, '
                                   . ' z.B.: 0640',
    'help_dirmask'               => 'Berechtigungen f&uuml;r Upload-Verzeichnis,  '
                                   . 'z.B.: 0750'
);

// Text outputs for the frontend
$LANG['frontend'] = array(
    'MAX_FILESIZE'               => 'Maximale Dateigr&ouml;sse:  '
                                   . '%d Kilobyte<br />Erlaubte Dateitypen: %s',
    'integer_error'              => 'Ganze Zahlen d&uuml;rfen nur  '
                                   . 'aus Ziffern bestehen',
    'decimal_error'              => 'Bitte geben Sie eine g&uuml;ltige  '
                                   . 'Dezimalzahl ein',
    'err_too_large'              => 'Dateigr&ouml;&szlig;e ist %d Byte,  '
                                   . 'erlaubt sind nur %d Byte!',
    'err_too_large2'             => 'Datei ist zu gro&szlig;,  '
                                   . 'erlaubt sind nur %d Byte!',
    'err_partial_upload'         => 'Nur ein Teil der Datei wurde hochgeladen!',
    'err_no_upload'              => 'Die Datei wurde nicht hochgeladen!',
    'err_upload'                 => 'Fehler beim Hochladen der Datei:  '
                                   . '%s (%s), bitte versuchen Sie es nochmals!',
    'select'                     => 'Bitte ausw&auml;hlen...',
    'select_recip'               => 'Sie m&uuml;ssen einen Empf&auml;nger  '
                                   . 'f&uuml;r das Formular ausw&auml;hlen',
    'REQUIRED_FIELDS'            => 'Bitte die rot markierten Angaben  '
                                   . 'erg&auml;nzen bzw. korrigieren!',
    'INCORRECT_CAPTCHA'          => 'Die eingegebene Pr&uuml;fziffer  '
                                   . 'ist nicht korrekt.',
    'VERIFICATION'               => 'Pr&uuml;fcode (SPAM-Schutz)'
);
