<?php

# Switch Language if Locale set in config
if(!empty($config['locale'])) {

  task_start('Language');

  # Install + Activate Language
  ignition_command('wp language core install '.ignition_arg($config['locale']));
  ignition_command('wp site switch-language '.ignition_arg($config['locale']));

  # Language Updates
  ignition_command('wp language core update');
  ignition_command('wp language plugin update --all');
  ignition_command('wp language theme update --all');

  task_end('Switched to '.$config['locale']);

}
