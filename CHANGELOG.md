# Changelog

This project adheres to [Semantic Versioning](http://semver.org/).

### 2.0.0: 30/09/2026

* Requires PHP 8.2 or later. Dependencies updated to `vlucas/phpdotenv` 5, `symfony/yaml` 7 and Box 4
* Tidier output: each task gets a heading and a one-line result with its duration, and the install ends with a total time
* Output from WP-CLI and Composer is only shown when something goes wrong
* The install stops if WordPress can't be downloaded, configured or installed, rather than carrying on without it
* Common plugins are now set with `common_plugins` in the config file, and are asked about by their WordPress.org name
* Stronger admin passwords, with special characters
* `--dry-run`, `--help` and `--version` options. `debug: true` in the config file still works as a dry run
* WP-CLI, Composer and an empty folder are checked for before any questions are asked
* The database login is checked before installing, and an existing database can be overwritten or swapped for another
* Email addresses, site URLs and y/n answers are checked as they're entered
* Answers can be edited with the arrow keys before they're entered
* Everything passed to WP-CLI and Composer is shell-escaped, so names and passwords can contain quotes and special characters
* WP-CLI is given enough memory to unpack WordPress when PHP's limit is lower than 512M
* The 'Hello world!' post is removed along with the sample page, and blank base page names are skipped
* `wp config create` replaces the deprecated `wp core config`
* The config file is read from `$XDG_CONFIG_HOME/ignition/config.yml` when set, and invalid YAML gets a clear error
* Ignition exits with code 1 when the install fails or is aborted
* Bedrock plugins are installed from WP Packages on new Bedrock projects, and from WPackagist on older ones
* Bedrock installs use `WP_HOME` as the site URL
* Plugins that fail to install aren't activated, and on Bedrock one plugin that can't be installed no longer holds back the rest
* The summary counts failed steps, and passwords are masked in the commands shown
* Admin usernames are checked against WordPress's rules

### 1.0.0: 08/08/2020

* Initial Release
