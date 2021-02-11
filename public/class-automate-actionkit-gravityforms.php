<?php

/**
 * This class use to render form settings and use to handel form submission call.
 *
 * @package    Gravityforms ActionKit Automation
 * @author     Third Bear Solutions
 */

GFForms::include_feed_addon_framework();

class automate_actionkit_gravityforms_public extends GFFeedAddOn {

    protected $_version = GF_ACTIONKIT_VERSION;
    protected $_min_gravityforms_version = '1.9.12';
    protected $_slug = 'automate-actionkit-gravityforms';
    protected $_path = 'automate-actionkit-gravityforms/automate-actionkit-gravityforms.php';
    protected $_full_path = __FILE__;
    protected $_url = 'https://www.thirdbearsolutions.com';
    protected $_title = 'GravityForms Actionkit Automation Add-On';
    protected $_short_title = 'Actionkit';
    protected $_enable_rg_autoupgrade = false;
    protected $api = null;
    private static $_instance = null;

    /* Permissions */
    protected $_capabilities_settings_page = 'gravityforms_actionkit';
    protected $_capabilities_form_settings = 'gravityforms_actionkit';
    protected $_capabilities_uninstall = 'gravityforms_actionkit_uninstall';

    /* Members plugin integration */
    protected $_capabilities = array( 'gravityforms_actionkit', 'gravityforms_actionkit_uninstall' );

    /**
     * Get instance of this class.
     *
     * @access public
     * @static
     * @return automate_actionkit_gravityforms_public
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
                        'name'              => 'actionkit_base_url',
                        'label'             => esc_html__( 'Base URL, e.g. https://act.yourdomain.com', 'automate-actionkit-gravityforms' ),
                        'type'              => 'text',
                        'class'             => 'large',
                        'feedback_callback' => array( $this, 'initialize_api' )
                    ),
                    array(
                        'type'              => 'save',
                        'messages'          => array(
                            'success' => esc_html__( 'ActionKit settings have been updated.', 'automate-actionkit-gravityforms' )
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
            esc_html__( 'ActionKit!' ),
        );
        $description .= '</p>';

        if ( ! $this->initialize_api() ) {

            $description .= '<p>';
            $description .= sprintf(
                esc_html__( 'Gravity Forms ActionKit Add-On requires a base URL for your ActionKit instance. This should be in the form https://act.yourdomain.com with no trailing slash and no other URL path.', 'automate-actionkit-gravityforms' ),
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
                'title' =>	'Send submissions to ActionKit',
                'fields' =>	array(
                    array(
                        'name'           => 'page',
                        'label'          => esc_html__( 'Page', 'automate-actionkit-gravityforms' ),
                        'type'           => 'text',
                        'required'       => true,
                        'class'          => 'medium',
                        'tooltip'        => $this->tooltip_for_feed_setting( 'page' ),
                    ),
                    array(
                        'name'           => 'source',
                        'label'          => esc_html__( 'Default Source', 'automate-actionkit-gravityforms' ),
                        'type'           => 'text',
                        'required'       => true,
                        'class'          => 'medium',
                        'tooltip'        => $this->tooltip_for_feed_setting( 'source' ),
                    ),
                    array(
                        'label'   => "Subscription behavior",
                        'type'    => 'checkbox',
                        'name'    => 'suppress_opt_in',
                        'tooltip' => '',
                        'choices' => array(
                            array(
                                'label' => "Don't change subscriptions",
                                'name'  => 'suppress_opt_in'
                            )
                        )
                    ),
                    array(
                        'label'   => 'After-action email behavior',
                        'type'    => 'checkbox',
                        'name'    => 'skip_confirmation',
                        'tooltip' => '',
                        'choices' => array(
                            array(
                                'label' => 'Suppress after-action email',
                                'name'  => 'suppress_email'
                            )
                        )
                    ),
		            array(
                        'name'                => 'metaData',
                        'label'               => esc_html__( 'Additional field mapping', 'automate-actionkit-gravityforms' ),
                        'type'                => 'dynamic_field_map',
                        'limit'               => 20,
                        'exclude_field_types' => '',
                        'tooltip'             => '<h6>' . esc_html__( 'Metadata', 'sometextdomain' ) . '</h6>' . esc_html__( 'Map form fields to ActionKit fields here. Use the ActionKit field names, e.g. first_name, source, action_my_custom_field_name, user_my_custom_field_name, mobile_phone, etc.', 'automate-actionkit-gravityforms' ),
                        //validation_callback' => array( $this, 'validate_custom_meta' ),
                    ),
                    array(
                        'type'  => 'feed_condition',
                        'name'  => 'sync_condition',
                        'label' => 'Conditional sync',
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

        $tooltips['page']  = '<h6>'. __( 'Page to submit to', 'automate-actionkit-gravityforms' ) .'</h6>';
        $tooltips['page'] .= esc_html__( 'ActionKit page name where form submissions should be sent.', 'automate-actionkit-gravityforms' ) . '<br /><br />';

        $tooltips['source']  = '<h6>'. __( 'Default source for synced action', 'automate-actionkit-gravityforms' ) .'</h6>';
        $tooltips['source'] .= esc_html__( 'Can be overridden by form field mapping below.', 'automate-actionkit-gravityforms' ) . '<br /><br />';
        
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
            '' => esc_html__( 'Options', 'automate-actionkit-gravityforms' ),
            'page_name' => esc_html__( 'ActionKit Page', 'automate-actionkit-gravityforms' ),
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

        /* If ActionKit instance is not initialized, exit. */
        if ( ! $this->initialize_api() ) {
            $this->add_feed_error( esc_html__( 'Feed was not processed because API was not initialized.', 'automate-actionkit-gravityforms' ), $feed, $entry, $form );
            return;
        }

        $fields = $this->get_dynamic_field_map_fields($feed, 'metaData');

        $payload = array();
        $payload['page'] = $feed['meta']['page'];
        $payload['source'] = $feed['meta']['source'];

        if ( $feed['meta']['skip_confirmation'] ) {
            $payload['skip_confirmation'] = '1';
        }

        if ( $feed['meta']['suppress_opt_in'] ) {
            $payload['opt_in'] = '1';
        }
        
        foreach($fields as $key => $field_id) {
            $payload[$key] = $this->get_field_value($form, $entry, $field_id);
        }
        
        $result = $this->api->act( $payload );

        $debug = array();
        $debug['result'] = $result;
        $debug['payload'] = $payload;

        if ( rgar( $result['response'], 'redirect_url' ) ) {
            $this->add_note( $entry['id'],
                             esc_html__( json_encode($debug),
                                         'automate-actionkit-gravityforms' ),
                             'success' );
        } else {
            $this->add_feed_error( esc_html__( json_encode($debug),
                                               'automate-actionkit-gravityforms' ), 
                                   $feed, $entry, $form );
        }

    }

    /**
     * Initializes ActionKit API
     *
     * @access public
     * @return bool
     */
    public function initialize_api() {

        if ( ! is_null( $this->api ) )
            return true;

        /* Load the API library. */
        if ( ! class_exists( 'Actionkit_Api' ) ) {
            require_once(__DIR__ . '/../includes/class-automate-actionkit-gravityforms-api.php');
        }

        $base_url = $this->get_plugin_setting( 'actionkit_base_url' );

        if ( rgblank( $base_url ) )
            return null;

        $actionkit = new Actionkit_Api( $base_url );

        $this->api = $actionkit;
        return true;
    }

}
