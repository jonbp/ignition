<?php

// Use Symfony Yaml
use Symfony\Component\Yaml\Yaml;

// Set Default Values
$config['debug'] = 'false';
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

// Config Path
$homeDir = exec('eval echo ~$USER');
$configPath = 'file://'.$homeDir.'/.config/ignition/config.yml';

// Check if config file exists
if(file_exists($configPath)) {

  $configFileContents = file_get_contents($configPath);

  // Parse YAML Config File
  $configRead = Yaml::parse($configFileContents);

  // Build $config variable
  foreach($configRead as $key => $value) {
    $config[$key] = $value;
  }

}

// Add debug variable to $_ENV global variable
if($config['debug'] == 'true') {
  $_ENV['debug'] = 'true';
} else {
  $_ENV['debug'] = 'false';
}