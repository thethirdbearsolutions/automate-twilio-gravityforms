<?php

/**
 * This class use to render form settings and use to handel form submission call.
 *
 * @package    Gravityforms Twilio Automation
 * @author     Third Bear Solutions
 */

GFForms::include_feed_addon_framework();

require_once( plugin_dir_path( __FILE__ ) . '../twilio-lib/src/Twilio/autoload.php');
use Twilio\Rest\Client;

class automate_twilio_gravityforms_public extends GFFeedAddOn {

    protected $_version = GF_TWILIO_VERSION;
    protected $_min_gravityforms_version = '1.9.12';
    protected $_slug = 'automate-twilio-gravityforms';
    protected $_path = 'automate-twilio-gravityforms/automate-twilio-gravityforms.php';
    protected $_full_path = __FILE__;
    protected $_url = 'https://www.thirdbearsolutions.com';
    protected $_title = 'GravityForms Twilio Automation Add-On';
    protected $_short_title = 'Twilio';
    protected $_enable_rg_autoupgrade = false;
    protected $api = null;
    private static $_instance = null;

    /* Permissions */
    protected $_capabilities_settings_page = 'gravityforms_twilio';
    protected $_capabilities_form_settings = 'gravityforms_twilio';
    protected $_capabilities_uninstall = 'gravityforms_twilio_uninstall';

    /* Members plugin integration */
    protected $_capabilities = array( 'gravityforms_twilio', 'gravityforms_twilio_uninstall' );

    /**
     * Get instance of this class.
     *
     * @access public
     * @static
     * @return automate_twilio_gravityforms_public
     */
    public static function get_instance() {

        if ( self::$_instance == null ) {
            self::$_instance = new self;
        }

        return self::$_instance;

    }

    /**
     * @access public
     * @return void
     */
    public function init() {

        parent::init();
    }

    /**
     * Setup plugin settings fields.
     *
     * @access public
     * @return array
     */
    public function plugin_settings_fields() {

        return array(
            array(
                'title'       => '',
                'description' => $this->plugin_settings_description(),
                'fields'      => array(
                    array(
                        'name'              => 'twilio_sid',
                        'label'             => esc_html__( 'Twilio SID', 'automate-twilio-gravityforms' ),
                        'type'              => 'text',
                        'class'             => 'large',
                        'feedback_callback' => array( $this, 'initialize_api' )
                    ),
                    array(
                        'name'              => 'twilio_token',
                        'label'             => esc_html__( 'Twilio token', 'automate-twilio-gravityforms' ),
                        'type'              => 'text',
                        'class'             => 'large',
                        'feedback_callback' => array( $this, 'initialize_api' )
                    ),
                    array(
                        'name'              => 'from_phone_number',
                        'label'             => esc_html__( 'From phone number', 'automate-twilio-gravityforms' ),
                        'type'              => 'text',
                        'class'             => 'large',
                        'feedback_callback' => array( $this, 'initialize_api' )
                    ),
                    array(
                        'type'              => 'save',
                        'messages'          => array(
                            'success' => esc_html__( 'Twilio settings have been updated.', 'automate-twilio-gravityforms' )
                        ),
                    ),
                ),
            ),
        );

    }

    /**
     * Prepare plugin settings description.
     *
     * @access public
     * @return string $description
     */
    public function plugin_settings_description() {

        $description  = '<p>';
        $description .= sprintf(
            esc_html__( 'Twilio' ),
        );
        $description .= '</p>';

        if ( ! $this->initialize_api() ) {

            $description .= '<p>';
            $description .= sprintf(
                esc_html__( 'Gravity Forms Twilio Add-On requires API key settings.', 'automate-twilio-gravityforms' ),
            );
            $description .= '</p>';

        }

        return $description;

    }

    /**
     * Setup fields for feed settings.
     *
     * @access public
     * @return array $settings
     */
    public function feed_settings_fields() {
        $settings = array(
            array(
                'title' =>	'Send Twilio SMS/MMS',
                'fields' =>	array(
                    array(
                        'name'           => 'message',
                        'label'          => esc_html__( 'Message', 'automate-twilio-gravityforms' ),
                        'type'           => 'text',
                        'required'       => true,
                        'class'          => 'medium merge-tag-support mt-position-right mt-hide_all_fields',
                        'tooltip'        => $this->tooltip_for_feed_setting( 'message' ),
                    ),
                    array(
                        'name'           => 'phone_number',
                        'label'          => esc_html__( 'Form field with recipient phone number', 'automate-twilio-gravityforms' ),
                        'type'           => 'text',
                        'required'       => true,
                        'class'          => 'medium merge-tag-support mt-position-right mt-hide_all_fields',
                        'tooltip'        => $this->tooltip_for_feed_setting( 'phone_number' ),
                    ),
                    array(
                        'name'           => 'mms_attachments',
                        'label'          => esc_html__( 'Optional URL(s) to MMS image attachment, one per line', 'automate-twilio-gravityforms' ),
                        'type'           => 'textarea',
                        'required'       => true,
                        'class'          => 'medium',
                        'tooltip'        => $this->tooltip_for_feed_setting( 'mms_attachments' ),
                    ),                   
                    array(
                        'type'  => 'feed_condition',
                        'name'  => 'sync_condition',
                        'label' => 'Conditional trigger',
                    )
                )
            )
        );
        return $settings;

    }

    /**
     * Get feed tooltip.
     *
     * @access public
     * @param array $field
     * @return string
     */
    public function tooltip_for_feed_setting( $field ) {

        /* Setup tooltip array */
        $tooltips = array();
        
        /* Return desired tooltip */
        return $tooltips[ $field ];

    }

    /**
     * Set feed creation control.
     *
     * @access public
     * @return bool
     */
    public function can_create_feed() {

        return $this->initialize_api();

    }

    /**
     * Setup columns for feed list table.
     *
     * @access public
     * @return array
     */
    public function feed_list_columns() {

        return array(
            '' => esc_html__( 'Options', 'automate-twilio-gravityforms' ),
            'message' => esc_html__( 'Message', 'automate-twilio-gravityforms' ),
        );

    }
    /**
     * Get value for Page Name feed list column.
     *
     * @access public
     * @param array $feed
     * @return string
     */
    public function get_column_value_page_name( $feed ) {

        return $feed['meta']['page'];
    }

    /**
     * Process feed.
     *
     * @access public
     * @param array $feed
     * @param array $entry
     * @param array $form
     * @return void
     */
    public function process_feed( $feed, $entry, $form ) {

        $this->log_debug( __METHOD__ . '(): Processing feed.' );

        if ( ! $this->initialize_api() ) {
            $this->add_feed_error( esc_html__( 'Feed was not processed because API was not initialized.', 'automate-twilio-gravityforms' ), $feed, $entry, $form );
            return;
        }

        $message = GFCommon::replace_variables( $feed['meta']['message'], $form, $entry, false, false, false, 'text' );
        $to = GFCommon::replace_variables( $feed['meta']['phone_number'], $form, $entry, false, false, false, 'text' );

        $payload = array(
            "from" => $this->get_plugin_setting( 'from_phone_number' ),
            "body" => $message,
        );

        if( $feed['meta']['mms_attachments'] ) {
            $payload["mediaUrl"] = preg_split("/\r\n|\n|\r/", trim($feed['meta']['mms_attachments']) );
        }

        try {
            $response = $this->api->messages->create(
                $to, $payload
            );
            $this->add_note( $entry['id'],
                             esc_html__( json_encode($response),
                                         'automate-twilio-gravityforms' ),
                             'success' );
        } catch (Exception $e) {
            $this->add_feed_error( esc_html__( $e->getMessage(),
                                               'automate-twilio-gravityforms' ), 
                                   $feed, $entry, $form );
        }

    }

    /**
     * Initializes Twilio API
     *
     * @access public
     * @return bool
     */
    public function initialize_api() {

        if ( ! is_null( $this->api ) )
            return true;

        $twilio_sid = $this->get_plugin_setting( 'twilio_sid' );
        $twilio_token = $this->get_plugin_setting( 'twilio_token' );
        $from_phone_number = $this->get_plugin_setting( 'from_phone_number' );

        if ( rgblank( $twilio_sid ) || rgblank( $twilio_token ) )
            return null;

        $client = new Client($twilio_sid, $twilio_token);

        $this->api = $client;

        return true;
    }

}
