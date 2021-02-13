=== Automate Twilio Sync for Gravityforms ===
Tags: GravityForms, Gravity Forms, Twilio, SMS, MMS
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: WordPress 3.6, Gravity form 1.9.14
Tested up to: 4.4.1
Stable tag: 1.0


== Description ==

With this plugin, you can trigger SMS/MMS messages from gravity form entries.

Shamelessly forked with gratitude from https://github.com/rtCamp/automate-slack-invite-gravityforms

= Requirements =

1. Gravity Form plugin
2. Twilio account


= How this plugin works =

1. Set the Twilio account SID, API token, and "from" phone number at Gravity Form -> Settings -> Twilio.
2. Now, create a gravity form, go to Form Setting -> Twilio and click on "Create one" link to set configuration.
3. Specify a message (with optional merge tags) and a recipient phone number (either hardcoded or merged from a form field)
4. Optionally specify one or more image URLs (newline-separated) to make it an MMS instead of an SMS
4. Once the user submits a gravity form, an SMS/MMS will be triggered.

Development of this plugin is done on [GitHub](https://github.com/thethirdbearsolutions/automate-twilio-gravityforms). You can report issues and suggest features.


== Changelog ==

= 1.0 =
* First release! Everything is new.
