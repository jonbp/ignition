<?php

// Validation
$validate_username = function($value) {
  // The characters WordPress allows in a username
  if(!preg_match('/^[A-Za-z0-9 _.@-]+$/', $value)) {
    return 'Use only letters, numbers, spaces and _ . - @';
  }
  return strlen($value) > 60 ? 'Use 60 characters or fewer' : null;
};
$validate_email = function($value) {
  return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : 'Enter a valid email address';
};
$validate_url = function($value) {
  return (preg_match('#^https?://#i', $value) && filter_var($value, FILTER_VALIDATE_URL)) ? null : 'Enter a full URL, starting with http:// or https://';
};

// Database is created unless database.php finds it already exists
$variables['db_action'] = 'create';

// Database Details Inputs - Only relevant for vanilla installs
if($variables['ignition_mode'] == 'vanilla') {
  ignition_heading('Database');
  $variables['db_name'] = ignition_input('Database name', true);
  $variables['db_user'] = ignition_input('Database user', true, $config['db_user']);
  $variables['db_pass'] = ignition_input('Database password', true, $config['db_pass']);
  $variables['db_host'] = 'localhost';
  include('database.php');
}

// Database Details - Bedrock installs read them from .env
if($variables['ignition_mode'] == 'bedrock' && !empty($_ENV['DB_NAME'])) {
  ignition_heading('Database');
  $variables['db_name'] = ignition_input('Database name', true, $_ENV['DB_NAME']);
  $variables['db_user'] = $_ENV['DB_USER'] ?? '';
  $variables['db_pass'] = $_ENV['DB_PASSWORD'] ?? '';
  $variables['db_host'] = $_ENV['DB_HOST'] ?? 'localhost';
  include('database.php');
}

// Admin Details Inputs
ignition_heading('WordPress Admin User');
$variables['wpuser'] = ignition_input('Username', true, $config['wpuser'], $validate_username);
$variables['wpuser_email'] = ignition_input('Email address', true, $config['wpuser_email'], $validate_email);
$variables['wpuser_fname'] = ignition_input('Forename', true, $config['wpuser_fname']);
$variables['wpuser_sname'] = ignition_input('Surname', true, $config['wpuser_sname']);

// Site Information Inputs
ignition_heading('Site');
if($variables['ignition_mode'] == 'vanilla') {
  $variables['site_url'] = ignition_input('Site URL (include http:// or https://)', true, '', $validate_url);
} elseif($variables['ignition_mode'] == 'bedrock') {
  $variables['site_url'] = ignition_input('Site URL', true, $_ENV['WP_HOME'], $validate_url);
}
$variables['site_name'] = ignition_input('Site name', true);
$variables['tagline'] = ignition_input('Tagline');
$variables['base_pages'] = ignition_input('Base pages (separate page names with commas)');

// Common Plugin Inputs, from common_plugins in the config file. Plugins that
// are always installed aren't asked about.
$variables['common_plugins'] = array();
$common_plugins = array_diff($config['common_plugins'], $config['active_plugins'], $config['plugins']);
if(!empty($common_plugins)) {
  ignition_heading('Common Plugins');
  foreach(plugin_names(array_values($common_plugins)) as $slug => $name) {
    $label = $name === null ? 'Install '.$slug.' (not found on WordPress.org)?' : 'Install '.$name.'?';
    if(ignition_confirm($label)) {
      $variables['common_plugins'][] = $slug;
    }
  }
}

// Final Check
ignition_heading('Final Check');
$variables['run'] = ignition_confirm('Are you ready to proceed?');
