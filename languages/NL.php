<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.32
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2019, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* The file NL.php provides strings in Dutch language. */

// Dutch module description
$module_description = 'Deze module biedt mogelijkheden om krachtige webformulieren te maken. ';

// declare module language array
global $LANG;
$LANG = array();

// Text outputs for the backend
$LANG['backend'] = array(
    'SETTINGS'                    => 'Formulierinstellingen',
    'TXT_TYP'                     => 'Type',
    'TXT_LIST'                    => "Lijst",
    'TXT_DEFAULT'                 => "Standaardwaarde",
    'TXT_ISFOLLOWING'             => "Heeft een voorgaand formulier",
    'des_is_following'            => 'Geeft aan of het formulier onderdeel is  '
                                   . 'van een samengesteld formulier, of niet.<br />'
                                   . 'Voor formulieren die uit een enkele pagina '
                                   . ' bestaan is NEE vereist!',
    'TXT_MP_SETTINGS'             => 'Workflow-instellingen',
    'TXT_LAYOUT_SETTINGS'         => 'Lay-out',
    'EMAIL_SETTINGS_1'            => 'Instellingen voor mail aan BEHEERDER',
    'EMAIL_SETTINGS_2'            => 'Instellingen voor mail aan BEZOEKER',
    'TXT_STATIC'                  => 'Kies een veld, of voer hieronder een tekst in',
    'EMAIL_SUBJECT'               => 'Ingezonden webformulier',
    'EMAIL_SUC_TEXT'              => 'Hartelijk dank voor het inzenden  '
                                   . 'van het formulier op ',
    'EMAIL_SUC_SUBJ'              => 'Uw ingezonden webformulier',
    'VERIFICATION'                => 'Verificatie (anti-spam maatregel)',
    'TXT_COPY_FIELD'              => 'Veld kopi&euml;ren',
    'TXT_ADD_FIELD'               => 'Veld toevoegen',
    'ROWS'                        => 'Hoogte (aantal regels)',
    'TXT_TBLSFX'                  => 'Achtervoegsel voor databasetabel',
    'DES_TXT_TBLSFX'              => 'Maar als u meerdere mpForm-formulieren  '
                                   . 'gebruikt op uw site, is het aan te raden  '
                                   . 'om de resultaten in aparte databasetabellen  '
                                   . 'op te slaan, om te voorkomen dat alle  '
                                   . 'resultaten in dezelfde tabel terechtkomen. '
                                   . 'Set this to DISABLED, if you do not want to have '
                                   . 'any entries stored in the results table. Independently, '
                                   . 'however, they are stored to the submissions table.',
    'textfield'                   => 'Korte tekst (1 regel)',
    'hiddenfield'                 => 'verborgen veld',
    'textarea'                    => 'Lange tekst (meerdere regels)',
    'date'                        => 'Datum',
    'fileupload'                  => 'Bestandsupload',
    'txt_email_to'                => 'E-mailadres(sen) ontvanger(s)',
    'des_email_to'                => 'Plaats niet meer dan 1 ontvanger per regel  '
                                   . 'en gebruik de volgende notatie:<br> '
                                   . '<strong>Algemeen &lt;info@uwbedrijf.nl&gt;<br> '
                                   . 'Support &lt;support@uwbedrijf.nl&gt;<br> '
                                   . 'Marketing &lt;marketing@uwbedrijf.nl, '
                                   . 'marketing2@uwbedrijf.nl&gt;</strong><br> '
                                   . 'U kunt ook gewoon e-mailadressen gebruiken  '
                                   . '(ipv namen met adressen tussen haakjes), '
                                   . 'maar in dat geval zal de bezoeker wel het '
                                   . 'e-mailadres kunnen zien als hij zelf  '
                                   . 'de ontvanger kan kiezen.',
    'txt_success_email_to'        => 'E-mailadres ontvanger',
    'txt_email_fromname_field'    => 'Naam afzender',
    'txt_success_email_fromname'  => 'Naam afzender',
    'txt_email_from_field'        => 'E-mailadres afzender',
    'txt_email_replyto_field'     => 'E-mailadres replyto',
    'des_email_replyto'           => 'Voer een statisch replyto adres. <br/> '
                                   . 'Als dit veld leeg is, wordt hetzelfde adres  '
                                   . 'als het veld wordt gebruikt. <br/> '
                                   . 'Wanneer een veld van het type e-mail wordt '
                                   . 'geselecteerd, wordt dit veld gebruikt in plaats. <br/> '
                                   . 'Als alternatief kan de e-mail van de aangemelde  '
                                   . 'gebruiker automatisch worden geselecteerd.',
    'txt_success_email_from'      => 'E-mailadres afzender',
    'txt_success_page'            => 'Landingspagina na verzenden',
    'des_success_page'            => 'De landingspagina wordt in de browser getoond '
                                   . 'nadat het formulier zonder fouten is '
                                   . 'ingevuld.<br /> '
                                   . 'Als geen landingspagina is ingesteld wordt  '
                                   . 'de in het volgende veld gespecificeerde  '
                                   . 'tekst verzonden.',
    'txt_success_text'            => 'Succestekst',
    'des_success_text'            => 'Als geen landingspagina is ingesteld wordt  '
                                   . 'deze tekst getoond nadat het formulier  '
                                   . 'zonder fouten is ingevuld.',
    'txt_submissions_text'        => 'Gegevens worden opgeslagen in de tabel '
                                   . ' mpform_submissions',
    'des_submissions_text'        => 'Deze tekst wordt in het veld <b>body</b>  '
                                   . 'van de tabel mpform_submissions opgeslagen.',
    'success_options'             => 'De volgende codes zijn mogelijk:<br /> '
                                   . '&nbsp; {DATA} - de gegevens die in het  '
                                   . 'formulier zijn ingevuld<br /> '
                                   . ' &nbsp; {REFERER} - indien bekend,  '
                                   . 'de pagina waarvandaan het formulier is '
                                   . ' opgeroepen<br /> '
                                   . '&nbsp; {IP} - het IP-adres van de afzender<br />'
                                   . '&nbsp; {DATE} - datum en tijd<br />'
                                   . '&nbsp; {USER} - name of the logged on user<br />'
                                   . '&nbsp; {EMAIL} - Email entered in the form or '
                                   . 'the one of the logged on user<br />',
    'des_success_email_text'      => 'Inhoud van de bevestigingsmail aan de afzender',
    'des_email_text'              => 'Inhoud van de e-mail aan de site-eigenaar',
    'des_success_email_css'       => 'CSS van de bevestigingsmail aan de afzender',
    'des_email_css'               => 'CSS van de e-mail aan de site-eigenaar',
    'txt_email_css'               => 'Email Style',
    'txt_success_email_css'       => 'Success Email Style',
    'fieldset_start'              => 'Veldset (start)',
    'fieldset_end'                => 'Veldset (eind)',
    'integer_number'              => 'Numerieke waarde',
    'decimal_number'              => 'Numerieke waarde met decimaal',
    'email_recip'                 => 'E-mailadres ontvanger',
    'email_subj'                  => 'E-mailonderwerp (achtervoegsel)',
    'module_name'                 => 'Multi Purpose Form',
    'TXT_SUBMIT'                  => 'Verzenden',
    'HTML'                        => "HTML-Code",
    'TXT_WHERE_USE_HTML'          => "activated in",
    'TXT_USE_IN_FORM'             => "the form",
    'TXT_USE_IN_SITE_HTML'        => "in the notification to the site owner",
    'TXT_USE_IN_USER_HTML'        => "in the notification to the user",
    'conditional'                 => "conditionally displayed block",
    'dependency'                  => "depending on",
    'entry'                       => "Invoer is",
    'compulsory_entry'            => "verplicht",
    'optional_entry'              => "optioneel",
    'ro_entry'                    => "alleen lezen",
    'disabled_entry'              => "invalide",
    'des_field_loop'              => 'De volgende codes zijn mogelijk:<br />'
                                   . '&nbsp; {CLASSES} - CSS-class (afhankelijk '
                                   . 'van het formulierveld en foutstatus)<br />'
                                   . '&nbsp; {TITLE} - naam van het formulierveld<br />'
                                   . '&nbsp; {FIELD} - het formulierveld zelf<br />'
                                   . '&nbsp; {FORMATTED_FIELD} - laat extra CSS-klassen<br /> '
                                   . '&nbsp; {REQUIRED} - markering voor  '
                                   . 'verplichte velden<br /> '
                                   . '&nbsp; {HELP} - initieel verborgen helptekst '
                                   . '(vereist Javascript om te tonen)<br /> '
                                   . '&nbsp; {HELPTXT} - altijd zichtbare helptekst<br /> '
                                   . '&nbsp; {ERRORTEXT} - fouttekst  '
                                   . '(alleen voor bepaalde veldtypen)<br />'
                                   . '&nbsp; {TEMPLATE} - inhoud van het veld sjabloon '
                                   . 'voor het huidige veld, {TEMPLATE1} voor de eerste lijn, '
                                   . '{TEMPLATE2} de tweete...<br />',

    'des_field_template'          => 'in het veld lus wordt deze geplaatst is voor '
                                   . 'de plaats houder {TEMPLATE}. '
                                   . 'De volgende codes zijn mogelijk:<br />'
                                   . '&nbsp; {CLASSES} - CSS-class (afhankelijk '
                                   . 'van het formulierveld en foutstatus)<br />'
                                   . '&nbsp; {TITLE} - naam van het formulierveld<br />'
                                   . '&nbsp; {FIELD} - het formulierveld zelf<br />'
                                   . '&nbsp; {FORMATTED_FIELD} - laat extra CSS-klassen<br /> '
                                   . '&nbsp; {REQUIRED} - markering voor  '
                                   . 'verplichte velden<br /> '
                                   . '&nbsp; {HELP} - initieel verborgen helptekst '
                                   . '(vereist Javascript om te tonen)<br /> '
                                   . '&nbsp; {HELPTXT} - altijd zichtbare helptekst<br /> '
                                   . '&nbsp; {ERRORTEXT} - fouttekst  '
                                   . '(alleen voor bepaalde veldtypen)<br />',
    'txt_extraclasses'            => 'CSS-klassen',
    'des_extraclasses'            => 'deze CSS-klassen zijn bovendien toegepast binnenkant van het veld',
    'des_footer'                  => 'De volgende code is mogelijk:<br /> '
                                   . '&nbsp; {SUBMIT} - Verzendknop',
    'TXT_MODIFY_FIELD'            => 'Bewerken veld (ID: %s)',
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
    'adv_settings'                => 'Geavanceerde instellingen',
    'CAPTCHA_CONTROL_HINT'        => 'Captcha choice and settings can be performed '
                                   . 'with the Admin-Tool "Captcha-Control"',
    'TXT_NOTIFICATION_SETTINGS'   => 'Notificatie-instellingen',
    'txt_heading_html'            => 'Titel',
    'des_heading_html'            => 'Gebruikt voor type: Titel<br />Code: {HEADING}',
    'txt_short_html'              => 'Invoer op één regel',
    'des_short_html'              => 'Gebruikt voor typen:  '
                                   . 'Korte tekst,  '
                                   . 'Radiobutton,  '
                                   . 'E-mail ontvanger,  '
                                   . 'E-mailonderwerp,  '
                                   . 'Datum,  '
                                   . 'Nummer<br /> '
                                   . 'Codes: {TITLE}, {DATA}, {CLASSES} en {TEMPLATE}',
    'txt_long_html'               => 'Invoer op meerdere regels',
    'des_long_html'               => 'Gebruikt voor typen:  '
                                   . 'Lange tekst,  '
                                   . 'Aankruisvakje,  '
                                   . 'Keuzemenu<br /> '
                                   . 'Codes: {TITLE}, {DATA}, {CLASSES} en {TEMPLATE}',
    'txt_email_html'              => 'E-mailadres',
    'des_email_html'              => 'Gebruikt voor type:  '
                                   . 'E-mailadres<br /> '
                                   . 'Codes: {TITLE}, {DATA}, {CLASSES} en {TEMPLATE}',
    'txt_uploadfile_html'         => 'Uploaden bestand',
    'des_uploadfile_html'         => 'Gebruikt voor type:  '
                                   . 'Uploaden bestand<br /> '
                                   . 'Codes: {TITLE}, {DATA}, {CLASSES}, {SIZE} en {TEMPLATE}',
    'SPECIAL_SETTINGS'            => 'Speciale instellingen',
    'txt_enum'                    => 'Startwaarde voor Radiobutton en Aankruisvakje',
    'des_enum'                    => 'Standaard gedrag:  '
                                   . 'Als dit veld leeg is tonen de radiobuttons en  '
                                   . 'aankruisvakjes de zichtbare tekst.<br /> '
                                   . 'Als dit veld een cijfer of letter krijgt '
                                   . '(handige cijfers of letters kunnen bijvoorbeeld, '
                                   . '0, 1 of a zijn), dan wordt deze waarde  '
                                   . 'opgehoogd voor elke optie en in plaats  '
                                   . 'van de tekst getoond.',
     'TXT_VALUE_OPTION_SEPARATOR' => "Waarde optie separator touwtje",
    'TXT_DATE_FORMAT'             => "Datumnotatie",
    'TXT_ASP_SETTINGS'            => 'Spam-protectie',
     'des_date_format'            => 'Datumnotatie, bijv. %d-%m-%Y',
    'TXT_UPLOAD_FILE_FOLDER'      => "Upload-folder",
    'TXT_UPLOAD_ONLY_EXTS'        => "Toegestane bestandstype(n)",
    'TXT_UPLOAD_SETTINGS'         => 'Upload-instellingen',
    'TXT_UPLOAD_FILEMASK'         => "Rechten voor geupload bestand",
    'TXT_UPLOAD_DIRMASK'          => "Rechten voor upload-folder",
    'TXT_ATTACH_FILE'             => "Voeg geupload bestand bij e-mail",
    'TXT_MAX_FILE_SIZE_KB'        => "Max. upload bestandsgrootte in Kb",
    'TXT_MULTIPLE_FILES'          =>  "Sta meerdere bestanden per veld toe",
    'date_format'                 => '%d-%m-%Y',
    'help_extensions'             => 'Voer extensies kommagescheiden in,  '
                                   . 'bijv. doc,pdf,xls,jpg,gif,png,tif,bmp.',
    'help_filemask'               => 'Rechten voor geupload bestand, bijv. 0640',
    'help_dirmask'                => 'Rechten voor upload-folder, bijv. 0750',
    'des_stored_submissions'      => 'Set this value to 0 if you do not want to have '
                                   . 'any submissions stored in the database.'
);

// Text outputs for the frontend
$LANG['frontend'] = array(
    'integer_error'               => 'Cijfervelden kunnen alleen cijfers bevatten.',
    'decimal_error'               => 'Geef een juist decimaal nummer',
    'MAX_FILESIZE'                => 'Max. bestandsgrootte: %d Kilobyte<br /> '
                                   . 'Toegestane bestandstypen: %s',
    'err_too_large'               => 'Bestandsgrootte is groter dan de max.  '
                                   . 'grootte van %d bytes!',
    'err_too_large2'              => 'Bestand is te groot, max. %d byte  '
                                   . 'is toegestaan!',
    'err_partial_upload'          => 'Bestand is slechts voor een deel geupload!',
    'err_no_upload'               => 'Bestand is niet geupload!',
    'err_upload'                  => 'Er is een fout opgetreden bij uploaden  '
                                   . 'van bestand %s (%s). Probeer opnieuw svp!',
    'select'                      => "Maak een keuze...",
    'select_recip'                => 'U dient een ontvanger voor het  '
                                   . 'formulier te kiezen!',
    'REQUIRED_FIELDS'             => 'Vul de rood gemarkeerde velden  '
                                   . 'alsnog of juist in!',
    'INCORRECT_CAPTCHA'           => 'Het verificatienummer dat is ingevuld  '
                                   . 'is onjuist.',
    'VERIFICATION'                => "Verification code (SPAM protection)"
);

?>
