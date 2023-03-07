<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://redil.io
 * @since      1.0.0
 *
 * @package    Redil
 * @subpackage Redil/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Redil
 * @subpackage Redil/admin
 * @author     Raskenlund <hello@raskenlund.com>
 */
class Redil_Admin {

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

    private $groups;

    public const GROUP_ALL       = 0;
    public const GROUP_ALL_TITLE = 'Everyone';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the meta fields 
     *
     * @since    1.0.0
     */
    public function on_init() {
        $args = array(
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        );

        foreach ( $this->post_types as $post_type ) {
            register_post_meta( $post_type, 'redil_group_id', array(
                'show_in_rest' => current_user_can( 'edit_posts' ),
                'single'       => true,
                'type'         => 'number',
                'default'      => self::GROUP_ALL
            ) );
        }

        $this->groups = get_posts( array(
            'numberposts' => -1,
            'post_type'   => 'redil_group'
        ) );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_enqueue_style(
            $this->plugin_name, 
            plugin_dir_url( __FILE__ ) . 'dist/css/redil-admin.css', 
            array(), 
            $this->version, 
            'all'
        );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script(
            $this->plugin_name, 
            plugin_dir_url( __FILE__ ) . 'dist/js/redil-admin.js', 
            array( 'jquery' ), 
            $this->version, 
            false
        );

    }

    public function menu_page() {

        add_menu_page(
            'Redil',
            'Redil',
            'administrator',
            $this->plugin_name,
            array($this, 'display_dashboard'),
            'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBVcGxvYWRlZCB0bzogU1ZHIFJlcG8sIHd3dy5zdmdyZXBvLmNvbSwgR2VuZXJhdG9yOiBTVkcgUmVwbyBNaXhlciBUb29scyAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIGZpbGw9IiMwMDAwMDAiIGhlaWdodD0iODAwcHgiIHdpZHRoPSI4MDBweCIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiANCgkgdmlld0JveD0iMCAwIDQ5MS4wNDMgNDkxLjA0MyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8Zz4NCgk8cGF0aCBkPSJNOTkuODY5LDQxOS42MzljLTYuNzA1LDAtMTMuMDgyLTEuMjAzLTE5LjI4MS0yLjkwNHY0MS43NTRjMCw2LjM2LDUuMTU3LDExLjUxOCwxMS41MTgsMTEuNTE4aDUzLjc0NA0KCQljNi4zNTksMCwxMS41MTgtNS4xNTcsMTEuNTE4LTExLjUxOHYtMjkuMzc3Yy0xMC40MzYtMy4wMzItMjAuMTY0LTguMTktMjguMzEzLTE1LjYxNg0KCQlDMTE5Ljg3MSw0MTcuNTIxLDEwOS45NzUsNDE5LjYzOSw5OS44NjksNDE5LjYzOXoiLz4NCgk8cGF0aCBkPSJNMjYyLjY4OSw0MzIuMTVjLTIuMjMsMC00LjQwMy0wLjMxMi02LjYwMi0wLjQ5N3YyNi44MzZjMCw2LjM2LDUuMTU3LDExLjUxOCwxMS41MTgsMTEuNTE4aDUzLjc1Mw0KCQljNi4zNiwwLDExLjUxNi01LjE1NywxMS41MTYtMTEuNTE4di02Ny4xMTNDMzE4LjI1NCw0MTYuMTc0LDI5Mi4yMjgsNDMyLjE1LDI2Mi42ODksNDMyLjE1eiIvPg0KCTxwYXRoIGQ9Ik0zNTguMzIyLDI5NS43NTdjLTUyLjUwOCwwLTg4LjkzNi03My4wMjUtOTUuMDU3LTEzMC4xMDVjLTM2LjQ0My00LjgyMS02Mi4zMDEtMjUuOTA1LTYyLjMwMS01Mi43OTENCgkJYzAtMy45MTMsMC42MjYtNy42NzQsMS42NjgtMTEuMzA3Yy03Ljk4OCw0LjUwNy0xNC42OTMsMTEuMjc2LTE5LjI2NSwxOS41ODRjLTcuNy01LjU2NS0xNy4wMDQtOC44My0yNy4wMTQtOC44Mw0KCQljLTIzLjI5LDAtNDIuNjk5LDE3LjYwNS00Ny4xNzQsNDAuOTkzYy0zNS4wMDEsNC4zMDctNjIuNzY4LDMyLjk1NS02Ny45OCw2OS44MzJDMTcuOTI1LDIyNi43NTgsMCwyNDcuODkxLDAsMjczLjY3Nw0KCQljMCwyOC4zMTEsMjEuNTU5LDUxLjI3Miw0OC4xNjIsNTEuMjcyYzAuNzcsMCwxLjQ5MS0wLjIsMi4yNTMtMC4yNGMtMS4zMjMsNC44NjktMi4yNTMsOS45MjEtMi4yNTMsMTUuMjM4DQoJCWMwLDMwLjM5NywyMy4xNDYsNTUuMDUyLDUxLjcwNyw1NS4wNTJjMTMuMzc3LDAsMjUuNDM5LTUuNTUsMzQuNjIzLTE0LjQyYzguMTU3LDE1Ljk1OSwyMy44NTIsMjYuOTMyLDQyLjEzLDI2LjkzMg0KCQljMTcuMDg1LDAsMzEuOTk0LTkuNTI5LDQwLjU0NC0yMy43OTZjMTAuNjUsMTQuMzg5LDI2Ljk4LDIzLjc5Niw0NS41MjMsMjMuNzk2YzI5LjA2NSwwLDUyLjk0MS0yMi43NzcsNTcuMjU2LTUyLjQzNw0KCQljMy41MzgsMC41NzgsNy4wNTksMS4xNDgsMTAuNzMyLDEuMTQ4YzM5LjI2LDAsNzEuMDY4LTMzLjg4Niw3MS4wNjgtNzUuNjgxYzAtMC45NDYtMC4xNTItMS44NDQtMC4xOTItMi43ODINCgkJQzM4OC45MTIsMjg4Ljg3NiwzNzQuMzgsMjk1Ljc1NywzNTguMzIyLDI5NS43NTd6Ii8+DQoJPHBhdGggZD0iTTQzNy4zLDgzLjY2OGMtNi43MzgsMC0xMy4xNTQsMC43MDgtMTkuMDk4LDEuOTQyYzIuMzQzLTQuMzEyLDMuNjczLTkuMjUzLDMuNjczLTE0LjUwNQ0KCQljMC0xNi44MDctMTMuNjI1LTMwLjQzMi0zMC40MzItMzAuNDMyYy0yLjY4NSwwLTUuMjg3LDAuMzUxLTcuNzY3LDEuMDA0Yy0yLjg4LTExLjgxNi0xMy44MzItMjAuNjQtMjcuMDU1LTIwLjY0DQoJCWMtMTIuOTA0LDAtMjMuNjU2LDguNDI3LTI2Ljg1NSwxOS44MjFjLTEuMDk0LTAuMTItMi4yMDQtMC4xODUtMy4zMy0wLjE4NWMtMTYuODA3LDAtMzAuNDMyLDEzLjYyNS0zMC40MzIsMzAuNDMyDQoJCWMwLDUuMzYzLDEuMzkzLDEwLjM5OSwzLjgyNywxNC43NzVjLTYuMzIyLTEuNDE2LTEzLjIyMi0yLjIxMS0yMC40ODUtMi4yMTFjLTI5LjY4MywwLTUzLjc0MywxMy4wNzMtNTMuNzQzLDI5LjE5Mw0KCQljMCwxNi4xMywyNC4wNjEsMjkuMTk1LDUzLjc0MywyOS4xOTVjMi44NCwwLDUuNTU5LTAuMjI1LDguMjc2LTAuNDU4Yy0wLjE4NCwyLjU4My0wLjYzMiw1LjA2OS0wLjYzMiw3LjcwOA0KCQljMCw0Ny44OTcsMzEuOTM3LDEyMS44MTIsNzEuMzMxLDEyMS44MTJjMzkuMzg5LDAsNzEuMzI1LTczLjkxNSw3MS4zMjUtMTIxLjgxMmMwLTIuNjQtMC40NDgtNS4xMjUtMC42NDEtNy43MDgNCgkJYzIuNzE5LDAuMjMzLDUuNDQ1LDAuNDU4LDguMjkzLDAuNDU4YzI5LjY4MywwLDUzLjc0My0xMy4wNjUsNTMuNzQzLTI5LjE5NUM0OTEuMDQzLDk2Ljc0MSw0NjYuOTgyLDgzLjY2OCw0MzcuMyw4My42Njh6DQoJCSBNMzMxLjk5MiwxNjEuMjM5Yy01LjMwMSwwLTkuNTkzLTQuMjktOS41OTMtOS41OTFjMC01LjMwMiw0LjI5Mi05LjU5Myw5LjU5My05LjU5M2M1LjMwMiwwLDkuNTkzLDQuMjksOS41OTMsOS41OTMNCgkJQzM0MS41ODUsMTU2Ljk0OSwzMzcuMjk0LDE2MS4yMzksMzMxLjk5MiwxNjEuMjM5eiBNMzg0LjYzOCwxNjEuMjM5Yy01LjI5NCwwLTkuNTg0LTQuMjktOS41ODQtOS41OTENCgkJYzAtNS4zMDIsNC4yOS05LjU5Myw5LjU4NC05LjU5M2M1LjMwOSwwLDkuNjAxLDQuMjksOS42MDEsOS41OTNDMzk0LjIzOCwxNTYuOTQ5LDM4OS45NDYsMTYxLjIzOSwzODQuNjM4LDE2MS4yMzl6Ii8+DQo8L2c+DQo8L3N2Zz4=',
            99
        );

    }

    public function display_dashboard() {
        require_once( 'partials/redil-admin-display.php' );
    }

    /**
     * Shows "Menu Item Audience" options for custom menu items.
     *
     * @param int      $item_id Menu item ID.
     * @param WP_Post  $item    Menu item data object.
     * @param int      $depth   Depth of menu item. Used for padding.
     * @param stdClass $args    An object of menu item arguments.
     * @param int      $id      Nav menu ID.
     */
    public function on_render_menu_item( $item_id, $item ) {

        if ( $item->type != 'custom' ) {
            // This only applies to custom menu items because items for posts
            // and pages are controlled by the post meta for the particular post.
            return;
        }

        $current_group = (int) get_post_meta( $item_id, 'redil_group_id', true );

        ?>
            <p class="description description-wide">
                <label for="edit-menu-item-group-<?php echo $item_id; ?>">
                    <?php _e('Target Group', 'redil'); ?>
                    <br />
                    <select id="menu-item-redil-group-<?php echo $item_id; ?>" name="menu-item-redil-group[<?php echo $item_id; ?>]" class="widefat edit-menu-item-group">
                        <option value="<?php echo self::GROUP_ALL; ?>" <?php selected( $group->ID == $current_group ) ?>>
                            <?php _e( self::GROUP_ALL_TITLE, 'redil' ); ?>
                        </option>
                        <?php foreach ( $this->groups as $group ): ?>
                            <option value="<?php echo esc_attr( $group->ID ); ?>" <?php selected( $group->ID == $current_group ) ?>>
                                <?php _e( $group->post_title, 'redil' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
        <?php
    }

    /**
     * @param int   $menu_id         ID of the updated menu.
     * @param int   $menu_item_db_id ID of the updated menu item.
     * @param array $args            An array of arguments used to update a menu item.
     */
    public function on_update_menu_item( $menu_id, $menu_item_db_id ) {

        $key = 'menu-item-redil-group';

        if ( isset( $_POST[ $key ][ $menu_item_db_id ] ) ) {
            $value = (int) $_POST[ $key ][ $menu_item_db_id ];

            if ( $value == self::GROUP_ALL ) {
                delete_post_meta( $menu_item_db_id, 'redil_group_id' );
            } else {
                update_post_meta( $menu_item_db_id, 'redil_group_id', $value );
            }
        }

    }

    /**
     * Removes the default level from the database.
     *
     * @param int    $mid
     * @param int    $post_id
     * @param string $key
     * @param int    $value
     * @return void
     */
    public function on_meta_saved( $mid, $post_id, $key, $value ) {

        if ( $key == 'redil_group_id' && (int) $value == self::GROUP_ALL ) {
            delete_post_meta( $post_id, $key );
        }

    }

    /**
     * Loads the JavaScript in Gutenberg.
     */
    public function on_block_editor_assets() {

        wp_enqueue_script(
            $this->plugin_name . '-Gutenberg',
            plugin_dir_url( __FILE__ ) . 'dist/js/redil-admin-gutenberg.js',
            array( 'wp-edit-post', 'wp-i18n' ),
            $this->version,
            false
        );

        wp_add_inline_script(
            $this->plugin_name . '-Gutenberg',
            'var redilData = ' . json_encode( array(
                'groups' => array_map( 'map', $this->groups ),
            ) ),
            'before'
        );
    }

    private function map($group) {

        return array (
            'key'   => $group->ID,
            'value' => $group->post_title
        );

    }
}
