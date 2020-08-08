<?php

# Switch Language if Locale set in config
if(!empty($config['locale'])) {

  # Install + Activate Language
  ignition_command('wp language core install '.$config['locale']);
  ignition_command('wp site switch-language '.$config['locale']);

  # Language Updates
  ignition_command('wp language core update');
  ignition_command('wp language plugin update --all');
  ignition_command('wp language theme update --all');

}