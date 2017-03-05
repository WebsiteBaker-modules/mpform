<?php
/**
 * WebsiteBaker CMS module: mpForm
 * ===============================
 * This module allows you to create customised online forms, such as a feedback form with file upload and customizable email notifications. mpForm allows forms over one or more pages, loops of forms, conditionally displayed sections within a single page, and many more things.  User input for the same session_id will become a single row in the submitted table.  Since Version 1.1.0 many ajax helpers enable you to speed up the process of creating forms with this module. Since 1.2.0 forms can be imported and exported directly in the module.
 *  
 * @category            page
 * @module              mpform
 * @version             1.3.8
 * @authors             Frank Heyne, NorHei(heimsath.org), Christian M. Stefan (Stefek), Martin Hecht (mrbaseman) and others
 * @copyright           (c) 2009 - 2017, Website Baker Org. e.V.
 * @url                 http://forum.websitebaker.org/index.php/topic,28496.0.html
 * @url                 https://github.com/WebsiteBaker-modules/mpform
 * @url                 https://forum.wbce.org/viewtopic.php?id=661
 * @license             GNU General Public License
 * @platform            2.8.x
 * @requirements        php >= 5.3
 *
 **/
/* English language Help file. */
// manually include the config.php file (defines the required constants)
require('../../config.php');

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die(header('Location: ../../index.php'));

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// Include WB admin wrapper script
$admin_header = FALSE;

// Include admin wrapper script
require(WB_PATH.'/modules/admin.php');

// 2nd level...
require_once(WB_PATH.'/framework/class.admin.php');

// include core functions to edit the optional module CSS files (frontend.css, backend.css)
@include_once(WB_PATH .'/framework/module.functions.php');

require_once(dirname(__FILE__).'/constants.php');

// redirect if local docs has been found
if(file_exists(dirname(__FILE__).'/docs/index.php')){

  $sUrlToGo='docs/index.php';

  if (defined("LANGUAGE")){
      if(file_exists(dirname(__FILE__).'/docs/'.LANGUAGE.'/mpform.html')){
              $sUrlToGo='docs/'.LANGUAGE.'/mpform.html';
      }
  }

 if(headers_sent())
    echo '<!DOCTYPE HTML>'
         . '<html lang="en-US">'
         . '<head>'
         . '  <meta charset="UTF-8">'
         . '  <meta http-equiv="refresh" content="1;url='.$sUrlToGo.'">'
         . '  <script type="text/javascript">'
         . '    window.location.href = "'.$sUrlToGo.'";'
         . '  </script>'
         . '  <title>mpForm Help</title>'
         . '</head>'
         . '<body>'
         . '  If you are not redirected automatically, please' 
         . '  <a href="'.$sUrlToGo.'">click here</a>'
         . '</body>'
         . '</html>';
 else 
   header("Location: ". $sUrlToGo);
 exit(0);
}

// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;

//START HEADER HERE
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');
//$adm = module_header_footer($page_id, $mod_dir);    
$admin->print_header();

//END HEADER HERE

?>
<div class="helppage">
    <?php if(LANGUAGE == 'DE') : ?>
    
    <p>Die Dokumentation zu diesem Modul umfasst mittlerweile etwa 20 Seiten und wird mit dem Modul mit ausgeliefert. Sie finden sie unter <a target="help" href="docs/DE/mpform.html">diesem Link</a>.
Das Modul wird jedoch auch in Versionen ausgeliefert wird, in denen die Dokumentation nicht enthalten ist. In diesem Fall m&uuml;ssen Sie sich diese separat besorgen.
</p>
    
    <?php else : ?>
    
    <p>The help and documentation for this module now consists of approximately 20 pages and it is delivered together with the module. You can find it <a target="help" href="docs/EN/mpform.html">here</a>. 
However, there are also packages available in which the documentation is not included. In that case you would have to obtain the documentation in a separate file.
</p> 
    <?php endif; ?>
</div>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td align="center">
            <input type="button" class="mod_mpform_button" value="<?php echo $TEXT['BACK']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" />
        </td>
    </tr>
</table>
<?php
$admin->print_footer();
