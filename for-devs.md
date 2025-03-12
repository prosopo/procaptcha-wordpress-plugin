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
`php-tools/code-quality/wp-ruleset.xml` config, or run the following command to automatically fix style issues:

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

### 5.1) Assets developing with the Vite HMR

The plugin assets setup uses [Vite](https://vite.dev/) and supports
its [Hot Module Replacement](https://vite.dev/guide/features.html#hot-module-replacement) feature. To use HMR:

1. Start Vite dev server: `cd assets; yarn dev:[settings/integrations]`
2. Update your local `wp-config.php` to add the dev mode constant: `define("PROSOPO_PROCAPTCHA_DEV_MODE", true);`

The dev mode constant tells the plugin to use the Vite dev server (`http://localhost:5173`) as the assets source,
instead of the default `/dist` folder.

Additionally, the plugin will automatically enqueue the Vite reloader script (`http://localhost:5173/@vite/client`) on
pages with any assets in use, so the Vite HMR will function as usually.

### 5.2) Scripts debugging

To debug any scripts that come from the plugin (like widget, admin settings page, etc) add `_wp_procaptcha_debug_mode`
item with any value to the `localStorage`. It'll enable the debug mode.

### 5.3) Full commands list

Common:

1. `bash tools/check-code-quality.sh` - runs all the code-quality checks, for both PHP and JS

JavaScript-related:

`cd assets` and:

1. `yarn build:[all/settings/integrations]` - runs Vite building
2. `yarn dev:[settings/integrations]` - starts Vite dev server
3. `yarn lint:[check/fix]` - runs ESLint static code analyses
4. `yarn prettier:[check/fix]` - runs Prettier formatter

PHP-related:

`cd tools` and:

1. `bash check-code-quality.sh codesniffer` - checks for WordPress Coding Standards violations
2. `bash check-code-quality.sh codebeautifer` - fixes WordPress Coding Standards violations
3. `bash check-code-quality.sh phpstan` - runs PHPStan static code analyses
4. `bash check-code-quality.sh pest` - runs Pest tests

### 5.4) For maintainers

If you're a package maintainer, read
the [for-maintainers.md](https://github.com/prosopo/procaptcha-wordpress-plugin/blob/main/for-maintainers.md) file to
get additional information about the project. 