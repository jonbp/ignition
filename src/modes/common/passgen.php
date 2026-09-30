<?php

// Generate Secure User Password, with at least one of each kind of character
$password_sets = array(
  'abcdefghijklmnopqrstuvwxyz',
  'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
  '0123456789',
  '!@#$%^&*()-_=+[]{}<>?,.;:'
);
$password_all = implode('', $password_sets);

$password_chars = array();
foreach($password_sets as $password_set) {
  $password_chars[] = $password_set[random_int(0, strlen($password_set) - 1)];
}
while(count($password_chars) < 24) {
  $password_chars[] = $password_all[random_int(0, strlen($password_all) - 1)];
}

// Shuffled so the guaranteed characters aren't always first
$admin_password = implode('', (new Random\Randomizer())->shuffleArray($password_chars));
