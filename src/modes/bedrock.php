<?php

// Runcheck + Password Generation
include_once('common/runcheck.php');
include_once('common/passgen.php');

// WordPress Database Creation + Setup
task_start('WordPress');
include_once('common/coresetup.php');
include_once('common/nameuser.php');
task_end('Installed WordPress at '.$variables['site_url']);

// Base Setup
task_start('Pages');
include_once('common/basesetup.php');
task_end('Created '.$page_count.' '.($page_count === 1 ? 'page' : 'pages'));

// Plugins
task_start('Plugins');

// Install Plugins, all in one go
$failed_plugins = array();
$composer_plugins = array_merge($config['active_plugins'], $config['plugins']);
if(!empty($composer_plugins)) {
  $composer_packages = array_map(fn($plugin) => ignition_arg($variables['plugin_prefix'].$plugin), $composer_plugins);
  $composer_installed = ignition_command('composer require --no-interaction '.implode(' ', $composer_packages));

  // Retry one at a time, so one plugin that can't be installed doesn't hold back
  // the rest. Plugins that still fail aren't activated.
  if(!$composer_installed && count($composer_plugins) > 1) {
    array_pop($GLOBALS['task_failures']);
    foreach($composer_plugins as $composer_plugin) {
      if(!ignition_command('composer require --no-interaction '.ignition_arg($variables['plugin_prefix'].$composer_plugin))) {
        $failed_plugins[] = $composer_plugin;
      }
    }
  } elseif(!$composer_installed) {
    $failed_plugins = $composer_plugins;
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
