<?php

// Tools needed for this mode
$required_tools = array('wp' => 'WP-CLI');
if($variables['ignition_mode'] == 'bedrock') {
  $required_tools['composer'] = 'Composer';
}

foreach($required_tools as $tool => $tool_name) {
  if(exec('command -v '.ignition_arg($tool)) !== '') {
    continue;
  }

  // A dry run doesn't run anything, so it can carry on without the tool
  if(IGNITION_DRY_RUN) {
    ignition_message($tool_name.' ('.$tool.') is not installed', 'Warning', 33);
  } else {
    ignition_fail($tool_name.' ('.$tool.') is not installed', 'Ignition needs '.$tool_name.' on your PATH');
  }
}

// Vanilla installs download WordPress into the current folder
if($variables['ignition_mode'] == 'vanilla' && (file_exists(getcwd().'/wp-load.php') || file_exists(getcwd().'/wp-config.php'))) {
  ignition_fail('WordPress is already in this folder', 'Run Ignition in an empty folder');
}

if($variables['ignition_mode'] == 'bedrock') {

  // Bedrock installs take the site URLs from .env
  foreach(array('WP_HOME', 'WP_SITEURL') as $env_url) {
    if(empty($_ENV[$env_url])) {
      ignition_fail($env_url.' is not set in .env', 'Fill in the .env file before running Ignition');
    }
  }

  // Plugins are installed with Composer, from whichever repository the project uses
  $variables['plugin_prefix'] = bedrock_plugin_prefix(getcwd());
  $may_install_plugins = !empty($config['active_plugins']) || !empty($config['plugins']) || !empty($config['common_plugins']);
  if($variables['plugin_prefix'] === null && $may_install_plugins) {
    ignition_fail('composer.json has no WordPress plugin repository', 'Add WP Packages (https://repo.wp-packages.org) or WPackagist (https://wpackagist.org) to its repositories');
  }

}
