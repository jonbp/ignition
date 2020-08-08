<?php

// Runcheck + Password Generation
include_once('common/runcheck.php');
include_once('common/passgen.php');

// WordPress Database Creation + Setup
include_once('common/coresetup.php');

// Add Name to Admin User
include_once('common/nameuser.php');

// Base Setup
include_once('common/basesetup.php');

// Install  Plugins
$composer_part_install = '';
if(!empty($config['active_plugins'])) {
  $composer_active_plugins = preg_filter('/^/', 'wpackagist-plugin/', $config['active_plugins']);
  $composer_part_install = $composer_active_plugins;
}
if(!empty($config['plugins'])) {
  $composer_plugins = preg_filter('/^/', 'wpackagist-plugin/', $config['plugins']);
  $composer_part_install = $composer_plugins;
}
if(!empty($config['plugins']) && !empty($config['active_plugins'])) {
  $composer_plugins_merge = array_merge($composer_active_plugins, $composer_plugins);
  $composer_plugins_output = implode(' ', $composer_plugins_merge);
  ignition_command('composer require '.$composer_plugins_output);
} elseif (!empty($composer_part_install)) {
  $composer_plugins_output = implode(' ', $composer_part_install);
  ignition_command('composer require '.$composer_plugins_output);
}

// Activate Plugins
include_once('common/activateplugins.php');

// Menu Creation
include_once('common/menu.php');

// Language Additions + Updates
include_once('common/language.php');