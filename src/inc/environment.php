<?php

// Check if Vanilla or Bedrock Installation
if (file_exists(getcwd() . '/.env')) {
  $dotenv = Dotenv\Dotenv::create(getcwd());
  $dotenv->load();
  task_message('Bedrock Installation', 'Mode', 90, false);
  $variables['ignition_mode'] = 'bedrock';
} else {
  task_message('Vanilla Installation', 'Mode', 90, false);
  $variables['ignition_mode'] = 'vanilla';
}