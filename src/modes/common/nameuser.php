<?php

// Add Name to Admin User
ignition_command('wp user update '.ignition_arg($variables['wpuser']).' --first_name='.ignition_arg($variables['wpuser_fname']).' --last_name='.ignition_arg($variables['wpuser_sname']).' --display_name='.ignition_arg($variables['wpuser_fname'].' '.$variables['wpuser_sname']));
