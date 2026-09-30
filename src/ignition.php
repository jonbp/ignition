<?php

// Version, filled in from the git tag when the phar is built
const IGNITION_VERSION = '@git_version@';

// Functions
include('inc/functions.php');

// Command Line Options
include('inc/arguments.php');

// Environment Load
include('inc/environment.php');

// Config File Load
include('inc/config.php');

// Welcome
ignition_header($variables['ignition_mode']);

// Pre-flight Checks
include('inc/preflight.php');

// User Inputs
include('inc/inputs.php');

// Load Mode
include('modes/'.$variables['ignition_mode'].'.php');

// Success
include('inc/success.php');

// Final Line Break + Color Reset
lb_cr();
