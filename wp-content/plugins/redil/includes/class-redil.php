<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://redil.io
 * @since      1.0.0
 *
 * @package    Redil
 * @subpackage Redil/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Redil
 * @subpackage Redil/includes
 * @author     Raskenlund <hello@raskenlund.com>
 */
class Redil {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Redil_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'REDIL_VERSION' ) ) {
            $this->version = REDIL_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'redil';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Redil_Loader. Orchestrates the hooks of the plugin.
     * - Redil_i18n. Defines internationalization functionality.
     * - Redil_Admin. Defines all hooks for the admin area.
     * - Redil_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-redil-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-redil-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-redil-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-redil-public.php';

        /**
         * The class responsible for getting the BCC user object and extracting relevant data
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-redil-user.php';

        /**
         * The class responsible for comparing the ruleset against the user profile
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-redil-comparer.php';

        $this->loader = new Redil_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Redil_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Redil_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Redil_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action('init', $plugin_admin, 'on_init', 1);

        $this->loader->add_action( 'admin_enqueue_styles', $plugin_admin, 'redil_enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'redil_enqueue_scripts' );

        $this->loader->add_action( 'admin_menu', $plugin_admin, 'menu_page' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'redil_add_metabox' );

        $this->loader->add_action( 'wp_nav_menu_item_custom_fields', $plugin_admin, 'on_render_menu_item', 10, 2 );
        $this->loader->add_action( 'wp_update_nav_menu_item', $plugin_admin, 'on_update_menu_item', 10, 2 );

        $this->loader->add_action( 'save_post', $plugin_admin, 'on_save_post' );
        $this->loader->add_action( 'added_post_meta', $plugin_admin, 'on_meta_saved', 10, 4 );
        $this->loader->add_action( 'updated_post_meta', $plugin_admin, 'on_meta_saved', 10, 4 );

        $this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'on_block_editor_assets' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Redil_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action('init', $plugin_public, 'on_init', 1);

        $this->loader->add_action( 'wp_enqueue_styles', $plugin_public, 'redil_enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'redil_enqueue_scripts' );

        $this->loader->add_filter( 'wp_get_nav_menu_items', $plugin_public, 'redil_filter_menu_items', 20 );
        $this->loader->add_filter( 'pre_get_posts', $plugin_public, 'redil_filter_before_get_posts' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Redil_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
