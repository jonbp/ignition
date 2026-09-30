<?php

// Discourage Search Engines
ignition_command('wp option update blog_public 0');

// Remove the 'Hello world!' post and sample page, where they exist
foreach(array('post' => 'hello-world', 'page' => 'sample-page') as $defaultType => $defaultSlug) {
  $defaultID = ignition_query('wp post list --post_type='.$defaultType.' --name='.$defaultSlug.' --field=ID --format=ids', '{'.$defaultSlug.'-id}');
  if($defaultID !== '') {
    ignition_command('wp post delete '.$defaultID.' --force');
  }
}

// Pages are credited to the admin user
$adminID = ignition_query('wp user get '.ignition_arg($variables['wpuser']).' --field=ID', '{admin-id}');

// Create a 'Home' page and pages from the comma seperated input, skipping blank names
$basePagesArray = array_filter(array_map('trim', explode(',', $variables['base_pages'])), 'strlen');
$page_ids = array();
foreach(array_merge(array('Home'), $basePagesArray) as $pageNumber => $singlePageTitle) {
  $page_ids[] = ignition_query('wp post create --post_type=page --post_status=publish --post_author='.ignition_arg($adminID).' --post_title='.ignition_arg($singlePageTitle).' --porcelain', '{page-'.($pageNumber + 1).'-id}');
}
$homeID = $page_ids[0];
$page_ids = array_values(array_filter($page_ids, 'strlen'));
$page_count = count($page_ids);

// Set the Front Page to our new 'Home' page
if($homeID !== '') {
  ignition_command('wp option update show_on_front page');
  ignition_command('wp option update page_on_front '.$homeID);
}

// Rewrite Structure + Flush URLs
ignition_command('wp rewrite structure '.ignition_arg('/%postname%/').' --hard');
ignition_command('wp rewrite flush --hard');
