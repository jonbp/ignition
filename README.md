# Ignition

<a href="https://github.com/jonbp/ignition"><img alt="WP-CLI Sync" src="https://jonbp.github.io/project-icons/ignition.svg" align="right" /></a>

[![GitHub Open Issues](https://img.shields.io/github/issues-raw/jonbp/ignition)](https://github.com/jonbp/ignition/issues)
[![GitHub Open Pull Requests](https://img.shields.io/github/issues-pr-raw/jonbp/ignition)](https://github.com/jonbp/ignition/pulls)

## About

The WordPress Launch System

![Screenshot](https://jonbp.github.io/project-screenshots/ignition.png)

Ignition harnesses the power of [WP-CLI](https://github.com/wp-cli/wp-cli) to quickly set up a WordPress site by using the command line. Using it drastically speeds up the creation of a new WordPress site.

Ignition achieves this by doing the following:

* Creates a new database
* Creates admin user
* Sets up and activates a base set of plugins
* Creates a base menu and page structure
* Clears out base WordPress junk (e.g. Hello World post and Hello Dolly Plugin)
* Uses a config file for common settings

## Requirements

* PHP 8.2 or later
* [WP-CLI](https://github.com/wp-cli/wp-cli) &mdash; As this project is uses WP-CLI heavily, the requirements for this plugin match that of WP-CLI
* [Composer](https://getcomposer.org) &mdash; Bedrock mode only, for installing plugins

## Installation

To install Ignition download the latest ignition.phar file from the [releases page](https://github.com/jonbp/ignition/releases).

Locate the ignition.phar file you just downloaded and run the following commands in the parent folder:

```
chmod +x ignition.phar
sudo mv ignition.phar /usr/local/bin/ignition
```

## Options

```
ignition [--dry-run]
```

* `--dry-run` &mdash; Lists the commands Ignition would run instead of running them
* `-h`, `--help` &mdash; Shows the options
* `-V`, `--version` &mdash; Shows the version

## Modes

Ignition uses two different modes of installation, Vanilla mode for general WordPress installs or Bedrock mode for [Bedrock](https://github.com/roots/bedrock) projects.

Ignition will detect which mode is needed so there is no need to manually select a mode.

### Vanilla Mode

To use Ignition in vanilla mode, simply create a new folder for your new site (usually the public folder inside a project) and then run `ignition`

Easy!

### Bedrock Mode

Firstly, you’ll need to set up your [Bedrock](https://github.com/roots/bedrock) project. Run this command to start your project:

```
composer create-project roots/bedrock project-name
```

Once this is complete, open the folder and populate the fields inside of `.env`. When this is complete, you can then run `ignition`. The site URL is taken from `WP_HOME`, and the database details from `DB_NAME`, `DB_USER`, `DB_PASSWORD` and `DB_HOST`.

Plugins are added with `composer require`, from whichever WordPress plugin repository the project's `composer.json` uses: [WP Packages](https://wp-packages.org) (new Bedrock projects) or [WPackagist](https://wpackagist.org) (older ones).

## Config File

You can also use a config file to define a base set of plugins or details. This config file comes in the form of a YAML file located at `~/.config/ignition/config.yml` (or `$XDG_CONFIG_HOME/ignition/config.yml` if you've set `XDG_CONFIG_HOME`). Values from the config file are used instead of asking for them.

Here’s an example of this file:

```yaml
# Admin User Details
wpuser: jonbp
wpuser_email: me@jonbp.co.uk
wpuser_fname: Jon
wpuser_sname: Beaumont-Pike

# Common Database Details - Only relevant for vanilla installs
db_user: dbuser
db_pass: dbpass

# Locale
locale: en_GB

# Plugins (Activated on install)
active_plugins:

  - crop-thumbnails
  - autoptimize
  - simple-history
  - wp-sweep
  - two-factor

# Plugins
plugins:

  - autodescription
  - ga-google-analytics
  - wp-super-cache

# Common Plugins (Asked about on each install, activated if chosen)
common_plugins:

  - woocommerce
  - advanced-custom-fields
  - wordpress-seo
```

Plugins are listed by their slug, the last part of their WordPress.org URL (e.g. `wordpress-seo` for https://wordpress.org/plugins/wordpress-seo/). Ignition looks up each common plugin's name on WordPress.org to ask about it, e.g. "Install Yoast SEO?".

## Building

The Ignition project uses [Box](https://github.com/humbug/box) for building as a PHAR file. To get started, run `composer install` in the project. Once that's finished, you can use `composer compile` to build the PHAR file.

Box is installed in its own `vendor-bin/box` folder by [composer-bin-plugin](https://github.com/bamarni/composer-bin-plugin), so its dependencies stay out of the PHAR. The version shown by `ignition --version` comes from the latest git tag.

Adding `debug: true` to the config file does the same as `--dry-run`.

## Previous Projects

Ignition is the product of my former projects [Flint](https://github.com/jonbp/flint) and [Hopper](https://github.com/jonbp/hopper). These were written as shell scripts. They’re archived now but still available for reference.
