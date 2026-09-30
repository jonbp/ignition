<?php

/**
 * Version, from the git tag the phar was built from
 */
function ignition_version() {
  return str_starts_with(IGNITION_VERSION, '@') ? 'dev' : IGNITION_VERSION;
}

/**
 * Only colour and redraw lines when writing to a terminal
 */
function ignition_is_tty() {
  static $is_tty = null;
  if($is_tty === null) {
    $is_tty = function_exists('stream_isatty') && stream_isatty(STDOUT);
  }
  return $is_tty;
}

/**
 * Wrap text in an ANSI colour
 */
function ignition_color($text, $color) {
  return ignition_is_tty() ? "\033[".$color."m".$text."\033[0m" : $text;
}

/**
 * Clear the current line, ready to redraw it
 */
function ignition_clear_line() {
  if(ignition_is_tty()) {
    echo "\r\033[2K";
  }
}

/**
 * Width of the terminal
 *
 * Read with stty, from the terminal itself. tput reports a default of 80 when
 * its output is captured, as it is when run from PHP.
 */
function ignition_columns() {
  $size = explode(' ', trim((string) exec('stty size 2>/dev/null')));
  return (int) ($size[1] ?? 0) ?: 80;
}

/**
 * Redraw a single status line, trimmed to the terminal width
 */
function ignition_status_line($text) {
  if(!ignition_is_tty()) {
    return;
  }
  $width = ignition_columns();
  if(function_exists('mb_strimwidth')) {
    $text = mb_strimwidth($text, 0, $width - 1, '…', 'UTF-8');
  } elseif(strlen($text) > $width - 1) {
    $text = substr($text, 0, $width - 4).'...';
  }
  ignition_clear_line();
  echo ignition_color($text, 90);
}

/**
 * Seconds since a microtime(true) start, as a short label
 */
function ignition_elapsed($start) {
  $seconds = microtime(true) - $start;
  return $seconds < 60 ? number_format($seconds, 1).'s' : floor($seconds / 60).'m '.round(fmod($seconds, 60)).'s';
}

/**
 * Banner shown at the start of a run, with the mode and version
 */
function ignition_header($mode) {
  $parts = array(ucfirst($mode).' installation');
  if(IGNITION_DRY_RUN) {
    $parts[] = ignition_color('dry run', 33);
  }
  $parts[] = ignition_color('v'.ignition_version(), 90);

  // Ignition's own colour (#FEC82C) where the terminal supports it, yellow elsewhere
  $brand = in_array(getenv('COLORTERM'), array('truecolor', '24bit'), true) ? '1;38;2;254;200;44' : '1;33';

  echo "\n".ignition_color('○ Ignition', $brand).'  '.implode(ignition_color(' · ', 90), $parts)."\n";
}

/**
 * Section heading, used for both the questions and the install tasks
 */
function ignition_heading($name) {
  echo "\n".ignition_color('› '.$name, '1;34')."\n";
}

/**
 * Standalone message, e.g. the detected mode
 */
function ignition_message($message, $title, $color = 90) {
  echo '  '.ignition_color($title.': ', $color).$message."\n";
}

/**
 * Notice within a section, set apart from the questions around it
 */
function ignition_notice($message, $type = 'warning') {
  $symbols = array(
    'success' => array('✔', 32),
    'warning' => array('!', 33),
    'error' => array('✖', 31)
  );
  list($symbol, $color) = $symbols[$type];

  echo "\n  ".ignition_color($symbol.' '.$message, $color)."\n";
}

/**
 * Stop the run with an error, and an optional hint on fixing it
 */
function ignition_fail($message, $hint = '') {
  echo "\n".ignition_color('✖ '.$message, '1;31')."\n";
  if($hint !== '') {
    ignition_message($hint, 'Hint', 33);
  }
  lb_cr();
  exit(1);
}

/**
 * Read a line of input
 *
 * In a terminal the answer is shown in yellow and can be edited with the arrow
 * keys before it's entered. Elsewhere (e.g. piped input) the line is read as it is.
 */
function ignition_read_line($prompt) {
  $saved_stty = (ignition_is_tty() && stream_isatty(STDIN)) ? trim((string) shell_exec('stty -g 2>/dev/null')) : '';

  if($saved_stty === '') {
    echo $prompt.(ignition_is_tty() ? "\033[33m" : '');
    $line = fgets(STDIN);
    echo ignition_is_tty() ? "\033[0m" : '';
    return $line;
  }

  return ignition_edit_line($prompt, $saved_stty);
}

/**
 * Line editor for ignition_read_line()
 *
 * The terminal is switched to reading a key at a time, and the line is redrawn
 * after each one. Answers too long for the line scroll sideways, and are shown
 * in full once entered. Returns false when the input is closed (Ctrl+D).
 */
function ignition_edit_line($prompt, $saved_stty) {
  static $restore_registered = false;
  $restore = function() use ($saved_stty) {
    exec('stty '.escapeshellarg($saved_stty).' 2>/dev/null');
  };

  // Put the terminal back even if the run ends unexpectedly
  if(!$restore_registered) {
    register_shutdown_function($restore);
    $restore_registered = true;
  }

  // Keys are read one at a time, unechoed, with Ctrl+C read as a key
  exec('stty -icanon -echo -isig min 1 time 0 2>/dev/null');
  stream_set_read_buffer(STDIN, 0);

  $chars = array();
  $pos = 0;
  $columns = ignition_columns();
  $prompt_width = count(preg_split('//u', $prompt, -1, PREG_SPLIT_NO_EMPTY));

  $draw = function($full = false) use (&$chars, &$pos, $prompt, $prompt_width, $columns) {
    if($full) {
      echo "\r".$prompt."\033[33m".implode('', $chars)."\033[0m\033[K";
      return;
    }

    // Scroll sideways to keep the cursor on the line
    $start = max(0, $prompt_width + $pos - $columns + 1);
    $view = array_slice($chars, $start, max(0, $columns - $prompt_width - 1));
    echo "\r".$prompt."\033[33m".implode('', $view)."\033[0m\033[K\r\033[".($prompt_width + $pos - $start)."C";
  };

  $draw();
  $line = false;

  while(true) {
    $key = fread(STDIN, 1);

    // Input closed
    if($key === false || $key === '') {
      break;
    }

    if($key === "\r" || $key === "\n") {
      $draw(true);
      echo "\n";
      $line = implode('', $chars);
      break;
    }

    if($key === "\x03") {
      // Ctrl+C
      $restore();
      echo "\033[0m\n";
      exit(130);
    } elseif($key === "\x04" && empty($chars)) {
      // Ctrl+D on an empty line
      echo "\n";
      break;
    } elseif($key === "\x7f" || $key === "\x08") {
      // Backspace
      if($pos > 0) {
        array_splice($chars, --$pos, 1);
      }
    } elseif($key === "\x04") {
      // Ctrl+D deletes forwards, like Delete
      array_splice($chars, $pos, 1);
    } elseif($key === "\x01") {
      // Ctrl+A
      $pos = 0;
    } elseif($key === "\x05") {
      // Ctrl+E
      $pos = count($chars);
    } elseif($key === "\x15") {
      // Ctrl+U clears back to the start
      array_splice($chars, 0, $pos);
      $pos = 0;
    } elseif($key === "\x0b") {
      // Ctrl+K clears to the end
      array_splice($chars, $pos);
    } elseif($key === "\033") {
      // Arrow, Home, End and Delete keys arrive as escape sequences
      $code = '';
      if(in_array(fread(STDIN, 1), array('[', 'O'), true)) {
        do {
          $byte = fread(STDIN, 1);
          $code .= $byte;
        } while($byte !== false && $byte !== '' && !preg_match('/[A-Za-z~]/', $byte));
      }

      if($code === 'D') {
        $pos = max(0, $pos - 1);
      } elseif($code === 'C') {
        $pos = min(count($chars), $pos + 1);
      } elseif(in_array($code, array('H', '1~', '7~'), true)) {
        $pos = 0;
      } elseif(in_array($code, array('F', '4~', '8~'), true)) {
        $pos = count($chars);
      } elseif($code === '3~') {
        array_splice($chars, $pos, 1);
      }
    } elseif(ord($key) >= 32) {
      // A typed character, reading the rest of it if it's more than one byte
      $extra = ord($key) >= 0xF0 ? 3 : (ord($key) >= 0xE0 ? 2 : (ord($key) >= 0xC0 ? 1 : 0));
      if($extra > 0) {
        $key .= fread(STDIN, $extra);
      }
      array_splice($chars, $pos++, 0, array($key));
    }

    $draw();
  }

  $restore();
  return $line;
}

/**
 * Input Recording
 *
 * A value from the config file is shown instead of asked for, unless it fails
 * validation. $validate returns an error message, or null when the value is fine.
 */
function ignition_input($label, $required = false, $default = '', $validate = null) {
  $value = (string) $default;
  $from_config = $value !== '';

  while(true) {
    if($from_config) {
      echo '  '.$label.': '.ignition_color($value, 90)."\n";
    } else {
      $line = ignition_read_line('  '.$label.': ');

      // Input closed (e.g. Ctrl+D), so there's nothing left to ask for
      if($line === false) {
        echo "\n";
        ignition_fail('Install aborted');
      }

      $value = trim($line);
    }

    if($required && $value === '') {
      $error = 'This field is required';
    } else {
      $error = ($value !== '' && $validate) ? $validate($value) : null;
    }

    if($error === null) {
      return $value;
    }

    echo '  '.ignition_color('✖ '.$error, 31)."\n";
    $from_config = false;
  }
}

/**
 * Yes/no question. A blank answer is a no.
 */
function ignition_confirm($label) {
  while(true) {
    $answer = strtolower(ignition_input($label.' (y/N)'));

    if(in_array($answer, array('y', 'yes'), true)) {
      return true;
    }
    if(in_array($answer, array('', 'n', 'no'), true)) {
      return false;
    }

    echo '  '.ignition_color('✖ Please answer y or n', 31)."\n";
  }
}

/**
 * Task heading. Starts the task's timer and clears its failures.
 */
function task_start($name) {
  $GLOBALS['task_start'] = microtime(true);
  $GLOBALS['task_failures'] = array();
  ignition_heading($name);
}

/**
 * Task outcome, with the time since task_start()
 */
function task_result($message, $type = 'success') {
  $symbols = array(
    'success' => array('✔', 32),
    'warning' => array('!', 33),
    'error' => array('✖', 31)
  );
  list($symbol, $color) = $symbols[$type];

  $time = isset($GLOBALS['task_start']) ? ' '.ignition_color('('.ignition_elapsed($GLOBALS['task_start']).')', 90) : '';

  ignition_clear_line();
  echo '  '.ignition_color($symbol.' '.$message, $color).$time."\n";
}

/**
 * Close a task: the success message, or every command that failed with its output
 */
function task_end($message) {
  $failures = $GLOBALS['task_failures'] ?? array();

  if(empty($failures)) {
    task_result($message);
    return;
  }

  $GLOBALS['fail_count'] = ($GLOBALS['fail_count'] ?? 0) + 1;
  task_result(count($failures).' '.(count($failures) === 1 ? 'command' : 'commands').' failed', 'error');
  foreach($failures as $failure) {
    echo '    '.ignition_color('$ '.ignition_mask($failure['command']), 31)."\n";
    ignition_output($failure['output']);
  }
}

/**
 * Indented block of command output
 */
function ignition_output($output, $max_lines = 12) {
  $lines = array_values(array_filter(array_map('rtrim', (array) $output), 'strlen'));

  // Errors come first, so long output (e.g. a stack trace) is cut short
  foreach(array_slice($lines, 0, $max_lines) as $line) {
    echo '      '.ignition_color($line, 90)."\n";
  }
  if(count($lines) > $max_lines) {
    echo '      '.ignition_color('… '.(count($lines) - $max_lines).' more lines', 90)."\n";
  }
}

/**
 * Summary of the plugins installed and activated
 */
function plugin_summary($config) {
  $installed = count($config['plugins']) + count($config['active_plugins']);
  $activated = count($config['active_plugins']);

  if($installed === 0) {
    return 'No plugins to install';
  }
  return 'Installed '.$installed.' '.($installed === 1 ? 'plugin' : 'plugins').', activated '.$activated;
}

/**
 * Plugin names from WordPress.org, looked up in one request
 *
 * Returns slug => name, with null for a plugin WordPress.org doesn't list. If
 * WordPress.org can't be reached, each slug stands in for its name.
 */
function plugin_names($slugs) {
  if(empty($slugs)) {
    return array();
  }
  $names = array_combine($slugs, $slugs);

  // Only the name is needed, so the larger fields are left out
  $url = 'https://api.wordpress.org/plugins/info/1.2/?'.http_build_query(array(
    'action' => 'plugin_information',
    'request' => array(
      'slugs' => $slugs,
      'fields' => array_fill_keys(array('sections', 'versions', 'screenshots', 'reviews', 'banners', 'icons', 'contributors', 'ratings', 'tags', 'compatibility'), 0)
    )
  ));
  $context = stream_context_create(array('http' => array(
    'timeout' => 5,
    'ignore_errors' => true,
    'user_agent' => 'Ignition/'.ignition_version()
  )));

  ignition_status_line('  Looking up plugin names...');
  $response = json_decode((string) @file_get_contents($url, false, $context), true);
  ignition_clear_line();

  if(!is_array($response)) {
    return $names;
  }

  foreach($slugs as $slug) {
    $info = $response[$slug] ?? null;
    if(isset($info['name'])) {
      // Drop the tagline some plugins add, e.g. 'Yoast SEO – Advanced SEO with...'
      $name = html_entity_decode($info['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
      $names[$slug] = trim(preg_split('/\s+[–—|-]\s+/u', $name)[0]);
    } elseif(isset($info['error'])) {
      $names[$slug] = null;
    }
  }

  return $names;
}

/**
 * Whether a database exists, and how many tables it has
 *
 * Returns a state of 'missing', 'exists' (with 'tables'), 'denied' when the
 * login is wrong, 'unavailable' when MySQL can't be reached (with 'message'),
 * or 'unchecked' when PHP has no mysqli extension to check with.
 */
function database_status($host, $user, $pass, $name) {
  if(!class_exists('mysqli')) {
    return array('state' => 'unchecked');
  }

  // DB_HOST can carry a port or socket, e.g. 'localhost:3307' or 'localhost:/tmp/mysql.sock'
  $port = null;
  $socket = null;
  if(preg_match('#^(.*):(\d+)$#', $host, $matches)) {
    list(, $host, $port) = $matches;
    $port = (int) $port;
  } elseif(preg_match('#^(.*?):(/.+)$#', $host, $matches)) {
    list(, $host, $socket) = $matches;
  }

  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  try {
    $db = new mysqli($host ?: 'localhost', $user, $pass, '', $port, $socket);

    // One row per matching database, so none when it doesn't exist
    $query = $db->prepare('SELECT COUNT(t.TABLE_NAME) FROM INFORMATION_SCHEMA.SCHEMATA s LEFT JOIN INFORMATION_SCHEMA.TABLES t ON t.TABLE_SCHEMA = s.SCHEMA_NAME WHERE s.SCHEMA_NAME = ? GROUP BY s.SCHEMA_NAME');
    $query->bind_param('s', $name);
    $query->execute();
    $row = $query->get_result()->fetch_row();
    $db->close();
  } catch(mysqli_sql_exception $e) {
    // 1045 is a wrong password, 1698 a login MySQL only allows over its socket
    $state = in_array($e->getCode(), array(1045, 1698), true) ? 'denied' : 'unavailable';
    return array('state' => $state, 'message' => $e->getMessage());
  }

  return $row ? array('state' => 'exists', 'tables' => (int) $row[0]) : array('state' => 'missing');
}

/**
 * Composer package prefix for WordPress.org plugins in a Bedrock project
 *
 * Newer Bedrock projects use WP Packages (wp-plugin/<slug>), older ones
 * WPackagist (wpackagist-plugin/<slug>). Null when composer.json has neither.
 */
function bedrock_plugin_prefix($project_dir) {
  $composer = json_decode((string) @file_get_contents($project_dir.'/composer.json'), true);

  foreach((array) ($composer['repositories'] ?? array()) as $repository) {
    $url = is_array($repository) ? (string) ($repository['url'] ?? '') : '';
    if(str_contains($url, 'wp-packages.org')) {
      return 'wp-plugin/';
    }
    if(str_contains($url, 'wpackagist.org')) {
      return 'wpackagist-plugin/';
    }
  }

  return null;
}

/**
 * Quote a value for use in a shell command
 */
function ignition_arg($value) {
  return escapeshellarg((string) $value);
}

/**
 * How to run WP-CLI
 *
 * Unpacking WordPress needs more memory than PHP's default 128M, so WP-CLI is
 * run through PHP with a higher limit, as WP-CLI's docs suggest. A launcher that
 * isn't a PHP file (e.g. Composer's shell script) reads WP_CLI_PHP_ARGS instead.
 */
function ignition_wp() {
  static $wp = null;

  if($wp === null) {
    $limit = ini_get('memory_limit');
    if($limit === '-1' || ini_parse_quantity($limit) >= 512 * 1024 * 1024) {
      return $wp = 'wp';
    }

    $path = exec('command -v wp');
    $head = $path !== '' ? (string) @file_get_contents($path, false, null, 0, 64) : '';

    if(str_starts_with($head, '<?php') || preg_match('/^#!.*\bphp\b/', $head)) {
      $wp = ignition_arg(PHP_BINARY).' -d memory_limit=512M '.ignition_arg($path);
    } else {
      $wp = 'WP_CLI_PHP_ARGS='.ignition_arg('-d memory_limit=512M').' wp';
    }
  }

  return $wp;
}

/**
 * A command as it's shown, with passwords masked
 *
 * Masked by option rather than value, so a password that matches another value
 * (e.g. a database user) doesn't hide that too.
 */
function ignition_mask($command) {
  return preg_replace("/(--(?:admin_password|dbpass)=)'(?:[^']|'\\\\'')*'/", "$1'••••••••'", $command);
}

/**
 * Run a command, returning whether it worked and its output
 *
 * Output is kept back unless the command fails. In a dry run the command is
 * listed instead of run.
 */
function ignition_run($command) {
  if(IGNITION_DRY_RUN) {
    echo '    '.ignition_color('$ '.ignition_mask($command), 90)."\n";
    return array(true, array());
  }

  // Commands are listed as 'wp ...' but run through ignition_wp()
  $run = str_starts_with($command, 'wp ') ? ignition_wp().substr($command, 2) : $command;

  ignition_status_line('    '.ignition_mask($command));
  exec($run.' 2>&1', $output, $status);
  ignition_clear_line();

  // Composer follows an error with the command's usage, which just adds noise
  if(str_starts_with($command, 'composer ')) {
    $output = preg_grep('/^\s*[a-z-]+ \[--/', $output, PREG_GREP_INVERT);
  }

  if($status !== 0) {
    $GLOBALS['task_failures'][] = array('command' => $command, 'output' => $output);
  }

  return array($status === 0, $output);
}

/**
 * Command Execution
 *
 * A $required command is one the rest of the install depends on, so the install
 * stops if it fails rather than carrying on without it.
 */
function ignition_command($command, $required = false) {
  list($success) = ignition_run($command);

  if(!$success && $required) {
    task_end('');
    $retry = ($GLOBALS['variables']['ignition_mode'] ?? '') == 'bedrock' ? 'run Ignition again' : 'run Ignition again in an empty folder';
    ignition_fail('Install stopped', 'Fix the error above, then '.$retry);
  }

  return $success;
}

/**
 * Command whose output is needed, e.g. an ID. A dry run gets $placeholder back.
 */
function ignition_query($command, $placeholder) {
  list($success, $output) = ignition_run($command);

  if(IGNITION_DRY_RUN) {
    return $placeholder;
  }

  // The value is on the last line, below any warnings
  $lines = array_filter(array_map('trim', $output), 'strlen');
  return ($success && $lines) ? end($lines) : '';
}

/**
 * Line Break + Color Reset
 */
function lb_cr() {
  echo "\n".(ignition_is_tty() ? "\033[0m" : '');
}
