<?php
// ********************************************** //
// This software is licensed by the LGPL
// -> http://www.gnu.org/copyleft/lesser.txt
// (c) 2001- 2004 by Tomas Von Veschler Cox //
// ********************************************** //
// $Id$

require_once (dirname(dirname(dirname(__FILE__))).'/PEAR.php');

/**
 * defines default chmod
 */
define('HTTP_UPLOAD_DEFAULT_CHMOD', 0660);

/**
 * Error Class for HTTP_Upload
 *
 * @author  Tomas V.V.Cox <cox@idecnet.com>
 * @see http://vulcanonet.com/soft/index.php?pack=uploader
 * @package HTTP_Upload
 * @category HTTP
 * @access public
 */
class HTTP_Upload_Error extends PEAR
{
    /**
     * Selected language for error messages
     * @var string
     */
    protected $lang = 'en';

    /**
     * Whether HTML entities shall be encoded automatically
     * @var boolean
     */
    protected $html = false;

    /**
     * @var array
     */
    protected $error_codes;

    /**
     * PHP5 Constructor
     *
     * Creates a new PEAR_Error
     *
     * @param string $lang The language selected for error code messages
     */
    public function __construct($lang = null, $html = false)
    {
        $this->lang = ($lang !== null) ? $lang : $this->lang;
        $this->html = ($html !== false) ? $html : $this->html;
        $raw_size = ini_get('upload_max_filesize');
        $ini_size = intval($raw_size);
        switch (strtoupper(substr($raw_size, -1))) {
            case 'G': $ini_size *= 1024;
            case 'M': $ini_size *= 1024;
            case 'K': $ini_size *= 1024;
        }

        if (function_exists('version_compare') &&
            version_compare(phpversion(), '4.1', 'ge')) {
            $maxsize = (isset($_POST['MAX_FILE_SIZE'])) ?
                $_POST['MAX_FILE_SIZE'] : null;
        } else {
            global $HTTP_POST_VARS;
            $maxsize = (isset($HTTP_POST_VARS['MAX_FILE_SIZE'])) ?
                $HTTP_POST_VARS['MAX_FILE_SIZE'] : null;
        }

        if (empty($maxsize) || ($maxsize > $ini_size)) {
            $maxsize = $ini_size;
        }
        $this->_maxsize = $maxsize;
        // XXXXX Add here error messages in your language

                $UPLOAD_MESSAGE = array();
                global $UPLOAD_MESSAGE;

                // check if module language file exists for the language set by the user (e.g. DE, EN)
                if(dirname(dirname(dirname(__FILE__))).'/languages/'.LANGUAGE .'.php') {
                        // no module language file exists for the language set by the user, include default module language file EN.php
                        require_once(dirname(dirname(dirname(__FILE__))).'/languages/EN.php');
                } else {
                        // a module language file exists for the language defined by the user, load it
                        require_once(dirname(dirname(dirname(__FILE__))).'/languages/'.LANGUAGE .'.php');
                }

        $UPLOAD_MESSAGE['TOO_LARGE'] = sprintf($UPLOAD_MESSAGE['TOO_LARGE'],$maxsize);
        $this->error_codes = $UPLOAD_MESSAGE;

        $this->_loadLanguage('en');
        $this->_loadLanguage($lang);
    }

    /**
     * PHP4 Constructor
     *
     * @see __construct()
     */
    public function HTTP_Upload_Error($lang = null, $html = false)
    {
        self::__construct($lang, $html);
    }

    /**
     * returns the error code
     *
     * @param    string $e_code  type of error
     * @return   string          Error message
     */
    public function errorCode($e_code)
    {
        if (!empty($this->error_codes[$this->lang][$e_code])) {
            $msg = $this->html ?
                html_entity_decode($this->error_codes[$this->lang][$e_code]) :
                $this->error_codes[$this->lang][$e_code];
        } else {
            $msg = $e_code;
        }

        if (!empty($this->error_codes[$this->lang]['ERROR'])) {
            $error = $this->error_codes[$this->lang]['ERROR'];
        } else {
            $error = $this->error_codes['en']['ERROR'];
        }
        return $error.' '.$msg;
    }

    /**
     * Overwrites the PEAR::raiseError method
     *
     * @param    string $e_code      type of error
     * @return   object PEAR_Error   a PEAR-Error object
     */
    public function raiseError($e_code)
    {
        return PEAR::raiseError($this->errorCode($e_code), $e_code);
    }


    /**
     * Loads language strings into error codes variable
     *
     * @param string $lang Language code (2-letter or pt_BR)
     *
     * @return mixed PEAR_Error on error, boolean true if all went well
     */
    protected function _loadLanguage($lang)
    {
        //prepare some variables
        $maxsize = $this->_maxsize;

        //when running from svn
        $local = dirname(__FILE__) . '/../data/' . $lang . '.php';
        if (file_exists($local)) {
            include $local;
        } else {
            include_once 'PEAR/Config.php';
            $dataf = PEAR_Config::singleton()->get('data_dir')
                . '/HTTP_Upload/' . $lang . '.php';
            if (!file_exists($dataf)) {
                //that's a bad error here
                return PEAR::raiseError('Language file could not be loaded');
            }
            include $dataf;
        }

        if (!isset($errorCodes[$lang])) {
            return PEAR::raiseError(
                'No language found in ' . $lang . ' language file'
            );
        }
        $this->error_codes[$lang] = $errorCodes[$lang];
        return true;
    }
}
