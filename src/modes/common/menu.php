<?php

// Create Base Navigation
$menuID = ignition_query('wp menu create '.ignition_arg('Main Navigation').' --porcelain', '{menu-id}');

// Add the pages created in basesetup.php, in order
$menu_count = 0;
if($menuID !== '') {
  foreach($page_ids as $pageID) {
    if(ignition_command('wp menu item add-post '.$menuID.' '.$pageID)) {
      $menu_count++;
    }
  }
}
