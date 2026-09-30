<?php

$dry_run_flag = false;

foreach(array_slice($_SERVER['argv'], 1) as $argument) {
  switch($argument) {

    case '-h':
    case '--help':
      echo <<<HELP
      Usage: ignition [--dry-run]

      Sets up a new WordPress site in the current folder. A Bedrock project is
      detected from its .env file, anything else gets a vanilla install.

      Options:
        --dry-run      List the commands instead of running them
        -h, --help     Show this help
        -V, --version  Show the version

      HELP;
      exit(0);

    case '-V':
    case '--version':
      echo 'Ignition '.ignition_version()."\n";
      exit(0);

    case '--dry-run':
      $dry_run_flag = true;
      break;

    default:
      ignition_fail('Unknown option '.$argument, 'Run ignition --help to see the options');

  }
}
