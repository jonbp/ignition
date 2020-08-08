<h1 align="center">
  <a href="https://github.com/jonbp/ignition"><img alt="Ignition" src="https://jonbp.github.io/project-icons/ignition.svg" width="64" height="64"></a><br />Ignition
</h1>

<p align="center">The WordPress Launch System</p>

<p align="center">
  <img src="https://jonbp.github.io/project-screenshots/ignition.png" />
</p>

## About

Ignition harnesses the power of [WP-CLI](https://github.com/wp-cli/wp-cli) to quickly set up a WordPress site by using the command line. Using it drastically speeds up the creation of a new WordPress site.

Ignition achieves this by doing the following:

* Creates a new database
* Creates admin user
* Sets up and activates a base set of plugins
* Creates a base menu and page structure
* Clears out base WordPress junk (e.g. Hello World post and Hello Dolly Plugin)
* Uses a config file for common settings

## Requirements

* [WP-CLI](https://github.com/wp-cli/wp-cli) &mdash; As this project is uses WP-CLI heavily, the requirements for this plugin match that of WP-CLI

## Installation

To install Ignition download the latest ignition.phar file from the [releases page](https://github.com/jonbp/ignition/releases).

Locate the ignition.phar file you just downloaded and run the following commands in the parent folder:

```
chmod +x ignition.phar
sudo mv ignition.phar /usr/local/bin/ignition
```

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

Once this is complete, open the folder and populate the fields inside of `.env`. When this is complete, you can then run `ignition`.

## Config File

You can also use a config file to define a base set of plugins or details. This config file comes in the form of a YAML file located at `~/.config/ignition/config.yml`.

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
```

## Building

The Ignition project uses [Box](https://github.com/humbug/box) for building as a PHAR file. To get started, run `composer install` in the project. Once that's finished, you can use `composer compile` to build the PHAR file.

You can also add `debug: true` to the config file to see the commands that are run instead of executing them.

## Previous Projects

Ignition is the product of my former projects [Flint](https://github.com/jonbp/flint) and [Hopper](https://github.com/jonbp/hopper). These were written as shell scripts. They’re archived now but still available for reference.
