<?php

// Use Symfony Yaml
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

// Set Default Values
$config['debug'] = false;
$config['wpuser'] = '';
$config['wpuser_email'] = '';
$config['wpuser_fname'] = '';
$config['wpuser_sname'] = '';
$config['db_name'] = '';
$config['db_user'] = '';
$config['db_pass'] = '';
$config['locale'] = '';
$config['active_plugins'] = array();
$config['plugins'] = array();
$config['common_plugins'] = array();

// Config Path. $XDG_CONFIG_HOME is checked first, then ~/.config.
$homeDir = rtrim((string) getenv('HOME'), '/');
$configPaths = array_unique(array_filter(array(
  getenv('XDG_CONFIG_HOME') ? rtrim(getenv('XDG_CONFIG_HOME'), '/').'/ignition/config.yml' : '',
  $homeDir.'/.config/ignition/config.yml'
)));

foreach($configPaths as $configPath) {

  // Check if config file exists
  if(!is_file($configPath)) {
    continue;
  }

  // Parse YAML Config File
  try {
    $configRead = Yaml::parseFile($configPath);
  } catch(ParseException $e) {
    ignition_fail('Could not read '.$configPath, $e->getMessage());
  }

  // Build $config variable (skip empty keys so defaults are kept)
  foreach((array) $configRead as $key => $value) {
    if($value !== null) {
      $config[$key] = $value;
    }
  }

  break;

}

// Plugin lists, in case the config gives a single name
$config['active_plugins'] = (array) $config['active_plugins'];
$config['plugins'] = (array) $config['plugins'];
$config['common_plugins'] = array_values(array_unique(array_filter(array_map('strval', (array) $config['common_plugins']), 'strlen')));

// Dry run, from --dry-run or debug: true in the config file
define('IGNITION_DRY_RUN', $dry_run_flag || filter_var($config['debug'], FILTER_VALIDATE_BOOLEAN));
