<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.3.2
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
/* The file provides strings in French language for the PEAR part. */

$UPLOAD_MESSAGE['TOO_LARGE']             = "La taille du fichier est trop grand."
                                         . "La taille maximale autoris&eacute;e est: %s octets";
$UPLOAD_MESSAGE['MISSING_DIR']           = 'Le r&eacute;pertoire de destination manquant. ';
$UPLOAD_MESSAGE['IS_NOT_DIR']            = 'Le r&eacute;pertoire de destination n\'existe '
                                         . 'ou est un fichier r&eacute;gulier.';
$UPLOAD_MESSAGE['NO_WRITE_PERMS']        = 'Le r&eacute;pertoire de destination n\'a pas '
                                         . 'autorisations en &eacute;criture.';
$UPLOAD_MESSAGE['NO_USER_FILE']          = 'Vous n\'importe quel fichier s&eacute;lectionn&eacute; '
                                         . 'pour le t&eacute;l&eacute;chargement. ';
$UPLOAD_MESSAGE['BAD_FORM']              = 'Le formulaire html ne contenir le n&eacute;cessaire '
                                         . '"M&eacute;thode = "post" enctype = "multipart/form-data".';
$UPLOAD_MESSAGE['E_FAIL_COPY']           = 'Impossible de copier le fichier temporaire.';
$UPLOAD_MESSAGE['E_FAIL_MOVE']           = 'Impossible de d&eacute;placer le fichier.';
$UPLOAD_MESSAGE['FILE_EXISTS']           = 'existe d&eacute;j&agrave; le fichier de destination.';
$UPLOAD_MESSAGE['CANNOT_OVERWRITE']      = 'Le fichier de destination existe d&eacute;j&agrave; '
                                         . 'et ne pouvait pas &ecirc;tre &eacute;cras&eacute;. ';
$UPLOAD_MESSAGE['NOT_ALLOWED_EXTENSION'] = 'extension de fichier non autoris&eacute;';
$UPLOAD_MESSAGE['PARTIAL']               = 'Le fichier a &eacute;t&eacute; que partiellement '
                                         . 't&eacute;l&eacute;charg&eacute;.';
$UPLOAD_MESSAGE['ERROR']                 = 'Upload d\'erreur:';
$UPLOAD_MESSAGE['DEV_NO_DEF_FILE']       = 'Ce nom de fichier est pas d&eacute;fini dans la forme '
                                         . 'comme &lt;input type="file" name=?&gt;.';


