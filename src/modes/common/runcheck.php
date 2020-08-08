<?php

// Runcheck
if($variables['run'] == 'y') {

  // Run Script
  $input_step_count++;
  task_message('Running Install...', 'Step '.$input_step_count, 34);

} else {

  // Abort Script
  task_message('Install Aborted', 'Error', 31);
  lb_cr();
  exit;

}

// Add common plugins to array
if($variables['cp_classiceditor'] == 'y') {
  array_push($config['active_plugins'], 'classic-editor');
}
if($variables['cp_woocommerce'] == 'y') {
  array_push($config['active_plugins'], 'woocommerce');
}
if($variables['cp_disablecomments'] == 'y') {
  array_push($config['active_plugins'], 'disable-comments');
}
if($variables['cp_disablesearch'] == 'y') {
  array_push($config['active_plugins'], 'disable-search');
}