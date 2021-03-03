<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Instructor_Role
 * @subpackage Instructor_Role/includes
 */

namespace InstructorRole\Includes;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/includes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */
class Instructor_Role_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    3.5.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wdm_instructor_role',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
