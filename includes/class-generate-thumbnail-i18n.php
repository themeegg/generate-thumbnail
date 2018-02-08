<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://themeegg.com
 * @since      1.0.0
 *
 * @package    Generate_Thumbnail
 * @subpackage Generate_Thumbnail/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Generate_Thumbnail
 * @subpackage Generate_Thumbnail/includes
 * @author     ThemeEgg <themeeggofficial@gmail.com>
 */
class RETMBL_Generate_Thumbnail_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'generate-thumbnail',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
