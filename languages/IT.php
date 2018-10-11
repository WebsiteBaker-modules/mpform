<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.23
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
/* The file IT.php provides strings in Italian language. */

// declare module language array
global $LANG;
$LANG = array();

// uscite di testo per il backend
$LANG['backend'] = array(
    'SETTINGS'                    => 'Impostazioni modulo',
    'TXT_TYP'                     => 'Tipo',
    'TXT_LIST'                    => "Lista",
    'TXT_DEFAULT'                 => "Valore predefinito",
    'TXT_ISFOLLOWING'             => "ha una forma precedente",
    'des_is_following'            => 'Specifica se la pagina &eacute; la prima pagina '
                                   . 'di un modulo di pi&ugave; pagine. <br /> '
                                   . 'Per i moduli composti da una sola pagina '
                                   . '&quot;Non&quot; &eacute; richiesto!',
    'TXT_MP_SETTINGS'             => 'Impostazioni flusso di lavoro',
    'TXT_LAYOUT_SETTINGS'         => 'Layout',
    'EMAIL_SETTINGS_1'            => 'Impostazioni e-mail (posta al proprietario del sito)',
    'EMAIL_SETTINGS_2'            => 'Impostazioni e-mail (mail di conferma utente)',
    'TXT_STATIC_ADDR'             => 'Indirizzo come definito nella riga successiva',
    'TXT_STATIC_NAME'             => 'Nome come definito nella riga successiva',
    'TXT_USER_ADDR'               => 'Indirizzo e-mail del utente connesso',
    'TXT_USER_NAME'               => 'Nome dell&apos;utente connesso',
    'EMAIL_SUBJECT'               => 'I risultati di modulo sul sito ...',
    'EMAIL_SUC_TEXT'              => 'Grazie per presentando il modulo',
    'EMAIL_SUC_SUBJ'              => 'Hai presentato un modulo',
    'VERIFICATION'                => 'di verifica (antispam)',
    'TXT_COPY_FIELD'              => 'Copiare casella',
    'TXT_ADD_FIELD'               => 'Aggiungi casella',
    'ROWS'                        => 'Righe',
    'TXT_TBLSFX'                  => 'suffisso del nome per la tabella dei risultati',
    'DES_TXT_TBLSFX'              => 'I risultati di tutte le pagine con lo stesso suffisso '
                                   . 'vanno nella stessa tabella. Impostalo su DISABLED,  '
                                   . 'se non vuoi che le voci siano memorizzate nella  '
                                   . 'tabella dei risultati. Indipendentemente, tuttavia,  '
                                   . 'vengono memorizzati nella tabella di invio.',
    'textfield'                   => 'Campo di testo (una linea)',
    'hiddenfield'                 => 'Campo nascosto',
    'textarea'                    => 'area di testo (multilinea)',
    'date'                        => 'Data',
    'fileupload'                  => 'Upload File',
    'txt_email_to'                => 'Indirizzo (i)',
    'des_email_to'                => 'mettere un solo indirizzo su una linea, '
                                   . 'formattato come nel seguente esempio:<br />'
                                   . '&nbsp; Personale di supporto '
                                   . '&lt;support@yourbussines.com&gt;<br />'
                                   . '&nbsp; personale di marketing '
                                   . '&lt;marketing@yourbussines.com,'
                                   . 'marketing2@yourbussines.com&gt;<br />'
                                   . 'Si pu&oacute; anche lasciare l&apos; '
                                   . 'indirizzo di posta elettronica da solo '
                                   . '(senza la parte nome e senza parentesi), '
                                   . 'Ma in questo caso l&apos;utente vedr&aacute;  '
                                   . 'l&apos; indirizzo di posta elettronica se gli '
                                   . '&eacute; permesso di scegliere'
                                   . 'il destinatario della e-mail.',
    'txt_success_email_to'        => 'Indirizzo',
    'txt_email_fromname_field'    => '&quot;Da&quot; Nome',
    'txt_success_email_fromname'  => '&quot;Da&quot; Nome',
    'txt_email_from_field'        => '&quot;Da&quot; Indirizzo',
    'txt_email_replyto_field'     => '&quot;Replyto&quot; Indirizzo',
    'des_email_replyto'           => 'Inserire un indirizzo replyto statico. <br/>'
                                   . 'Se questo campo &eacute; vuoto, lo stesso indirizzo '
                                   . 'Come il campo &quot;Da&quot; viene utilizzato. <br/> '
                                   . 'Quando si seleziona un campo di tipo e-mail, '
                                   . 'Questo campo &eacute; usato al posto. <br/> '
                                   . 'In alternativa, l&apos;email dell&apos;utente collegato-in '
                                   . 'Pu&oacute; essere selezionata automaticamente.',
    'txt_success_email_from'      => '&quot;Da&quot; Indirizzo',
    'txt_success_page'            => 'Pagina successiva',
    'des_success_page'            => 'La "Pagina successiva" verr&aacute; inviato al browser '
                                   . 'dopo che il modulo &eacute; stato compilato '
                                   . 'senza alcun errore. <br />'
                                   . 'Se no &quot;Pagina successiva&quot; &eacute; impostato, il testo '
                                   . 'specificato nel campo successivo sar&aacute; inviato.',

    'txt_success_text'            => 'testo successo',
    'des_success_text'            => 'Se no &quot;Pagina successiva&quot; &eacute; impostato, il testo '
                                   . 'verr&aacute; mostrato dopo che il modulo &eacute; stato '
                                   . 'compilato senza alcun errore.',
    'txt_submissions_text'        => 'Il trasferimento dei dati al tavolo mpform_submissions',
    'des_submissions_text'        => 'questo testo sar&aacute; scritto nel campo '
                                   . ' <b>body</b> della tabella mpform_submissions.',
    'success_options'             => 'I seguenti segnaposto sono possibili: <br /> '
                                   . '&nbsp; {DATA} -  tutti i dati inseriti nel modulo<br /> '
                                   . '&nbsp; {REFERER} - se &eacute; nota,  '
                                   . 'nella pagina da cui il modulo &eacute; stato chiamato <br /> '
                                   . '&nbsp; {IP} - l&apos;indirizzo IP del mittente<br /> '
                                   . '&nbsp; {DATE} - la data e l&apos;ora<br /> '
                                   . '&nbsp; {USER} - nome del connesso utente<br />'
                                   . '&nbsp; {EMAIL} - Email inserito oppure quella del '
                                   . 'connesso utente<br />',
    'des_success_email_text'      => 'Contenuto della e-mail di conferma al mittente',
    'des_email_text'              => 'Contenuto del messaggio al proprietario del sito',
    'des_success_email_css'       => 'CSS della e-mail di conferma al mittente',
    'des_email_css'               => 'CSS del messaggio al proprietario del sito',
    'txt_email_css'               => 'Email Style',
    'txt_success_email_css'       => 'Success Email Style',
    'fieldset_start'              => 'Fieldset (start)',
    'fieldset_end'                => 'Fieldset (fine)',
    'integer_number'              => 'Valore intero',
    'decimal_number'              => 'Valore decimale',
    'email_recip'                 => 'E-Mail Recipient',
    'email_subj'                  => 'E-Mail Oggetto (suffisso)',
    'module_name'                 => 'Modulo Multi Purpose',
    'TXT_SUBMIT'                  => 'Invia modulo',
    'HTML'                        => "codice HTML",
    'TXT_WHERE_USE_HTML'          => "attivato nella",
    'TXT_USE_IN_FORM'             => "forma",
    'TXT_USE_IN_SITE_HTML'        => "notifica al proprietario del sito",
    'TXT_USE_IN_USER_HTML'        => "nella notifica per l&apos;utente",
    'conditional'                 => "blocco visualizzato condizionalmente",
    'dependency'                  => "dipendenza",
    'entry'                       => "L&apos;ingresso &eacute;",
    'compulsory_entry'            => "obbligatorio",
    'optional_entry'              => "optionale",
    'ro_entry'                    => "sola lettura",
    'disabled_entry'              => "disabile",
    'des_field_loop'              => 'I seguenti segnaposto sono possibili: <br />'
                                   . '&nbsp; {CLASSES} - classe css  '
                                   . '(Dipende dal tipo di campo e lo stato di errore) <br />'
                                   . '&nbsp; {TITLE} - titolo del campo modulo <br /> '
                                   . '&nbsp; {FIELD} - il campo stesso <br /> '
                                   . '&nbsp; {FORMATTED_FIELD} - permette classi CSS aggiuntivi<br /> '
                                   . '&nbsp; {REQUIRED} - marchio per campi obbligatori <br /> '
                                   . '&nbsp; {HELP} - nascosto testo di aiuto, '
                                   . 'Richiede JavaScript <br />'
                                   . '&nbsp; {HELPTXT} - testo della guida sempre visibile <br /> '
                                   . '&nbsp; {ERRORTEXT} - testo di errore '
                                   . '(Solo per alcuni tipi di campo) <br />'
                                   . '&nbsp; {TEMPLATE} - contenuto del modello di campo per '
                                   . 'il campo corrente, {TEMPLATE1} per la prima linea, '
                                   . '{TEMPLATE2} per la seconda...<br />',
    'des_field_template'          => 'nel ciclo campo questo &eacute; inserito per il modello '
                                   . 'segnaposto {TEMPLATE}. '
                                   . 'I seguenti segnaposto sono possibili: <br />'
                                   . '&nbsp; {CLASSES} - classe css  '
                                   . '(Dipende dal tipo di campo e lo stato di errore) <br />'
                                   . '&nbsp; {TITLE} - titolo del campo modulo <br /> '
                                   . '&nbsp; {FIELD} - il campo stesso <br /> '
                                   . '&nbsp; {FORMATTED_FIELD} - permette classi CSS aggiuntivi<br /> '
                                   . '&nbsp; {REQUIRED} - marchio per campi obbligatori <br /> '
                                   . '&nbsp; {HELP} - nascosto testo di aiuto, '
                                   . 'Richiede JavaScript <br />'
                                   . '&nbsp; {HELPTXT} - testo della guida sempre visibile <br /> '
                                   . '&nbsp; {ERRORTEXT} - testo di errore '
                                   . '(Solo per alcuni tipi di campo) <br />',
    'txt_extraclasses'            => 'classi CSS',
    'des_extraclasses'            => 'queste classe CSS sono inoltre applicati all&apos;interno del campo',
    'des_footer'                  => 'Il seguente segnaposto &eacute; possibile: <br /> '
                                   . '&nbsp; {SUBMIT} - pulsante Submit',
    'TXT_MODIFY_FIELD'            => 'Modifica campo (ID: %s)',
    'TXT_ADD_FIELD'               => 'Aggiungi Campo',
    'TXT_SETTINGS'                => 'Opzioni generali',
    'TXT_EDIT_CSS'                => 'Impostazioni CSS',
    'TXT_EXPORT_FORM'             => 'Export modulo',
    'TXT_EXPORT_SUBMISSIONS'      => 'Export iscrizioni',
    'TXT_IMPORT_FORM'             => 'Import modulo',
    'txt_import_err_wrong_module' => 'solo l&apos;importazione di tipo di modulo '
                                   . '&quot;mpform&quot; &eacute; supportato',
    'txt_import_err_not_empty'    => 'Ci sono gi&agrave; alcuni campi in questo modulo. '
                                   . 'Importazione &egrave; supportata solo per '
                                   . 'le sezioni vuote.',
    'txt_import_warning'          => 'Attenzione: Inseriti non vengono trasferiti '
                                   . 'durante l&apos;importazione / esportazione',
    'txt_you_have_selected'       => 'Hai selezionato',
    'des_conditional_div'         => "Codice generato - probabilmente si desidera modificare il contenuto\n"
                                   . "     delle sezioni div e si potrebbe desiderare di spostare la\n"
                                   . "     chiusura div-tag e tutto ci&oacute; che segue a un&apos;altra\n"
                                   . "     sezione HTML. Se si modificano le opzioni di %s &egrave;\n"
                                   . "     necessario aggiornare questo codice manualmente\n"
                                   . "     (o si avvia di nuovo cambiando il tipo di nuovo al\n"
                                   . "     blocco di visualizzazione condizionale) "
);

$LANG['backend_adv'] = array(
    'adv_settings'                => 'Impostazioni avanzate',
    'CAPTCHA_CONTROL_HINT'        => 'scelta Captcha e le impostazioni possono essere eseguite '
                                   . 'Con la Admin-Tool &quot;Captcha-Control&quot;',
    'TXT_NOTIFICATION_SETTINGS'   => 'Il campo di formattazione per le notifiche',
    'txt_heading_html'            => 'Titoli',
    'des_heading_html'            => 'Utilizzato per tipo: Intestazione <br /> '
                                   . 'Place titolari: {HEADING}',
    'txt_short_html'              => 'campi di input una corona',
    'des_short_html'              => 'usata per i tipi:  '
                                   . 'Testo breve,'
                                   . 'Gruppo di pulsanti radio,'
                                   . 'E-Mail Recipient,'
                                   . 'Oggetto dell&apos;email, '
                                   . 'Data, '
                                   . 'Numero <br />'
                                   . 'Place titolari: {TITLE}, {DATA}, {CLASSES} e {TEMPLATE}',
    'txt_long_html'               => 'campi di input potenzialmente multipli file',
    'des_long_html'               => 'usata per i tipi:'
                                   . 'Testo Lungo,'
                                   . 'Gruppo Casella,'
                                   . 'Selezionare scatola <br />'
                                   . 'Place titolari: {TITLE}, {DATA}, {CLASSES} e {TEMPLATE}',
    'txt_email_html'              => 'E-Mail',
    'des_email_html'              => 'Utilizzato per tipo:'
                                   . 'E-Mail <br />'
                                   . 'Place titolari: {TITLE}, {DATA}, {CLASSES} e {TEMPLATE}',
    'txt_uploadfile_html'         => 'Carica file',
    'des_uploadfile_html'         => 'Utilizzato per tipo: upload di file <br />'
                                   . 'Place titolari: {TITLE}, {DATA}, {CLASSES}, {SIZE} e {TEMPLATE}',
    'SPECIAL_SETTINGS'            => 'Impostazioni speciali',
    'txt_enum'                    => 'Valore iniziale per la Radio e la casella di controllo',
    'des_enum'                    => 'comportamento predefinito: Se questo campo &eacute; vuoto, '
                                   . 'I pulsanti di opzione e caselle di controllo tornano '
                                   . 'il testo visibile. <br />'
                                   . 'Se questo campo contiene un carattere '
                                   . '(Caratteri utili potrebbero essere, ad esempio, '
                                   . '0, 1 oppure a), questo valore viene incrementato '
                                   . 'per ogni opzione ed &eacute; tornato '
                                   . 'invece del testo.',
    'TXT_VALUE_OPTION_SEPARATOR'  => "opzione valore stringa separatore",
    'TXT_DATE_FORMAT'             => "Formato data",
    'TXT_ASP_SETTINGS'            => 'Protezione SPAM',
    'des_date_format'             => 'Bash Formato data (ad es. %m/%d/%Y)',
    'TXT_UPLOAD_FILE_FOLDER'      => "Cartella Upload file",
    'TXT_UPLOAD_ONLY_EXTS'        => "Carica solo tipo di file",
    'TXT_UPLOAD_SETTINGS'         => 'Impostazioni per Upload File',
    'TXT_UPLOAD_FILEMASK'         => "Autorizzazioni per file inserito",
    'TXT_UPLOAD_DIRMASK'          => "Le autorizzazioni per directory di upload",
    'TXT_ATTACH_FILE'             => "Allega file caricato a e-mail",
    'TXT_MAX_FILE_SIZE_KB'        => "Dimensione massima file caricati Kb",
    'date_format'                 => '%m/%d/%Y',
    'help_extensions'             => 'le estensioni dei file virgola separati, ad es. pdf, xls',
    'help_filemask'               => 'permessi per file inserito, per esempio. 0640 ',
    'help_dirmask'                => 'permessi per directory di upload, ad es. 0750 ',
    'des_stored_submissions'      => 'Impostare questo valore su 0 se non si desidera che '
                                   . 'qualsiasi invio sia memorizzato nel database.'
);

// Text outputs for the frontend
$LANG['frontend'] = array(
    'MAX_FILESIZE'                => 'Massima dimensione:% d Kilobyte <br /> '
                                   . 'Tipi di file consentiti: %s',
    'integer_error'               => 'numeri interi devono essere figure solo',
    'decimal_error'               => 'Si prega di inserire un numero decimale valida',
    'err_too_large'               => 'dimensione del file &eacute; %d byte,  '
                                   . 'solo %d byte &eacute; consentito!',
    'err_too_large2'              => 'dimensione del file &eacute; troppo grande,  '
                                   . 'solo %d byte &eacute; consentito!',
    'err_partial_upload'          => 'Solo una parte del file &eacute; stato caricato!',
    'err_no_upload'               => 'Il file non &eacute; stato caricato!',
    'err_upload'                  => 'Si &eacute; verificato un errore durante il caricamento del file: '
                                   . '%s (%s), si prega di riprovare!',
    'select'                      => "Seleziona ...",
    'select_recip'                => "&Eacute; necessario selezionare un destinatario per la forma",
    'REQUIRED_FIELDS'             => 'Si prega di completare o correggere i campi  '
                                   . 'in colore rosso!',
    'INCORRECT_CAPTCHA'           => 'Il numero di verifica '
                                   . '(Noto anche come Captcha) '
                                   . 'che hai inserito non &eacute; corretta.',
    'VERIFICATION'                => "Codice di verifica (antispam)"

);
