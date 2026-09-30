<?php

// Completion Message
echo "\n";
if($fail_count > 0) {
  echo ignition_color('! Finished with '.$fail_count.' failed '.($fail_count === 1 ? 'step' : 'steps').' in '.ignition_elapsed($install_start), '1;33')."\n";
} else {
  echo ignition_color('✔ '.(IGNITION_DRY_RUN ? 'Dry run' : 'Setup').' complete in '.ignition_elapsed($install_start), '1;32')."\n";
}

// Site Details
$site_details = array(
  'Site URL' => $variables['site_url'],
  'Admin Username' => $variables['wpuser'],
  'Admin Password' => IGNITION_DRY_RUN ? '(not set in a dry run)' : $admin_password
);
echo "\n";
foreach($site_details as $label => $value) {
  echo '  '.ignition_color(str_pad($label, 16), 90).$value."\n";
}

if($fail_count > 0) {
  lb_cr();
  exit(1);
}
