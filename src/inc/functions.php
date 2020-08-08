<?php

/**
 * Task Message
 */
function task_message($message, $title='Task', $color = 34, $firstBreak = true) {
  if($firstBreak == true) {
    echo "\n";
  }
  echo "\033[".$color."m".$title.": ".$message."\n\033[0m";
}

/**
 * Input Recording
 */
function ignition_input($label, $required = false, $configVar = '') {
  if(empty($configVar)) {
    echo $label.": \e[33m";
    $var = rtrim(fgets(STDIN));
  } else {
    echo $label.": \e[90m";
    echo $configVar;
    $var = $configVar;
    echo "\n";
  }
  echo "\033[0m";

  if(empty($var) && $required == true) {
    task_message('This field is required', 'Error', 31, false);
    return ignition_input($label, $required);
  }

  return $var;
}

/**
 * Command Execution and Debug
 */
function ignition_command($command) {
  if($_ENV['debug'] == 'true') {
    task_message($command, 'Debug', 97, false);
  } else {
    system($command);
  }
}

/**
 * Line Break + Color Reset
 */ 
function lb_cr() {
  echo "\n\033[0m";
}