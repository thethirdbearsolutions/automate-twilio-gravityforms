<?php
/* 
Plugin Name: Gravityforms ActionKit Automation
Plugin URI: https://thirdbearsolutions.com
Description: Gravity Forms add-on to automatically sync submissions to ActionKit
Version: 1.0
Author: Third Bear Solutions
Author URI: https://thirdbearsolutions.com
Text Domain: automate-actionkit-gravityforms
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

add_action('gform_loaded', array('GF_Actionkit', 'load'), 5);

define ( 'GF_ACTIONKIT_VERSION', '1.0' );
class GF_Actionkit {

    public static function load(){
        require_once('public/class-automate-actionkit-gravityforms.php');
        $automate_actionkit_gravityforms = new automate_actionkit_gravityforms_public();
    }

}

function gf_actionkit() {
    return automate_actionkit_gravityforms_public::get_instance();
}