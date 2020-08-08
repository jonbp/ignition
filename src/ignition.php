<?php

// Functions
include('inc/functions.php');

// Welcome
task_message('The WordPress Launch System', 'Ignition v1.0.0', 97);

// Environment Load
include('inc/environment.php');

// Config File Load
include('inc/config.php');

// User Inputs
include('inc/inputs.php');

// Load Mode
include('modes/'.$variables['ignition_mode'].'.php');

// Success
include('inc/success.php');

// Final Line Break + Color Reset
lb_cr();
