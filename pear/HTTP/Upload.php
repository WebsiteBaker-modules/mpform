<?php
// ********************************************** //
// This software is licensed by the LGPL
// -> http://www.gnu.org/copyleft/lesser.txt
// (c) 2001- 2004 by Tomas Von Veschler Cox //
// ********************************************** //
// $Id$

/**
 * Pear File Uploader class. Easy and secure managment of files
 * submitted via HTML Forms.
 *
 * Leyend:
 * - you can add error msgs in your language in the HTTP_Upload_Error class
 *
 * TODO:
 * - try to think a way of having all the Error system in other
 *   file and only include it when an error ocurrs
 *
 * -- Notes for users HTTP_Upload >= 0.9.0 --
 *
 *  Error detection was enhanced, so you no longer need to
 *  check for PEAR::isError() in $upload->getFiles() or call
 *  $upload->isMissing(). Instead you'll
 *  get the error when do a check for $file->isError().
 *
 *  Example:
 *
 *  $upload = new HTTP_Upload('en');
 *  $file = $upload->getFiles('i_dont_exist_in_form_definition');
 *  if ($file->isError()) {
 *      die($file->getMessage());
 *  }
 *
 *  --
 *
 */

require_once (dirname(dirname(__FILE__)).'/PEAR.php');
require_once (dirname(dirname(__FILE__)).'/HTTP/Upload/Error.php');
require_once (dirname(dirname(__FILE__)).'/HTTP/Upload/File.php');

/**
 * This class provides an advanced file uploader system
 * for file uploads made from html forms
 *
 * @author  Tomas V.V.Cox <cox@idecnet.com>
 * @see http://vulcanonet.com/soft/index.php?pack=uploader
 * @package  HTTP_Upload
 * @category HTTP
 */
class HTTP_Upload extends HTTP_Upload_Error
{
    /**
     * Contains an array of "uploaded files" objects
     * @var array
     */
    protected $files = array();

    /**
     * Whether the files array has already been built or not
     * @var int
     */
    protected $is_built = false;

    /**
     * Contains the desired chmod for uploaded files
     * @var int
     */
    protected $_chmod = HTTP_UPLOAD_DEFAULT_CHMOD;

    /**
     * Specially used if the naming mode is 'seq'
     * Contains file naming information
     *
     * @var array
     */
    protected $_modeNameSeq = array(
        'flag' => false,
        'prepend' => '',
        'append' => '',
    );

    /**
     * Whether or not to consider multiple extensions
     * e.g. file.txt.foo would have 'txt' and 'foo'
     * @var bool
     */
    protected $_allowMultipleExtensions = false;

    /**
     * PHP5 Constructor
     *
     * @param string $lang Language to use for reporting errors
     * @see Upload_Error::error_codes
     */
    public function __construct($lang = null)
    {
        parent::__construct($lang);
        if (function_exists('version_compare') &&
            version_compare(phpversion(), '4.1', 'ge'))
        {
            $this->post_files = $_FILES;
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $this->content_type = $_SERVER['CONTENT_TYPE'];
            }
        } else {
            global $HTTP_POST_FILES, $HTTP_SERVER_VARS;
            $this->post_files = $HTTP_POST_FILES;
            if (isset($HTTP_SERVER_VARS['CONTENT_TYPE'])) {
                $this->content_type = $HTTP_SERVER_VARS['CONTENT_TYPE'];
            }
        }
    }

    /**
     * PHP4 Constructor
     *
     * @see __construct
     */
    public function HTTP_Upload($lang = null)
    {
        self::__construct($lang);
    }

    /**
     * Whether or not to consider multiple extensions
     * e.g. file.txt.foo would have 'txt' and 'foo'
     * @param string $flag
     */
    public function allowMultipleFileExtensions($flag = true)
    {
        $this->_allowMultipleExtensions = (bool) $flag;
    }

    /**
     * Get files
     *
     * @param mixed $file If:
     *    - not given, function will return array of upload_file objects
     *    - is int, will return the $file position in upload_file objects array
     *    - is string, will return the upload_file object corresponding
     *        to $file name of the form. For ex:
     *        if form is <input type="file" name="userfile">
     *        to get this file use: $upload->getFiles('userfile')
     *
     * @return mixed array or object (see @param $file above) or Pear_Error
     */
    public function getFiles($file = null)
    {
        //build only once for multiple calls
        if (!$this->is_built) {
            $files = $this->_buildFiles();
            if (PEAR::isError($files)) {
                // there was an error with the form.
                // Create a faked upload embedding the error
                $files_code = $files->getCode();
                $this->files['_error'] =  new HTTP_Upload_File(
                                                       '_error', null,
                                                       null, null,
                                                       null, $files_code,
                                                       $this->lang, $this->_chmod, $this->_allowMultipleExtensions);
            } else {
                $this->files = $files;
            }
            $this->is_built = true;
        }
        if ($file !== null) {
            if (is_int($file)) {
                $pos = 0;
                foreach ($this->files as $obj) {
                    if ($pos == $file) {
                        return $obj;
                    }
                    $pos++;
                }
            } elseif (is_string($file) && isset($this->files[$file])) {
                return $this->files[$file];
            }
            if (isset($this->files['_error'])) {
                return $this->files['_error'];
            } else {
                // developer didn't specify this name in the form
                // warn him about it with a faked upload
                $huf =  new HTTP_Upload_File(
                                             '_error', null,
                                             null, null,
                                             null, 'DEV_NO_DEF_FILE',
                                             $this->lang, HTTP_UPLOAD_DEFAULT_CHMOD, $this->_allowMultipleExtensions);
                return $huf;
            }
        }
        return $this->files;
    }

    /**
     * Creates the list of the uploaded file
     *
     * @return array of HTTP_Upload_File objects for every file
     */
    protected function _buildFiles()
    {
        // Form method check
        if (!isset($this->content_type) ||
            strpos($this->content_type, 'multipart/form-data') !== 0)
        {
            $error = $this->raiseError('BAD_FORM');
            return $error;
        }
        // In 4.1 $_FILES isn't initialized when no uploads
        // XXX (cox) afaik, in >= 4.1 and < 4.3 only
        if (function_exists('version_compare') &&
            version_compare(PHP_VERSION, '4.1', 'ge') &&
            version_compare(PHP_VERSION, '4.3', 'lt'))
        {
            $error = $this->isMissing();
            if (PEAR::isError($error)) {
                return $error;
            }
        }

        // map error codes from 4.2.0 $_FILES['userfile']['error']
        if (function_exists('version_compare') &&
            version_compare(phpversion(), '4.2.0', 'ge')) {
            $uploadError = array(
                1 => 'TOO_LARGE',
                2 => 'TOO_LARGE',
                3 => 'PARTIAL',
                4 => 'NO_USER_FILE'
                );
        }


        // Parse $_FILES (or $HTTP_POST_FILES)
        $files = array();
        foreach ($this->post_files as $userfile => $value) {
            if (is_array($value['name'])) {
                foreach ($value['name'] as $key => $val) {
                    $err = $value['error'][$key];
                    if (isset($err) && $err !== 0 && isset($uploadError[$err])) {
                        $error = $uploadError[$err];
                    } else {
                        $error = null;
                    }
                    $name = basename($value['name'][$key]);
                    $tmp_name = $value['tmp_name'][$key];
                    $size = $value['size'][$key];
                    $type = $value['type'][$key];
                    $formname = $userfile . "[$key]";
                    $files[$formname] = new HTTP_Upload_File($name, $tmp_name,
                                                             $formname, $type, $size, $error, $this->lang, $this->_chmod, $this->_allowMultipleExtensions);
                }
                // One file
            } else {
                $err = $value['error'];
                if (isset($err) && $err !== 0 && isset($uploadError[$err])) {
                    $error = $uploadError[$err];
                } else {
                    $error = null;
                }
                $name = basename($value['name']);
                $tmp_name = $value['tmp_name'];
                $size = $value['size'];
                $type = $value['type'];
                $formname = $userfile;
                $files[$formname] = new HTTP_Upload_File($name, $tmp_name,
                                                         $formname, $type, $size, $error, $this->lang, $this->_chmod, $this->_allowMultipleExtensions);
            }
        }
        return $files;
    }

    /**
     * Checks if the user submited or not some file
     *
     * @return mixed False when are files or PEAR_Error when no files
     * @access public
     * @see Read the note in the source code about this function
     */
    public function isMissing()
    {
        if (count($this->post_files) < 1) {
            $error = $this->raiseError('NO_USER_FILE');
            return $error;
        }
        //we also check if at least one file has more than 0 bytes :)
        $files = array();
        $size = 0;
        $error = null;

        foreach ($this->post_files as $userfile => $value) {
            if (is_array($value['name'])) {
                foreach ($value['name'] as $key => $val) {
                    $size += $value['size'][$key];
                }
            } elseif (!empty($value['name'])) {  //one file
                $size += $value['size'];
                $error = $value['error'];
            }
        }
        if ($error !== null && $error != 2 && $size == 0) {
            $error = $this->raiseError('NO_USER_FILE');
            return $error;
        }
        return false;
    }

    /**
     * Sets the chmod to be used for uploaded files
     *
     * @param int Desired mode
     */
    public function setChmod($mode)
    {
        $this->_chmod = $mode;
    }
}
