<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.31
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2019, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* The file provides strings in Italian language for the PEAR part. */

$UPLOAD_MESSAGE['TOO_LARGE']             = "dimensione del file troppo grande. "
                                         . "La dimensione massima consentita &egrave; %s bytes.";
$UPLOAD_MESSAGE['MISSING_DIR']           = 'directory di destinazione mancante.';
$UPLOAD_MESSAGE['IS_NOT_DIR']            = 'La directory di destinazione non esiste '
                                         . 'o &egrave; un file regolare.';
$UPLOAD_MESSAGE['NO_WRITE_PERMS']        = 'La directory di destinazione non ha  '
                                         . 'permessi di scritto.';
$UPLOAD_MESSAGE['NO_USER_FILE']          = 'si rifugio selezionato un file per il caricamento. ';
$UPLOAD_MESSAGE['BAD_FORM']              = 'Il form html non contenere la richiesta  '
                                         . 'method="post" enctype="multipart/form-data".';
$UPLOAD_MESSAGE['E_FAIL_COPY']           = 'Impossibile copiare il file temporaneo.';
$UPLOAD_MESSAGE['E_FAIL_MOVE']           = 'Impossibile spostare il file.';
$UPLOAD_MESSAGE['FILE_EXISTS']           = 'Il file di destinazione esiste gi&agrave;.';
$UPLOAD_MESSAGE['CANNOT_OVERWRITE']      = 'Il file di destinazione esiste gi&agrave; '
                                         . 'e non poteva essere sovrascritti.';
$UPLOAD_MESSAGE['NOT_ALLOWED_EXTENSION'] = 'estensione del file non &egrave; permesso.';
$UPLOAD_MESSAGE['PARTIAL']               = 'Il file &egrave; stato caricato solo parzialmente.';
$UPLOAD_MESSAGE['ERROR']                 = 'Carica di errore:';
$UPLOAD_MESSAGE['DEV_NO_DEF_FILE']       = 'Questo nome non &egrave; definito nella forma '
                                         . 'Come &lt;input type="file" name=?&gt;.';

