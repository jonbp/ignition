<?php

// Runcheck
if(!$variables['run']) {
  ignition_fail('Install aborted');
}

// Run Script
$install_start = microtime(true);
$fail_count = 0;

// Add the chosen common plugins to the activated plugins
$config['active_plugins'] = array_merge($config['active_plugins'], $variables['common_plugins']);

// Plugins listed more than once are only installed once, activated if either list says so
$config['active_plugins'] = array_values(array_unique($config['active_plugins']));
$config['plugins'] = array_values(array_diff(array_unique($config['plugins']), $config['active_plugins']));
