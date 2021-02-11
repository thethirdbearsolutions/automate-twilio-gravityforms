=== Automate ActionKit Sync for Gravityforms ===
Tags: GravityForms, Gravity Forms, ActionKit
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: WordPress 3.6, Gravity form 1.9.14
Tested up to: 4.4.1
Stable tag: 1.0


== Description ==

With this plugin, you can sync gravity form entries to ActionKit actions.

Shamelessly forked with gratitude from https://github.com/rtCamp/automate-slack-invite-gravityforms

= Requirements =

1. Gravity Form plugin
2. ActionKit instance base URL


= How this plugin works =

1. Set the ActionKit instance base URL Gravity Form -> Settings -> ActionKit.
2. Now, create a gravity form, go to Form Setting -> ActionKit and click on "Create one" link to set sync configuration.
3. Specify an ActionKit page name and a field mapping. Your field mapping should include "email" at minimum!
4. Once the user submits a gravity form, an action will be created with the corresponding parameters on the designated ActionKit page.


Note: Your Gravity Form must have an email field and it must be manually mapped to ActionKit's "email" parameter (unless you are doing something
fancier involving an akid parameter) 

Development of this plugin is done on [GitHub](https://github.com/thethirdbearsolutions/automate-actionkit-gravityforms). You can report issues and suggest features.


== Changelog ==

= 1.0 =
* First release! Everything is new.
