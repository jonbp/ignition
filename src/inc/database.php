<?php

/**
 * Database Check
 *
 * Checks the login works and whether the database already exists, so an
 * existing database can be overwritten or swapped for another before anything
 * is installed. Vanilla installs can re-enter their details. Bedrock installs
 * read theirs from .env, so they stop instead.
 */
$is_vanilla = $variables['ignition_mode'] == 'vanilla';

while(true) {
  $db_status = database_status($variables['db_host'], $variables['db_user'], $variables['db_pass'], $variables['db_name']);

  // Wrong login
  if($db_status['state'] === 'denied') {
    if(!$is_vanilla) {
      ignition_fail('Can\'t log in to MySQL as '.$variables['db_user'], 'Check DB_USER and DB_PASSWORD in .env');
    }
    ignition_notice('Can\'t log in to MySQL as '.$variables['db_user'], 'error');
    $variables['db_user'] = ignition_input('Database user', true);
    $variables['db_pass'] = ignition_input('Database password', true);
    continue;
  }

  // MySQL not running or not reachable. A dry run carries on without it.
  if($db_status['state'] === 'unavailable') {
    if(IGNITION_DRY_RUN) {
      ignition_notice('Can\'t connect to MySQL, so the database wasn\'t checked');
      break;
    }
    ignition_fail('Can\'t connect to MySQL', 'Check MySQL is running and the database host is right ('.$db_status['message'].')');
  }

  if($db_status['state'] === 'exists') {

    // An empty database is used as it is
    if($db_status['tables'] === 0) {
      ignition_notice('Database '.$variables['db_name'].' already exists but is empty, so it will be used', 'success');
      $variables['db_action'] = 'existing';
      break;
    }

    $db_tables = $db_status['tables'].' '.($db_status['tables'] === 1 ? 'table' : 'tables');
    ignition_notice('Database '.$variables['db_name'].' already exists with '.$db_tables);
    if(ignition_confirm('Overwrite it?')) {
      $variables['db_action'] = 'reset';
      break;
    }

    if(!$is_vanilla) {
      ignition_fail('Install aborted', 'Change DB_NAME in .env to use a different database');
    }
    $variables['db_name'] = ignition_input('Database name', true);
    continue;
  }

  // Missing, so it'll be created, or unchecked
  break;
}
