<?php

/*

 Website Baker Project <http://www.websitebaker.org/>
 Copyright (C) 2004-2009, Ryan Djurovich

 Website Baker is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Website Baker is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Website Baker; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

require('../../../config.php');
require_once(WB_PATH.'/framework/class.admin.php');
require_once(WB_PATH.'/framework/functions.php');

#require_once(dirname(__FILE__) . '/info.php');

$set_field = explode ( "-",$_POST['id']);
$page_field = $set_field[0];
$field_id = $set_field[1];

// suppress to print the header, so no new FTAN will be set
$admin = new admin('Pages', 'pages_settings', false);

//sanitize new value to update
$new_value = str_replace(array("[[", "]]"), '', $admin->add_slashes($admin->get_post('value')));


// must check if user can change things,
// should be checked in tool.php so that user with no rights can't edit
// but also check in save to avoid any hack from a logged in not admin user
if($admin->get_permission('pages_modify') == false ) {
    exit;
}


// Go to save me !
if(isset($new_value) && $page_field == 'field'){
    // Update page settings in the pages table
    $sql
        = 'UPDATE `'.TABLE_PREFIX.'mod_mpform_fields`'
            . ' SET `title` = "'.$new_value.'"'
            . ' WHERE `field_id` = '.$field_id.'';
    $database->query($sql);
}

if($database->is_error()) {

    #exit;
    echo '<b>(nv:'.$new_value.'; field_id: '
          .((method_exists( $admin, 'checkIDKEY' ))
            ? ($admin->checkIDKEY($set_field[1]))
            : ($set_field[1]))
          .'</b> )'
          .$database->get_error();
    #$admin->print_error($database->get_error());
}else {
    echo $new_value;
    // there is a problem when user enter some comma
    // they are escaped and returned escaped :(
}

exit;
return;
