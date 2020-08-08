<?php

// Loop through and activate plugins
if(!empty($config['active_plugins'])) {
  foreach($config['active_plugins'] as $activePlugin) {
    ignition_command('wp plugin activate '.$activePlugin);
  }
}