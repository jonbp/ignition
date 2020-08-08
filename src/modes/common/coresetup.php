<?php

// WordPress Database Creation + Setup
ignition_command('wp db create');
ignition_command('wp core install --url="'.$variables['site_url'].'" --title="'.$variables['site_name'].'" --admin_user="'.$variables['wpuser'].'" --admin_password="'.$admin_password.'" --admin_email="'.$variables['wpuser_email'].'" --skip-email');
ignition_command('wp option update blogdescription "'.$variables['tagline'].'"');