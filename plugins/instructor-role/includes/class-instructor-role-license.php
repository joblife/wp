<?php
/**
 * Handling plugin licenses
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Instructor_Role
 * @subpackage Instructor_Role/includes
 */

namespace InstructorRole\Includes;

/**
 * LD Group Registration License
 */
class Instructor_Role_License {
	/**
	 * Load license
	 */
	public function load_license() {
		global $instructorRolePluginData;

		if ( empty( $instructorRolePluginData ) ) {
			$instructorRolePluginData = include_once plugin_dir_path( dirname( __FILE__ ) ) . 'license.config.php';
			new \Licensing\WdmLicense( $instructorRolePluginData );
		}
	}

	/**
	 * Check if license available
	 *
	 * @return boolean    True if active, false otherwise.
	 */
	public static function is_available_license() {
		global $instructorRolePluginData;

		if ( empty( $instructorRolePluginData ) ) {
			$instructorRolePluginData = include_once plugin_dir_path( dirname( __FILE__ ) ) . 'license.config.php';
			new \Licensing\WdmLicense( $instructorRolePluginData );
		}

		$get_data_from_db = \Licensing\WdmLicense::checkLicenseAvailiblity( $instructorRolePluginData['pluginSlug'], false );

		if ( 'available' == $get_data_from_db ) {
			return true;
		}

		return false;
	}
}
