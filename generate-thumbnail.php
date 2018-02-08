<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://themeegg.com
 * @since             1.0.0
 * @package           Generate_Thumbnail
 *
 * @wordpress-plugin
 * Plugin Name:       Generate Thumbnail
 * Plugin URI:        http://wordpress.org/plugins/generate-thumbnail
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            ThemeEgg
 * Author URI:        http://themeegg.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       generate-thumbnail
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
define( 'TEG_RETMBL_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-generate-thumbnail-activator.php
 */
function retmbl_activate_generate_thumbnail() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-generate-thumbnail-activator.php';
	RETMBL_Generate_Thumbnail_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-generate-thumbnail-deactivator.php
 */
function retmbl_deactivate_generate_thumbnail() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-generate-thumbnail-deactivator.php';
	RETMBL_Generate_Thumbnail_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'retmbl_activate_generate_thumbnail' );
register_deactivation_hook( __FILE__, 'retmbl_deactivate_generate_thumbnail' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-generate-thumbnail.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function retmbl_run_generate_thumbnail() {

	$plugin = new RETMBL_Generate_Thumbnail();
	$plugin->run();

}
retmbl_run_generate_thumbnail();
