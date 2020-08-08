<?php

task_message('Site setup complete!', 'Success', 32);
task_message($variables['site_url'], 'Site URL', 97);
task_message($variables['wpuser'], 'Admin Username', 97);
task_message($admin_password, 'Admin Password', 97, false);