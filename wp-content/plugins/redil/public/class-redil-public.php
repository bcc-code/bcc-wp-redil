<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://redil.io
 * @since      1.0.0
 *
 * @package    Redil
 * @subpackage Redil/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Redil
 * @subpackage Redil/public
 * @author     Raskenlund <hello@raskenlund.com>
 */
class Redil_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The post types that this plugin applies to
     * 
     */
    private $post_types = array( 'post', 'page' );

    public const GROUP_ALL   = 0;
    public const GROUP_YOUTH = 1;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        $this->load_dependencies();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Redil_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Redil_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/redil-public.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Redil_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Redil_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/redil-public.js', array( 'jquery' ), $this->version, false );

    }

    /**
     * Filters out menu items that the current users shouldn't see.
     *
     * @param WP_Post[] $items
     * @return WP_Post[]
     */
    public function filter_menu_items( $items ) {

        if ( current_user_can( 'edit_posts' ) ) {
            return $items;
        }

        $removed         = array();
        $bcc_user_groups = $this->get_bcc_user_groups();

        foreach ( $items as $key => $item ) {
            // Don't render children of removed menu items.
            if ( in_array( $item->menu_item_parent, $removed, true ) ) {
                $removed[] = $item->ID;
                unset( $items[ $key ] );
                continue;
            }

            if ( in_array( $item->object, $this->post_types, true ) ) {
                $group_id = (int) get_post_meta( $item->object_id, 'redil_group_id', true );

                if (!$group_id) {
                    continue;
                }

                if ( in_array( $group_id, $bcc_user_groups, true ) ) {
                    continue;
                }

                unset( $items[ $key ] );
                $removed[] = $item->ID;
            }
        }

        return $items;
    }

    /**
     * Filters out posts that the current user shouldn't see. This filter
     * applies to category lists and REST API results.
     *
     * @param WP_Query $query
     * @return WP_Query
     */
    function filter_pre_get_posts( $query ) {
        if ( current_user_can( 'edit_posts' ) || $query->is_singular ) {
            return $query;
        }

        // Allow feeds to be accessed using key
        if ( $query->is_feed && ! empty($this->_settings->feed_key) && array_key_exists('id',$_GET) && $this->_settings->feed_key == $_GET['id'] ) {
            return $query;
        }

        // Get original meta query
        $meta_query      = (array)$query->get('meta_query');
        $bcc_user_groups = $this->get_bcc_user_groups();

        // Add visibility rules
        $group_rules = array(
            'key'     => 'redil_group_id',
            'compare' => 'IN',
            'value'   => $bcc_user_groups
        );

        // In the unlikely case the user has no group membership
        if ( count( $bcc_user_groups ) == 0 ) {
            $group_rules = array(
                'key'     => 'redil_group_id',
                'compare' => 'IN',
                'value'   => [ self::GROUP_ALL ]
            );
        }

        // Include posts where redil_group_id isn't specified
        $group_rules = array(
            'relation' => 'OR',
            $group_rules,
            array(
                'key'     => 'redil_group_id',
                'compare' => 'NOT EXISTS'
            )
        );

        $meta_query[] = $group_rules;

        // Set the meta query to the complete, altered query
        $query->set('meta_query', $meta_query);

        return $query;
    }

    private function load_dependencies() {

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-redil-user.php';

    }

    private function get_bcc_user_groups() {
        $user = $this->get_bcc_user();

        // All users are in the Everyone group
        $groups = array( self::GROUP_ALL );

        // Add user to the Youth group
        if ( $this->is_user_in_group( $user, self::GROUP_YOUTH ) )
        {
            $groups[] = self::GROUP_YOUTH;
        }

        return $groups;
    }

    function is_user_in_group($user, $group) {

        // Hard-Coded rules
        // - must be in church "Grenland"
        // - must be between 13 and 36 years old
        // Quite rudimentary for now

        $age = date_diff( date_create( $user->birthdate ), date_create( date('Y-m-d') ), true )->format('%y');

        if ( $group == self::GROUP_YOUTH )
            if ( $user->churchName == 'Grenland' )
                if ( $age > 12 && $age < 37 )
                    return true;

        return false;
    }

    function get_bcc_user() {
        $token_id = $_COOKIE['oidc_token_id'];
        $token    = get_transient( 'oidc_id_token_' . $token_id );
        $parts    = explode('.', $token );
        $json     = base64_decode(str_replace(array( '-', '_' ), array( '+', '/' ), $parts[ 1 ]));
        $data     = json_decode($json, true);
        $user     = new Redil_User();

        $user->churchName = $data[ 'https://login.bcc.no/claims/churchName' ];
        $user->birthdate  = $data[ 'birthdate' ];

        return $user;
    }

}
