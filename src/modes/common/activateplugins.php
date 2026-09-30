<?php

// Loop through and activate plugins, skipping any that failed to install
foreach(array_diff($config['active_plugins'], $failed_plugins) as $activePlugin) {
  ignition_command('wp plugin activate '.ignition_arg($activePlugin));
}
