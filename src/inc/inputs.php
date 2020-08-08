<?php

// Define step count value
$input_step_count = 0;

// Database Details Inputs - Only relevant for vanilla installs
if($variables['ignition_mode'] == 'vanilla') {
  $input_step_count++;
  task_message('Database Information', 'Step '.$input_step_count, 34);
  $variables['db_name'] = ignition_input('Database Name', true);
  $variables['db_user'] = ignition_input('Database User', true, $config['db_user']);
  $variables['db_pass'] = ignition_input('Database Password', true, $config['db_pass']);
}

// Admin Details Inputs
$input_step_count++;
task_message('Wordpress Admin User Details', 'Step '.$input_step_count, 34);
$variables['wpuser'] = ignition_input('Admin Username', true, $config['wpuser']);
$variables['wpuser_email'] = ignition_input('Admin Email Address', true, $config['wpuser_email']);
$variables['wpuser_fname'] = ignition_input('Admin Forename', true, $config['wpuser_fname']);
$variables['wpuser_sname'] = ignition_input('Admin Surname', true, $config['wpuser_sname']);

// Site Information Inputs
$input_step_count++;
task_message('Site Information', 'Step '.$input_step_count, 34);
if($variables['ignition_mode'] == 'vanilla') {
  $variables['site_url'] = ignition_input('Site URL (include http:// or https://)', true);
} elseif($variables['ignition_mode'] == 'bedrock') {
  $variables['site_url'] = $_ENV['WP_SITEURL'];
}
$variables['site_name'] = ignition_input('Site Name', true);
$variables['tagline'] = ignition_input('Tagline');
$variables['base_pages'] = ignition_input('Base Pages (Sererate page names with commas)');

// Common Plugin Inputs
$input_step_count++;
task_message('Common Plugins', 'Step '.$input_step_count, 34);
$variables['cp_classiceditor'] = ignition_input('Install Classic Editor? (y/n)');
$variables['cp_woocommerce'] = ignition_input('Install WooCommerce? (y/n)');
$variables['cp_disablecomments'] = ignition_input('Disable Comments? (y/n)');
$variables['cp_disablesearch'] = ignition_input('Disable Search? (y/n)');

// Final Check
$input_step_count++;
task_message('Final Check', 'Step '.$input_step_count, 34);
$variables['run'] = ignition_input('Are you ready to proceed? (y/n)');
