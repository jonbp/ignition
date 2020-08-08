<?php

// Runcheck + Password Generation
include_once('common/runcheck.php');
include_once('common/passgen.php');

// WordPress Core Download
ignition_command('wp core download');

// Create WordPress Config File
ignition_command('wp core config --dbname='.$variables['db_name'].' --dbuser='.$variables['db_user'].' --dbpass='.$variables['db_pass']);

// WordPress Database Creation + Setup
include_once('common/coresetup.php');

// Add Name to Admin User
include_once('common/nameuser.php');

// Base Setup
include_once('common/basesetup.php');

// Remove Default Plugins
ignition_command('wp plugin uninstall akismet hello');

// Install  Plugins
if(!empty($config['plugins'])) {
  foreach($config['plugins'] as $installPlugin) {
    ignition_command('wp plugin install '.$installPlugin);
  }
}
if(!empty($config['active_plugins'])) {
  foreach($config['active_plugins'] as $installPlugin) {
    ignition_command('wp plugin install '.$installPlugin);
  }
}

// Activate Plugins
include_once('common/activateplugins.php');

// Menu Creation
include_once('common/menu.php');

// Language Additions + Updates
include_once('common/language.php');