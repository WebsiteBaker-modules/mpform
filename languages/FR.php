<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and email attachment mpForm allows forms over one or more pages.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.1.24
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Quinto, Martin Hecht (mrbaseman)
 * @copyright           (c) 2009 - 2016, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        
 *
 **/

/*  French translation by Quinto */

// declare module language array
global $LANG;
$LANG = array();

// Text outputs for the backend
$LANG['backend'] = array(
        'SETTINGS' => 'R&eacute;glages du Formulaire',
        'TXT_TYP'        => 'Type',
        'TXT_LIST'        => 'Liste',
        'TXT_DEFAULT'        => 'Valeur par d&eacute;faut',
        'TXT_ISFOLLOWING' => 'Est pr&eacute;c&eacute;d&eacute; d&apos;un formulaire',
        'des_is_following'=> 'Sp&eacute;cifie si la page est la premi&egrave;re d&apos;un formulaire multi-pages.<br />
Choisissez Non dans le cas d&apos;un formulaire compos&eacute; d&apos;une seule page.',
        'TXT_MP_SETTINGS' => 'D&eacute;roulement des op&eacute;rations',
        'TXT_LAYOUT_SETTINGS' => 'Mise en page',
        'EMAIL_SETTINGS_1' => 'R&eacute;glages email (email au gestionnaire du site)',
        'EMAIL_SETTINGS_2' => 'R&eacute;glages email (email de confirmation &agrave; l&apos;utilisateur)',
        'TXT_STATIC_ADDR' => 'Adresse comme d&eacute;finie &agrave; la ligne suivante',
        'TXT_STATIC_NAME' => 'Nom comme d&eacute;fini &agrave; la ligne suivante',
        'TXT_USER_ADDR'        => 'Adresse email de l&apos;utilisateur connect&eacute;',
        'TXT_USER_NAME'        => 'Nom de l&apos;utilisateur connect&eacute;',
        'EMAIL_SUBJECT' => 'Resultats du formulaire du site internet...',
        'EMAIL_SUC_TEXT' => 'Merci pour l&apos;envoi de votre demande sur ',
        'EMAIL_SUC_SUBJ' => 'Votre demande &agrave; &eacute;t&eacute; envoy&eacute;e',
        'VERIFICATION' => 'V&eacute;rification (protection anti-SPAM)',
        'HELP' => 'Aide pour',
        'TXT_COPY_FIELD' => 'Dupliquer Champ',
        'TXT_ADD_FIELD'        => 'Ajouter Champ',
        'ROWS'        => 'Lignes',
        'TXT_TBLSFX' => 'Code du tableau de r&eacute;sultats',
        'DES_TXT_TBLSFX' => 'Toutes les pages de r&eacute;sultats d&eacute;sign&eacute;es par ce code seront regroup&eacute;s dans un m&ecirc;me tableau.',
        'textfield'        => 'Champ texte (une ligne)',
        'textarea'        => 'Zone de texte (multi-lignes)',
        'date'        => 'Date',
        'fileupload'        => 'Envoi de fichier (Upload)',
        'txt_email_to'        => '&quot;&Agrave;&quot; Adresse(s)',
        'des_email_to'        => 'Mettez une seule adresse &quot;&Agrave;&quot; par ligne, format&eacute;e comme dans l&apos;exemple ci-dessous:<br />
&nbsp; Service apr&egrave;s vente &lt;sav@votreentreprise.com&gt;<br />
&nbsp; Service commercial &lt;vente@ votreentreprise.com,commercial@ votreentreprise.com&gt;<br />
Vous pouvez aussi &eacute;crire seulement l&apos;adresse email (sans le nom et sans d&eacute;limiteurs),<br />
Dans ce cas l&apos;utilisateur verra l&apos;adresse email si il a l&apos;autorisation de choisir le destinataire.',
 
        'txt_success_email_to'        => '&quot;&Agrave;&quot; l&apos;Adresse',
        'txt_email_fromname_field'        => '&quot;Depuis&quot; le Nom',
        'txt_success_email_fromname'        => '&quot;Depuis&quot; le Nom',
        'txt_email_from_field'        => '&quot;Depuis&quot; l&apos;Adresse',
        'txt_email_replyto_field'        => '&quot;Response&quot; &agrave;&apos;Adresse',
        'des_email_replyto'        => 'Entrez une adresse replyto statique. <br/>
Si ce champ est vide, la m&egrave;me adresse que le champ &quot;Depuis&quot; est utilis&eacute;. <br/>
Quand un champ de type e-mail est s&eacute;lectionn&eacute;, ce champ est utilis&eacute; &agrave; la place. <br/>
Alternativement, l&apos;e-mail de l&apos;utilisateur connect&eacute; peut &ecric;tre automatiquement s&eacute;lectionn&eacute;.',
        'txt_success_email_from'        => '&quot;Depuis&quot; L&apos;Adresse',
        'txt_success_page'        => 'Page suivante',
        'des_success_page'        => 'La &quot;Page suivante&quot; sera affich&eacute;e dans le navigateur une fois le formulaire correctement rempli.<br />
Si vous sp&eacute;cifiez &quot;aucun&quot;, le texte ci-dessous sera affich&eacute;.
',
        'txt_success_text'        => 'Texte si r&eacute;ussi',
        'des_success_text'        => 'Si "Aucun" est sp&eacute;cifi&eacute; pour la page suivante, ce texte sera affich&eacute; apr&egrave;s validation du formulaire correctement rempli.',
        'txt_submissions_text'        => 'Donn&eacute;es transmises au tableau mpform_submissions',
        'des_submissions_text'        => 'Ce texte sera &eacute;crit dans le champ <b>body</b> du tableau mpform_submissions.',
        'success_options'        => 'Vous pouvez utiliser les variables suivantes:<br />
 &nbsp; {DATA} - toutes les donn&eacute;es saisies dans le formulaire<br />
 &nbsp; {REFERER} - si renseign&eacute;, la page d&apos;origine de l&apos;appel du formulaire<br />
 &nbsp; {IP} - l&apos;adresse IP de l&apos;internaute ayant valid&eacute; le formulaire<br />
 &nbsp; {DATE} - date et heure<br />
 &nbsp; {USER} - nom de l&apos;utilisateur connect&eacute;<br />
',
        'des_success_email_text'        => 'Contenu de l&apos;email de confirmation envoy&eacute; &agrave; l&apos;internaute',
        'des_email_text'        => 'Contenu de l&apos;email envoy&eacute; au gestionnaire du site',
        'fieldset_start'        => 'Ensemble de champs (d&eacute;but)',
        'fieldset_end'        => 'Ensemble de champs (fin)',
        'integer_number'        => 'Valeur enti&egrave;re',
        'decimal_number'        => 'Valeur d&eacute;cimale',
        'email_recip'        => 'Destinataire de l&apos;email',
        'email_subj'        => 'Objet de l&apos;email',
        'module_name'        => 'Multi Purpose Form',
        'TXT_SUBMIT'        => 'Validez le Formulaire',
        'HTML'        => 'Code HTML',
        'entry'        => 'Saisie',
        'compulsory_entry'        => 'obligatoire',
        'optional_entry'        => 'optionnelle',
        'ro_entry'        => 'lecture seulement',
        'disabled_entry'        => 'd&eacute;sactiv&eacute;e',
        'des_field_loop'        => 'Vous pouvez utiliser les variables suivantes:<br />
 &nbsp; {CLASSES} - classe css (selon le type de champ et l&apos;&eacute;tat d&apos;erreur)<br />
 &nbsp; {TITLE} - titre du champ<br />
 &nbsp; {FIELD} - contenu du champ<br />
 &nbsp; {REQUIRED} - marqueur pour les champs obligatoires<br />
 &nbsp; {HELP} - texte d&apos;aide cach&eacute;, n&eacute;cessite Javascript<br />
 &nbsp; {HELPTXT} - texte d&apos;aide visible en permanence<br />
 &nbsp; {ERRORTEXT} - texte d&apos;erreur (seulement pour certains types de champs)<br />',
        'des_footer'        => 'Vous pouvez utiliser la variable suivante:<br />
 &nbsp; {SUBMIT} - bouton de validation',
 'TXT_MODIFY_FIELD'        => 'Edition Champ (ID: %s)',
 'TXT_ADD_FIELD'        => 'Ajouter Champ',
 'TXT_SETTINGS'                => 'Options G&eacute;n&eacute;rales',
 'TXT_EDIT_CSS'                => 'Editer la feuille CSS'    
);

$LANG['backend_adv'] = array(
        'adv_settings'        => 'Configuration Avanc&eacute;e',
        'CAPTCHA_CONTROL_HINT'        => 'Les r&eacute;glages et le choix du type de Captcha peuvent &ecirc;tres effectu&eacute;s via l&apos;Outil d&apos;administration "Captcha and Advanced-Spam-Protection (ASP) Control".',
        'TXT_NOTIFICATION_SETTINGS'        => 'Formatage des champs pour les Notifications',
        'txt_heading_html'        => 'Ent&ecirc;te',
        'des_heading_html'        => 'Utilis&eacute; pour le type: Ent&ecirc;te<br />Variable: {HEADING}',
        'txt_short_html'        => 'Champ de saisie <br/>(une ligne)',
        'des_short_html'        => 'Utilis&eacute; pour les types: Texte court, Groupe de boutons radio, Destinataire de l&apos;email, Objet de l&apos;email, Date, Nombre<br />Variables: {TITLE} et {DATA}',
        'txt_long_html'        => 'Zone de saisie <br/>(multi-lignes)',
        'des_long_html'        => 'Utilis&eacute; pour les types: Texte long, Groupe de checkbox, S&eacute;lection des bo&icirc;tes<br />Variables: {TITLE} et {DATA}',
        'txt_email_html'        => 'Adresse email',
        'des_email_html'        => 'Utilis&eacute; pour type: Adresse email<br />Variables: {TITLE} et {DATA}',
        'txt_uploadfile_html'        => 'Envoi de fichiers <br/>(Upload)',
        'des_uploadfile_html'        => 'Utilis&eacute; pour type: Envoi de fichiers<br />Variables: {TITLE} et {DATA}',
        'SPECIAL_SETTINGS'        => 'R&eacute;glages sp&eacute;ciaux',
        'txt_enum'        => 'Valeur initiale des boutons Radio et Checkbox',
        'des_enum'        => 'Comportement par d&eacute;faut: Si ce champ est vide, les boutons radio et les checkboxs renvoient le texte affich&eacute;.<br />
Si ce champ contient un caract&egrave;re (il est pratique d&apos;utiliser, par exemple, 0, 1 ou a), alors cette valeur sera incr&eacute;ment&eacute;e pour chaque option et renvoy&eacute;e &agrave; la place du texte.',        
         'TXT_VALUE_OPTION_SEPARATOR' => "Cha&icirc;ne de s&eacute;parateur valeur-option",
        'TXT_DATE_FORMAT' => 'Format de la date',
        'TXT_ASP_SETTINGS'        => 'Protection Anti-SPAM', 
         'des_date_format'        => '<br/>Cha&icirc;ne de format de date et d&apos;heure standard (ex. %m/%d/%Y)',
        'TXT_UPLOAD_FILE_FOLDER' => 'Dossier de destination',
        'TXT_UPLOAD_ONLY_EXTS' => 'Autoriser seulement les fichiers de type(s)',
        'TXT_UPLOAD_SETTINGS'        => 'R&eacute;glages pour l&apos;envoi de fichiers (Upload)',
        'TXT_UPLOAD_FILEMASK' => 'Droits des fichiers envoy&eacute;s',
        'TXT_UPLOAD_DIRMASK' => 'Droits du dossier de destination',
        'TXT_ATTACH_FILE' => 'Joindre les fichiers &agrave; l&apos;email',
        'TXT_MAX_FILE_SIZE_KB' => 'Taille maximum des fichiers en ko',
        'date_format'        => '%d/%m/%Y',
        'help_extensions'        => 'Extensions de fichiers s&eacute;par&eacute; par des virgules, ex. pdf,xls',
        'help_filemask'        => 'Droits des fichiers apr&egrave;s envoi (upload), ex. 0640',
        'help_dirmask'        => 'Droits du dossier de destination des fichiers, ex. 0750'

);

// Text outputs for the frontend
$LANG['frontend'] = array(
        'MAX_FILESIZE' => "Taille maximum du fichier: %d Kilo-octets<br />Types de fichiers permis: %s",
        'integer_error'        => 'Veuillez saisir des chiffres seulement pour composer un nombre entier',
        'decimal_error'        => 'Veuillez saisir un nombre au format d&eacute;cimal',
        'err_too_large'        => 'La taille du fichier est de %d octets, le maximum accept&eacute; est %d octets!',
        'err_too_large2'        => 'La taille du fichier d&eacute;passe la taille permise, le maximum est %d octets!',
        'err_partial_upload' => 'Le fichier n&apos;a pas pu &ecirc;tre envoy&eacute; int&eacute;gralement!',
        'err_no_upload' => 'Le fichier n&apos;a pas pu &ecirc;tre envoy&eacute;!',
        'err_upload' => 'Une erreur s&apos;est produite lors de l&apos;envoi du fichier: %s (%s), veuillez r&eacute;essayer!',
        'select' => 'Veuillez choisir...',
        'select_recip' => 'Vous devez choisir un destinataire pour le formulaire',
        'REQUIRED_FIELDS' => 'Veuillez compl&eacute;ter ou corriger les champs de couleur rouge!',
        'INCORRECT_CAPTCHA' => 'Le num&eacute;ro de v&eacute;rification (aussi appel&eacute; CAPTCHA) que vous avez saisi est incorrect.',
        'VERIFICATION' => 'Code v&eacute;rification (anti-SPAM)'
        
);

?>
