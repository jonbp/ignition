<?php

// Discourage Search Engines
ignition_command('wp option update blog_public 0');

// Remove the sample page and create a 'Home' page
ignition_command('wp post delete $(wp post list --post_type=page --posts_per_page=1 --post_status=publish --pagename="sample-page" --field=ID --format=ids)');
ignition_command('wp post create --post_type=page --post_title=Home --post_status=publish --post_author=$(wp user get '.$variables['wpuser'].' --field=ID)');

// Set the Front Page to our new 'Home' page
ignition_command('wp option update show_on_front "page"');
ignition_command('wp option update page_on_front $(wp post list --post_type=page --post_status=publish --posts_per_page=1 --pagename=home --field=ID --format=ids)');

// Create pages from the comma seperated input
$basePagesArray = explode(',', $variables['base_pages']);
foreach($basePagesArray as $singlePageTitle) {
	ignition_command('wp post create --post_type=page --post_status=publish --post_author=$(wp user get '.$variables['wpuser'].' --field=ID) --post_title="'.trim($singlePageTitle).'"');
}

// Rewrite Structure + Flush URLs
ignition_command('wp rewrite structure \'/%postname%/\' --hard');
ignition_command('wp rewrite flush --hard');