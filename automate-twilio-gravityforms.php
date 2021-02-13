<?php
/* 
Plugin Name: Gravityforms Twilio Automation
Plugin URI: https://thirdbearsolutions.com
Description: Gravity Forms add-on to trigger Twilio SMS messages upon submission
Version: 1.0
Author: Third Bear Solutions
Author URI: https://thirdbearsolutions.com
Text Domain: automate-twilio-gravityforms
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

add_action('gform_loaded', array('GF_Twilio', 'load'), 5);

define ( 'GF_TWILIO_VERSION', '1.0' );
class GF_Twilio {

    public static function load(){
        require_once('public/class-automate-twilio-gravityforms.php');
        $automate_twilio_gravityforms = new automate_twilio_gravityforms_public();
    }

}

function gf_twilio() {
    return automate_twilio_gravityforms_public::get_instance();
}
