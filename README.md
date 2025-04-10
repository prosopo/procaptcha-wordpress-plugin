## Prosopo Procaptcha Integration for WordPress

Welcome to the GitHub repository for
the [Prosopo Procaptcha Integration for WordPress](https://wordpress.org/plugins/prosopo-procaptcha/)!

This repository serves as a GitHub mirror of
the [official WordPress SVN repository](http://plugins.svn.wordpress.org/prosopo-procaptcha/), designed to streamline
community collaboration and contributions.

We ensure this repository is kept up-to-date with the latest version. Feel free to open issues or submit pull requests
directly here — any changes made will be included in the next official release.

## 1. Installation

The plugin is distributed via the official WordPress Plugin
Repository: [Prosopo Procaptcha](https://wordpress.org/plugins/prosopo-procaptcha/).

To install manually you'll need to clone the repository and build the assets:

1. `git clone git@github.com:prosopo/procaptcha-wordpress-plugin.git .`
2. `cd assets; corepack use yarn@latest; yarn build:all`
2. Now you can copy the `prosopo-procaptcha` directory and add it to your local WordPress installation by placing it in
   the `wp-content/plugins` folder.

## 2. Folders Description

This repository includes both the plugin code and the workflow tools:

- `.github/workflows` - GitHub Actions: (CI/CD) workflows
- `assets` - TypeScript and Sass source files; [ESLint](https://eslint.org/), [Prettifier](https://prettier.io/)
  and [Vite](https://vitejs.dev/) configs
- `data-for-tests` - files involved in GitHub actions
- `php-tools`
-
    * `code-quality`  - composer packages and configs
      for [PHPStan](https://phpstan.org/), [PHPSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
      and [Pest](https://pestphp.com/)
    * `origin-vendors`  - composer dependencies
    * `scoper` - PHP tool for package scoping (as WP doesn't support composer)
- `prosopo-procaptcha` - plugin source code
- `tests` - end-to-end [Cypress](https://cypress.io) tests
- `tools` - bash scripts, used CI/CD or manually.
- `wordpress-org-assets` - images for the WordPress SVN repository

## 3. Related Resources

* [Prosopo Procaptcha Website](https://prosopo.io/)
* [Plugin Documentation](https://docs.prosopo.io/en/wordpress-plugin/)
* [Plugin Support Forum](https://wordpress.org/support/plugin/prosopo-procaptcha/)
* [Plugin SVN Repository](http://plugins.svn.wordpress.org/prosopo-procaptcha/)

## 4. Contribution

We would be excited if you decide to contribute 🤝

Please read the [for-devs.md](https://github.com/prosopo/procaptcha-wordpress-plugin/blob/main/for-devs.md) file for
project guidelines and agreements.
