<?php

// Create Base Navigation
ignition_command('wp menu create "Main Navigation"');

// Get Page IDs
$pageIDs = shell_exec('wp post list --order="ASC" --orderby="date" --post_type=page --post_status=publish --posts_per_page=-1 --field=ID --format=ids');

// Explode into Array
$pageIDs = explode(' ', $pageIDs);

// Loop through IDs and create menu items
foreach($pageIDs as $pageid) {
  ignition_command('wp menu item add-post main-navigation '.$pageid);
}