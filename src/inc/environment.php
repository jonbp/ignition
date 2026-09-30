<?php

// Check if Vanilla or Bedrock Installation
if (file_exists(getcwd() . '/.env')) {
  $dotenv = Dotenv\Dotenv::createImmutable(getcwd());
  $dotenv->load();
  $variables['ignition_mode'] = 'bedrock';
} else {
  $variables['ignition_mode'] = 'vanilla';
}
