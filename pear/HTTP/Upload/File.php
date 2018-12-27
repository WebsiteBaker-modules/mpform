<?php
// ********************************************** //
// This software is licensed by the LGPL
// -> http://www.gnu.org/copyleft/lesser.txt
// (c) 2001- 2004 by Tomas Von Veschler Cox //
// ********************************************** //
// $Id$

require_once (dirname(dirname(dirname(__FILE__))).'/PEAR.php');

/**
 * This class provides functions to work with the uploaded file
 *
 * @author  Tomas V.V.Cox <cox@idecnet.com>
 * @see http://vulcanonet.com/soft/index.php?pack=uploader
 * @package  HTTP_Upload
 * @category HTTP
 */
class HTTP_Upload_File extends HTTP_Upload_Error
{
    /**
     * Assoc array with file properties
     * @var array
     */
    protected $upload = array();

    /**
     * If user haven't selected a mode, by default 'safe' will be used
     * @var boolean
     */
    protected $mode_name_selected = false;

    /**
     * It's a common security risk in pages who has the upload dir
     * under the document root (remember the hack of the Apache web?)
     *
     * @var array
     * @see HTTP_Upload_File::setValidExtensions()
     */
    protected $_extensionsCheck = array('php', 'phtm', 'phtml', 'php3', 'inc');

    /**
     * @see HTTP_Upload_File::setValidExtensions()
     * @var string
     */
    protected $_extensionsMode  = 'deny';

    /**
     * Whether to use case-sensitive extension checks or not
     * @see HTTP_Upload_File::setValidExtensions()
     * @var bool
     */
     protected $_extensionsCaseSensitive = true;

    /**
     * Contains the desired chmod for uploaded files
     * @var int
     */
    protected $_chmod = HTTP_UPLOAD_DEFAULT_CHMOD;

    /**
     * PHP5 Constructor
     *
     * @param   string  $name       destination file name
     * @param   string  $tmp        temp file name
     * @param   string  $formname   name of the form
     * @param   string  $type       Mime type of the file
     * @param   string  $size       size of the file
     * @param   string  $error      error on upload
     * @param   string  $lang       used language for errormessages
     * @param   bool    $allowMulti allow for checking multiple extensions in same filename
     */
    public function __construct($name = null, $tmp = null,  $formname = null,
                              $type = null, $size = null, $error = null,
                              $lang = null, $chmod = HTTP_UPLOAD_DEFAULT_CHMOD, $allowMulti = false)
    {
        parent::__construct($lang);
        $ext = null;
        $extra_ext = array();

        if (empty($name)
            && ($error != 'TOO_LARGE' && $error != 'DEV_NO_DEF_FILE' && $size == 0)
        ) {
            $error = 'NO_USER_FILE';
        } elseif ($tmp == 'none' || $name == '_error' && $error == 'DEV_NO_DEF_FILE') {
            $error = 'TOO_LARGE';
        } else {
            // strpos needed to detect files without extension
            if (($pos = strrpos($name, '.')) !== false) {
                $ext = substr($name, $pos + 1);

                if ($allowMulti === true) {
                    // check for multi extensions, e.g. foo.php.txt
                    $base = substr($name, 0, $pos);
                    while (($pos = strrpos($base, '.')) !== false) {
                        $extra = substr($base, $pos + 1);
                        $extra_ext[] = $extra;
                        $base = substr($base, 0, $pos);
                    }
                }
            }
        }

        if (function_exists('version_compare') &&
            version_compare(phpversion(), '4.1', 'ge')) {
            if (isset($_POST['MAX_FILE_SIZE']) &&
                $size > $_POST['MAX_FILE_SIZE']) {
                $error = 'TOO_LARGE';
            }
        } else {
            global $HTTP_POST_VARS;
            if (isset($HTTP_POST_VARS['MAX_FILE_SIZE']) &&
                $size > $HTTP_POST_VARS['MAX_FILE_SIZE']) {
                $error = 'TOO_LARGE';
            }
        }

        $this->upload = array(
            'real'      => $name,
            'name'      => $name,
            'form_name' => $formname,
            'ext'       => $ext,
            'tmp_name'  => $tmp,
            'size'      => $size,
            'type'      => $type,
            'error'     => $error,
            'extra_ext' => $extra_ext,
        );

        $this->_chmod = $chmod;
    }

    /**
     * PHP4 Constructor
     *
     * @see __construct
     */
    public function HTTP_Upload_File($name = null, $tmp = null,  $formname = null,
                              $type = null, $size = null, $error = null,
                              $lang = null, $chmod = HTTP_UPLOAD_DEFAULT_CHMOD, $allowMulti = false)
    {
        self::__construct($name, $tmp, $formname, $type, $size, $error, $lang, $chmod, $allowMulti);
    }

    /**
     * Sets the name of the destination file
     *
     * @param string $mode     A valid mode: 'uniq', 'seq', 'safe' or 'real' or a file name
     * @param string $prepend  A string to prepend to the name
     * @param string $append   A string to append to the name
     *
     * @return string The modified name of the destination file
     */
    public function setName($mode, $prepend = null, $append = null)
    {
        switch ($mode) {
            case 'uniq':
                $name = $this->nameToUniq();
                $this->upload['ext'] = $this->nameToSafe($this->upload['ext'], 10);
                $name .= '.' . $this->upload['ext'];
                break;
            case 'safe':
                $name = $this->nameToSafe($this->upload['real']);
                if (($pos = strrpos($name, '.')) !== false) {
                    $this->upload['ext'] = substr($name, $pos + 1);
                } else {
                    $this->upload['ext'] = '';
                }
                break;
            case 'real':
                $name = $this->upload['real'];
                break;
            case 'seq':
                $this->_modeNameSeq['flag'] = true;
                $this->_modeNameSeq['prepend'] = $prepend;
                $this->_modeNameSeq['append'] = $append;
                break;
            default:
                $name = $mode;
        }
        $this->upload['name'] = $prepend . $name . $append;
        $this->mode_name_selected = true;
        return $this->upload['name'];
    }

    /**
     * Sequence file names in the form: userGuide[1].pdf, userGuide[2].pdf ...
     *
     * @param string $dir  Destination directory
     */
    public function nameToSeq($dir)
    {
        //Check if a file with the same name already exists
        $name = $dir . DIRECTORY_SEPARATOR . $this->upload['real'];
        if (!@is_file($name)) {
            return $this->upload['real'];
        } else {
            //we need to strip out the extension and the '.' of the file
            //e.g 'userGuide.pdf' becomes 'userGuide'
            $baselength = strlen($this->upload['real']) - strlen($this->upload['ext']) - 1;
            $basename = substr( $this->upload['real'],0, $baselength );

            //here's the pattern we're looking for
            $pattern = '/(\[)([[:digit:]]+)(\])$/';

            //just incase the original filename had a sequence, we take it out
            // e.g: 'userGuide[3]' should become 'userGuide'
            $basename =  preg_replace($pattern, '', $basename);

            /*
             * attempt to find a unique sequence file name
             */
            $i = 1;

            while (true) {
                $filename = $basename . '[' . $i . '].' . $this->upload['ext'];
                $check = $dir . DIRECTORY_SEPARATOR . $filename;
                if (!@is_file($check)) {
                    return $filename;
                }
                $i++;
            }
        }
    }

    /**
     * Unique file names in the form: 9022210413b75410c28bef.html
     * @see HTTP_Upload_File::setName()
     */
    public function nameToUniq()
    {
        $uniq = uniqid(rand());
        return $uniq;
    }

    /**
     * Format a file name to be safe
     *
     * @param    string $file   The string file name
     * @param    int    $maxlen Maximun permited string lenght
     * @return   string Formatted file name
     * @see HTTP_Upload_File::setName()
     */
    public function nameToSafe($name, $maxlen=250)
    {
        $noalpha = 'ÁÉÍÓÚÝáéíóúýÂÊÎÔÛâêîôûÀÈÌÒÙàèìòù&Auml;ËÏ&Ouml;&Uuml;&auml;ëï&ouml;&uuml;ÿÃãÕõÅåÑñÇç@°ºªÞþÆæ';
        $alpha   = 'AEIOUYaeiouyAEIOUaeiouAEIOUaeiouAEIOUaeiouyAaOoAaNnCcaooaTtAa';

        $name = substr($name, 0, $maxlen);
        $name = strtr($name, $noalpha, $alpha);
        // not permitted chars are replaced with "_"
        return preg_replace('/[^a-zA-Z0-9,._\+\()\-]/', '_', $name);
    }

    /**
     * The upload was valid
     *
     * @return bool If the file was submitted correctly
     */
    public function isValid()
    {
        if ($this->upload['error'] === null) {
            return true;
        }
        return false;
    }

    /**
     * User haven't submit a file
     *
     * @return bool If the user submitted a file or not
     */
    public function isMissing()
    {
        if ($this->upload['error'] == 'NO_USER_FILE') {
            return true;
        }
        return false;
    }

    /**
     * Some error occured during upload (most common due a file size problem,
     * like max size exceeded or 0 bytes long).
     * @return bool If there were errors submitting the file (probably
     *              because the file excess the max permitted file size)
     */
    public function hasError()
    {
        if (in_array($this->upload['error'], array('TOO_LARGE', 'BAD_FORM','DEV_NO_DEF_FILE'))) {
            return true;
        }
        return false;
    }

    /**
     * Moves the uploaded file to its destination directory.
     *
     * @param  string  $dir  Destination directory
     * @param  bool    $overwrite Overwrite if destination file exists?
     * @return mixed   True on success or PEAR_Error object on error
     */
    public function moveTo($dir, $overwrite = true)
    {
        if (!$this->isValid()) {
            $error = $this->raiseError($this->upload['error']);
            return $error;
        }

        //Valid extensions check
        if (!$this->_evalValidExtensions()) {
            $error = $this->raiseError('NOT_ALLOWED_EXTENSION');
            return $error;
        }

        $err_code = $this->_chkDirDest($dir);
        if ($err_code !== false) {
            $error = $this->raiseError($err_code);
            return $error;
        }
        // Use 'safe' mode by default if no other was selected
        if (!$this->mode_name_selected) {
            $this->setName('safe');
        }

        //test to see if we're working with sequence naming mode
        if (isset($this->_modeNameSeq) && isset($this->_modeNameSeq['flag']) && $this->_modeNameSeq['flag'] === true) {
            $this->upload['name'] = $this->_modeNameSeq['prepend'] . $this->nameToSeq($dir) . $this->_modeNameSeq['append'];
        }

        $name = $dir . DIRECTORY_SEPARATOR . $this->upload['name'];

        if (@is_file($name)) {
            if ($overwrite !== true) {
                $error = $this->raiseError('FILE_EXISTS');
                return $error;
            } elseif (!is_writable($name)) {
                $error = $this->raiseError('CANNOT_OVERWRITE');
                return $error;
            }
        }

        // copy the file and let php clean the tmp
        if (!@move_uploaded_file($this->upload['tmp_name'], $name)) {
            $error = $this->raiseError('E_FAIL_MOVE');
            return $error;
        }
        @chmod($name, $this->_chmod);
        $prop = $this->getProp('name');
        return $prop;
    }

    /**
     * Check for a valid destination dir
     *
     * @param    string  $dir_dest Destination dir
     * @return   mixed   False on no errors or error code on error
     */
    protected function _chkDirDest($dir_dest)
    {
        if (!$dir_dest) {
            return 'MISSING_DIR';
        }
        if (!@is_dir($dir_dest)) {
            return 'IS_NOT_DIR';
        }
        if (!is_writeable($dir_dest)) {
            return 'NO_WRITE_PERMS';
        }
        return false;
    }
    /**
     * Retrive properties of the uploaded file
     * @param string $name   The property name. When null an assoc array with
     *                       all the properties will be returned
     * @return mixed         A string or array
     * @see HTTP_Upload_File::HTTP_Upload_File()
     */
    public function getProp($name = null)
    {
        if ($name === null) {
            return $this->upload;
        }
        return $this->upload[$name];
    }

    /**
     * Returns a error message, if a error occured
     * (deprecated) Use getMessage() instead
     * @return string    a Error message
     */
    public function errorMsg()
    {
        return $this->errorCode($this->upload['error']);
    }

    /**
     * Returns a error message, if a error occured
     * @return string    a Error message
     */
    public function getMessage()
    {
        return $this->errorCode($this->upload['error']);
    }

    /**
     * Returns an array with all valid file extensions.
     *
     * @return array Array of extensions without dot.
     */
    public function getValidExtensions()
    {
        return $this->_extensionsCheck;
    }

    /**
     * Function to restrict the valid extensions on file uploads.
     * Restrictions are applied to the name of the file on the user's
     * disk, not the destination file name used at moveTo().
     *
     * @param array $exts File extensions to validate
     * @param string $mode The type of validation:
     *                       1) 'deny'   Will deny only the supplied extensions
     *                       2) 'accept' Will accept only the supplied extensions
     *                                   as valid
     * @param bool $case_sensitive whether extension check is case sensitive.
     *                             When it is case insensitive, the extension
     *                             is lowercased before compared to the array
     *                             of valid extensions.
     */
    public function setValidExtensions($exts, $mode = 'deny', $case_sensitive = null)
    {
        $this->_extensionsCheck = $exts;
        $this->_extensionsMode  = $mode;
        if ($case_sensitive !== null) {
            $this->_extensionsCaseSensitive  = $case_sensitive;
        }
    }

    /**
     * Adds an extension to the previously set list of valid extensions
     * @param string $ext
     */
    public function addValidExtension($ext)
    {
        $this->_extensionsCheck[] = $ext;
    }

    /**
     * Does this file object have a valid extension?
     * @return bool
     */
    public function hasValidExtension()
    {
        return $this->_evalValidExtensions();
    }

    /**
     * Evaluates the validity of the extensions set by setValidExtensions.
     * Checks the validity of the file extension of the original filename
     * the user used for the file on his disk.
     *
     * @return bool False on non valid extension, true if they are valid
     */
    protected function _evalValidExtensions()
    {
        $exts = $this->_extensionsCheck;
        settype($exts, 'array');

        $extensionsToCheck = array_merge(array($this->getProp('ext')), $this->getProp('extra_ext'));

        if (!$this->_extensionsCaseSensitive) {
            //$ext = strtolower($ext);
            foreach($extensionsToCheck as $key => $extension) {
                $extensionsToCheck[] = strtolower($extension);
            }
        }
        if ($this->_extensionsMode == 'deny') {
            foreach ($extensionsToCheck as $extension) {
                if (in_array($extension, $exts)) {
                    return false;
                }
            }
            return true;
        // mode == 'accept'
        } else {
            foreach ($extensionsToCheck as $extension) {
                if (in_array($extension, $exts)) {
                    return true;
                }
            }
            return false;
        }
    }
}
