<?php

// Add Name to Admin User
ignition_command('wp user update '.$variables['wpuser'].' --first_name="'.$variables['wpuser_fname'].'" --last_name="'.$variables['wpuser_sname'].'" --display_name="'.$variables['wpuser_fname'].' '.$variables['wpuser_sname'].'"');