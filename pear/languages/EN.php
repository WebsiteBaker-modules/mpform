<?php

/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *
 * @category            page
 * @module              mpform
 * @version             1.3.35
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2020, Website Baker Org. e.V.
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* The file provides strings in English language for the PEAR part. */

$UPLOAD_MESSAGE['TOO_LARGE']             = "File size too large. "
                                         . "The maximum permitted size is: %s bytes.";
$UPLOAD_MESSAGE['MISSING_DIR']           = 'Missing destination directory.';
$UPLOAD_MESSAGE['IS_NOT_DIR']            = 'The destination directory doesn\'t exist '
                                         . 'or is a regular file.';
$UPLOAD_MESSAGE['NO_WRITE_PERMS']        = 'The destination directory doesn\'t have '
                                         . 'write permissions.';
$UPLOAD_MESSAGE['NO_USER_FILE']          = 'You haven\'t selected any file for uploading.';
$UPLOAD_MESSAGE['BAD_FORM']              = 'The html form doesn\'t contain the required '
                                         . 'method="post" enctype="multipart/form-data".';
$UPLOAD_MESSAGE['E_FAIL_COPY']           = 'Failed to copy the temporary file.';
$UPLOAD_MESSAGE['E_FAIL_MOVE']           = 'Impossible to move the file.';
$UPLOAD_MESSAGE['FILE_EXISTS']           = 'The destination file already exists.';
$UPLOAD_MESSAGE['CANNOT_OVERWRITE']      = 'The destination file already exists '
                                         . 'and could not be overwritten.';
$UPLOAD_MESSAGE['NOT_ALLOWED_EXTENSION'] = 'File extension not permitted.';
$UPLOAD_MESSAGE['PARTIAL']               = 'The file was only partially uploaded.';
$UPLOAD_MESSAGE['ERROR']                 = 'Upload error:';
$UPLOAD_MESSAGE['DEV_NO_DEF_FILE']       = 'This filename is not defined in the form '
                                         . 'as &lt;input type="file" name=?&gt;.';
