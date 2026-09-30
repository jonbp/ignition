<?php

// WordPress Database Creation + Setup
if($variables['db_action'] === 'reset') {
  ignition_command('wp db reset --yes', true);
} elseif($variables['db_action'] !== 'existing') {
  ignition_command('wp db create', true);
}
ignition_command('wp core install --url='.ignition_arg($variables['site_url']).' --title='.ignition_arg($variables['site_name']).' --admin_user='.ignition_arg($variables['wpuser']).' --admin_password='.ignition_arg($admin_password).' --admin_email='.ignition_arg($variables['wpuser_email']).' --skip-email', true);
ignition_command('wp option update blogdescription '.ignition_arg($variables['tagline']));
