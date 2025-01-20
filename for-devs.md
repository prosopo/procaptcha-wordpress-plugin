# For Devs

This file contains information about the project and serves as a guide for those interested in contributing.

## 1. Manual plugin installation

To test your changes, set up a local WordPress installation, clone this repository, and create a symlink to the plugin
directory:

`ln -s /your-local-path-to-repo/prosopo-procaptcha ./wp-content/plugins/prosopo-procaptcha`.

This will add the plugin to the list of installed plugins. Before activating it, make sure to build the assets:

`cd assets; corepack use yarn@latest; yarn build:all`

## 2. Code Style

### 2.1) PHP

The project follows
the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).

Configure your IDE to use the [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) and use the
`php-tools/code-quality/wp-ruleset.xml` config, or run the following
command to automatically fix style issues:

`cd php-tools/code-quality; composer install; composer phpcbf`

### 2.2) JavaScript

The project uses [Prettier](https://prettier.io/) as a formatter. Configure your IDE to use the `assets/.prettierrc`, or
run the following command to automatically fix style issues:

`cd assets; corepack use yarn@latest; yarn prettier:fix`

## 3. Static Code Analysis

### 3.1) PHP

The project uses [PHPStan](https://phpstan.org/) for static analysis. Configure your IDE with the
`php-tools/code-quality/phpstan.neon` config or run the analysis using the following command:

`cd code-quality-tools; composer install; composer phpstan`

### 3.2) JavaScript

The project uses [EELint](https://eslint.org/) for static analysis. Configure your IDE with the
`assets/eslint.config.mjs` config or run the analysis using the following command:

`cd assets; corepack use yarn@latest; yarn lint:fix`

## 4. Pull Requests

Please open your Pull Requests against the `main` branch.

## 5. Advanced

### 5.1) Dynamic scripts debugging

To debug any scripts that come from the plugin (like widget, admin settings page, etc) add `_wp_procaptcha_debug_mode`
item with any value to the localStorage. It'll enable the debug mode.

### 5.2) Full commands list

PHP:

* `bash tools/check-code-quality.sh codesniffer` - Check WordPress Coding Standards
* `bash tools/check-code-quality.sh codebeautifer` - Fix WordPress Coding Standards
* `bash tools/check-code-quality.sh phpstan` - Check PHPStan
* `bash tools/check-code-quality.sh pest` - Run Pest tests

JS:

* `bash tools/check-code-quality.sh eslint` - Check ESLint
* `bash tools/check-code-quality.sh prettier` - Check Prettier
* `cd assets && yarn lint:fix` - Fix ESLint-related issues
* `cd assets && yarn prettier:fix` - Fix Prettier-related issues

Assets compilation:

* `cd assets && yarn build:all` - Build all TypeScript and Sass files.
* `cd assets && yarn watch:settings` - Build & watch for the settings-page assets.
* `cd assets && yarn watch:statistics` - Build & watch for the statistics-page assets.

### 5.3) For maintainers

If you're a package maintainer, read
the [for-maintainers.md](https://github.com/prosopo/procaptcha-wordpress-plugin/blob/main/for-maintainers.md) file
with additional information about the project. 