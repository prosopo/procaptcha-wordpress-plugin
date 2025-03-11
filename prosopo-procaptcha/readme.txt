=== Prosopo Procaptcha ===
Contributors: 1prosopo
Tags: Captcha, Procaptcha, antispam, anibot, spam.
Requires at least: 5.5
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.12.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

GDPR compliant, privacy-friendly, and better-value CAPTCHA for your WordPress website.

== Description ==

[Prosopo Procaptcha](https://prosopo.io/) is a GDPR-compliant, privacy-first CAPTCHA solution, offering seamless bot protection without compromising user data.

== Key Features of Procaptcha ==

* **Private & GDPR Friendly** - [No data storage](https://prosopo.io/articles/stop-giving-your-website-data-away/) ensures full compliance with privacy laws.
* **Seamless Integration** - A drop-in replacement for reCaptcha and hCaptcha, allowing setup within minutes.
* **Customizable Defense** - Easily adjust bot protection settings to meet your site's specific needs.
* **Affordable** - Enjoy a top-value CAPTCHA solution with a generous [free tier](https://prosopo.io/pricing/).

== Why Use Procaptcha in WordPress? ==

* **Official WordPress Plugin** - Specifically built for WordPress, ensuring secure and reliable integration.
* **Multiple Built-In Integrations** - Works seamlessly with core WordPress forms and popular form plugins. Supported list provided below.
* **Multilingual** - Completely translated into German (DE), Spanish (ES), French (FR), Italian (IT), and Portuguese (PT).
* **Documentation** - [Plugin documentation](https://docs.prosopo.io/en/wordpress-plugin/) is available to help you get the most out of the plugin.

== Third-Party Service Notice ==

For proper functionality, the plugin loads the [Prosopo Procaptcha](https://prosopo.io/) JavaScript on your chosen forms to display the CAPTCHA on the client side.

Upon form submission, the plugin communicates with the [Prosopo Procaptcha](https://prosopo.io/) API server-side to verify the CAPTCHA response.

Please review the [Prosopo Privacy Policy](https://prosopo.io/privacy-policy/) and [Terms and Conditions](https://prosopo.io/terms-and-conditions/) to fully understand data handling practices.

== Supported forms ==

**Form Plugins**:

1. [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) - Add the `[prosopo_procaptcha]` field to your form.
2. [Everest Forms](https://wordpress.org/plugins/everest-forms/) - Add the `Prosopo Procaptcha` field to your form (the `Advanced Fields` group).
3. [Fluent Forms](https://wordpress.org/plugins/fluentform/) - Add the `Prosopo Procaptcha` field to your form (the `Advanced Fields` group).
4. [Formidable Forms](https://wordpress.org/plugins/formidable/) - Add the `Prosopo Procaptcha` field to your form.
5. [Gravity Forms](https://www.gravityforms.com/) - Add the `Prosopo Procaptcha` field to your form (the `Advanced Fields` group).
6. [Ninja Forms](https://wordpress.org/plugins/ninja-forms/) - Add the `Prosopo Procaptcha` field to your form (the `Miscellaneous` group).
7. [User Registration](https://wordpress.org/plugins/user-registration/) - Add the `Prosopo Procaptcha` field to your form (the `Extra Fields` group).
8. [WP Forms](https://wordpress.org/plugins/wpforms-lite/) - Add the `Prosopo Procaptcha` field to your form (the `Standard Fields` group).

**WordPress Core Forms**:

1. Login
2. Registration
3. Lost Password
4. Comments
5. Post/Page password protection

Visit the plugin settings to enable protection for these forms.

**Account Plugins**:

The Procaptcha plugin is officially compatible with the following plugins, that extend the WordPress account features:

Security:
1. [All-In-One Security](https://wordpress.org/plugins/all-in-one-wp-security-and-firewall/) (Hide backend feature)
2. [LWS Hide Login](https://wordpress.org/plugins/lws-hide-login/) (Hide backend feature)
3. [Login With Ajax](https://wordpress.org/plugins/login-with-ajax/) (AJAXify Login Forms feature)
4. [Security Optimizer](https://wordpress.org/plugins/sg-security/) (Hide backend feature)
5. [Solid Security](https://wordpress.org/plugins/better-wp-security/) (Hide backend feature)
6. [WPS Hide Login](https://wordpress.org/plugins/wps-hide-login/)

Branding:
1. [Branda White Labeling](https://wordpress.org/plugins/branda-white-labeling/)
2. [Custom Login](https://wordpress.org/plugins/custom-login/)
3. [Login Customizer](https://wordpress.org/plugins/login-customizer/)
4. [Login Designer](https://wordpress.org/plugins/login-designer/)
5. [Login Page Customizer by Colorlib](https://wordpress.org/plugins/colorlib-login-customizer/)
6. [Loginpress](https://wordpress.org/plugins/loginpress/)
7. [Theme My Login](https://wordpress.org/plugins/theme-my-login/)
8. [Ultimate Dashboard](https://wordpress.org/plugins/ultimate-dashboard/)
9. [White label](https://wordpress.org/plugins/white-label/)
10. [YITH Custom Login](https://wordpress.org/plugins/yith-custom-login/)

Account-related:
1. [Simple Membership](https://wordpress.org/plugins/simple-membership/)
2. [User Registration](https://wordpress.org/plugins/user-registration/)

While only the items above are tested, overall, the Procaptcha plugin supports all the plugins with custom account forms that use the native WordPress account hooks.

**Other Integrations**:

1. [BBPress](https://wordpress.org/plugins/bbpress/) -  Account forms from [the shortcodes](https://codex.bbpress.org/features/shortcodes/); Forum forms: Open the target forum settings to enable topic and reply forms protection.
2. [Elementor Pro](https://elementor.com/) - Form widgets -  use the `Prosopo Procaptcha` field. Login widgets: use the `Prosopo Procaptcha` checkbox in the `Form Fields` tab (Login must be enabled in the plugin settings->Account forms).
3. [JetPack](https://wordpress.org/plugins/jetpack/) (Forms) - Gutenberg form block: Add the Group block with the `prosopo_procaptcha` shortcode inside the target form block.
4. [WooCommerce](https://wordpress.org/plugins/woocommerce/) - My Account forms; Classic Checkout, Blocks Checkout, Order Tracking forms: enable protection in the `WooCommerce` tab of the plugin settings.
5. [Spectra](https://wordpress.org/plugins/ultimate-addons-for-gutenberg/) - Form block - add hidden input with the `prosopo_procaptcha` name

**Built-In Integrations**:

Some of the plugins have Procaptcha support in its core, so you don't need this plugin to use Procaptcha with them:

1. [Mailchimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/)
2. [Site reviews](https://wordpress.org/plugins/site-reviews/)

More integrations coming soon!

== Screenshots ==

1. Customize the CAPTCHA appearance and behavior through the plugin settings.
2. Easily manage integration-specific settings.
3. Monitor usage statistics without leaving the plugin.
4. Seamless integration with multiple vendors and forms.

== Frequently Asked Questions ==

= Where is the plugin Docs?   =

You can access the plugin documentation in the official [Prosopo Procaptcha documentation](https://docs.prosopo.io/en/wordpress-plugin/).

= My form plugin is missing. Can you add it? =

Please start a thread in the [support forum](https://wordpress.org/support/plugin/prosopo-procaptcha/). We'll review your request and consider adding support for your form plugin.

= The plugin is not available in my language. Can you translate it? =

Please start a thread in the [support forum](https://wordpress.org/support/plugin/prosopo-procaptcha/). We'll review your request and consider adding support for your language.

= Can I contribute? =

Absolutely! The plugin has a [public GitHub repository](https://github.com/prosopo/procaptcha-wordpress-plugin), and we would be excited to have your contribution 🤝

== Changelog ==

= 1.13.0 (2024-03-10) =
- Added support for [Spectra](https://wordpress.org/plugins/ultimate-addons-for-gutenberg/)

= 1.12.0 (2024-03-06) =
- Updated Statistics page to use the new API

= 1.11.0 (2024-02-18) =
- Updated JetPack integration to support the latest JetPack form changes

= 1.10.0 (2024-01-16) =
- Added support for [Simple Membership](https://wordpress.org/plugins/simple-membership/) register, login and reset password forms

= 1.9.0 (2024-12-10) =
- Removed the local Shadow DOM from the widget (in favor of the global one)
- Added support for [Elementor Forms and Login widgets](https://elementor.com/)

= 1.8.1 (2024-12-02) =
- Introduced Statistics page

= 1.8.0 (2024-11-25) =
- Improved the settings page
- Added support for [WooCommerce](https://wordpress.org/plugins/woocommerce/)

= 1.7.0 (2024-11-13) =
- Added support for [User Registration](https://wordpress.org/plugins/user-registration/) plugin

= 1.6.0 (2024-11-11) =
- Added support for [Everest Forms](https://wordpress.org/plugins/everest-forms/)

= 1.5.0 (2024-11-07) =
- Added support for [Jetpack](https://wordpress.org/plugins/jetpack/) (forms)

= 1.4.0 (2024-11-06) =
- Wrapped widget into WebComponent with Shadow DOM to prevent style conflicts
- Added 'require from authorized' UI setting
- Added support for [WPForms](https://wordpress.org/plugins/wpforms-lite/)

= 1.3.0 (2024-10-30) =
- Added support for [bbPress](https://wordpress.org/plugins/bbpress/)
- Added support for [Gravity Forms](https://www.gravityforms.com/)

= 1.2.0 (2024-10-24) =
- Added support for [Formidable Forms](https://wordpress.org/plugins/formidable/)

= 1.1.0 (2024-10-22) =
- Added support for [Ninja Forms](https://wordpress.org/plugins/ninja-forms/)
- Added support for [Fluent Forms](https://wordpress.org/plugins/fluentform/)

= 1.0.1 (2024-10-16) =
- Screenshots and readme updates

= 1.0.0 (2024-10-16) =
- Initial release
