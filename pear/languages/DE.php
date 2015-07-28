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
/* The file provides strings in German language for the PEAR part. */

$UPLOAD_MESSAGE['TOO_LARGE'] = "Die Datei ist zu gro&szlig;. Die maximal erlaubte Gr&ouml;&szlig;e ist: %s Byte.";
$UPLOAD_MESSAGE['MISSING_DIR'] ='Zielverzeichnis fehlt.';
$UPLOAD_MESSAGE['IS_NOT_DIR'] = 'Das Zielverzeichnis existiert nicht oder ist eine Datei.';
$UPLOAD_MESSAGE['NO_WRITE_PERMS'] = 'Keine Schreibrechte f&uuml;r das Zielverzeichnis.';
$UPLOAD_MESSAGE['NO_USER_FILE'] = 'Es wurde keine Datei zum Hochladen angegeben.';
$UPLOAD_MESSAGE['BAD_FORM'] = 'Im HTML-Formular fehlen die Angaben: method="post" enctype="multipart/form-data".';
$UPLOAD_MESSAGE['E_FAIL_COPY'] = 'Tempor&auml;re Datei konnte nicht kopiert werden.';
$UPLOAD_MESSAGE['E_FAIL_MOVE'] = 'Datei konnte nicht verschoben werden.';
$UPLOAD_MESSAGE['FILE_EXISTS'] = 'Zieldatei existiert bereits.';
$UPLOAD_MESSAGE['CANNOT_OVERWRITE'] = 'Zieldatei existiert bereits und kann nicht &uuml;berschrieben werden.';
$UPLOAD_MESSAGE['NOT_ALLOWED_EXTENSION'] = 'Diese Dateierweiterung ist nicht erlaubt.';
$UPLOAD_MESSAGE['PARTIAL'] = 'Die Datei wurde unvollst&auml;ndig hochgeladen.';
$UPLOAD_MESSAGE['ERROR'] = 'Upload-Fehler: ';
$UPLOAD_MESSAGE['DEV_NO_DEF_FILE'] = 'Dieser Dateiname wurde im Formular nicht als &lt;input type="file" name=?&gt; definiert.';
?>
