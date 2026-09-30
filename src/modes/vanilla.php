<?php

// Runcheck + Password Generation
include_once('common/runcheck.php');
include_once('common/passgen.php');

// WordPress Core Download, Config + Database Setup
task_start('WordPress');
ignition_command('wp core download', true);
ignition_command('wp config create --dbname='.ignition_arg($variables['db_name']).' --dbuser='.ignition_arg($variables['db_user']).' --dbpass='.ignition_arg($variables['db_pass']), true);
include_once('common/coresetup.php');
include_once('common/nameuser.php');
task_end('Installed WordPress at '.$variables['site_url']);

// Base Setup
task_start('Pages');
include_once('common/basesetup.php');
task_end('Created '.$page_count.' '.($page_count === 1 ? 'page' : 'pages'));

// Plugins
task_start('Plugins');

// Remove Default Plugins
ignition_command('wp plugin uninstall akismet hello');

// Install Plugins. Any that fail aren't activated.
$failed_plugins = array();
foreach(array_merge($config['plugins'], $config['active_plugins']) as $installPlugin) {
  if(!ignition_command('wp plugin install '.ignition_arg($installPlugin))) {
    $failed_plugins[] = $installPlugin;
  }
}

// Activate Plugins
include_once('common/activateplugins.php');
task_end(plugin_summary($config));

// Menu Creation
task_start('Menu');
include_once('common/menu.php');
task_end('Created Main Navigation with '.$menu_count.' '.($menu_count === 1 ? 'item' : 'items'));

// Language Additions + Updates
include_once('common/language.php');
