# For Maintainers

This file contains extra information about the project for maintainers.

It extends the [for-devs.md](https://github.com/prosopo/procaptcha-wordpress-plugin/blob/main/for-devs.md) file,
covering maintaining-related aspects.

## 1. Git Workflow

### 1.1) Branches

* `main` - the primary branch, for active development
* `release` - for the release candidates only

### 1.2) GitHub Actions

* `pull_request on release` - runs the tests
* `tag on release` - deploys the plugin to the WordPress SVN repository, tag must follow the `1.0.0` format

So after the release candidate is ready, open a pull request from `main` to `release`, and after the tests are passed,
merge it to the `release`
branch and add the version tag to deploy.

> Note: it's still your necessity to update the version in the `prosopo-procaptcha.php`, `readme.txt` and
`src/Plugin.php` files.

## 2. One command packages installation

After cloning the repository, run `bash tools/install-tools.sh`. It'll install all the necessary composer and npm
packages at once.

> Make sure you've the [wp-cli](https://wp-cli.org/) installed and the `wp` command is available in the terminal (for
> working with the translation files).

## 3. Translations management

In the `prosopo/procaptcha/lang` folder:

1. `.pot` file containing all the strings available for translation.
2. `.po` files made based on the `.pot` for each language. They're supposed to be filled by editors or a tool.
3. `.mo` and `.l10n.php` files compiled from the `.po` files. They're used by WordPress (`.mo` is used by old WP
   installations, `.l10n.php` by new).

Commands:

1. `bash tools/refresh-translations.sh` - updates the `.pot` and `.po` files based on the plugin codebase.
2. `bash tools/compile-translations.sh` - compiles the `.po` files to `.mo` and `.l10n.php` files.

## 4. End-to-End tests

The integrations are covered by [Cypress](https://www.cypress.io/) e2e tests. These tests are integrated
into GitHub Actions but can also be run locally.

> Tip: you can use the [LocalWP](https://localwp.com/) for quickly spin it up.

* WordPress should be setup on `http://procaptcha.local`
* The expected WordPress installation location is `$HOME/Local Sites/procaptcha/app/public`.
* The ready DB for tests should be imported from the private storage.

Launching:

`bash tools/run-tests.sh [all|wordpress|form-plugins] [local]`
