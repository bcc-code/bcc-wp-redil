<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/bcc-code/redil
 * @since             1.0.0
 * @package           Redil
 *
 * @wordpress-plugin
 * Plugin Name:       Redil
 * Plugin URI:        https://github.com/bcc-code/redil
 * Description:       Redil is a plug-in for conditional access to resources
 * Version:           1.0.0
 * Author:            Karel Boek
 * Author URI:        https://karelboek.com
 * License:           Apache-2.0
 * License URI:       http://www.apache.org/licenses/LICENSE-2.0
 * Text Domain:       redil
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'REDIL_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-redil-activator.php
 */
function activate_redil() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-redil-activator.php';
	Redil_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-redil-deactivator.php
 */
function deactivate_redil() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-redil-deactivator.php';
	Redil_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_redil' );
register_deactivation_hook( __FILE__, 'deactivate_redil' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-redil.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_redil() {

	$plugin = new Redil();
	$plugin->run();

}
run_redil();
