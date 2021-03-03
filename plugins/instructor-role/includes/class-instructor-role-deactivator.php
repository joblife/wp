<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Instructor_Role
 * @subpackage Instructor_Role/includes
 */

namespace InstructorRole\Includes;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/includes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */
class Instructor_Role_Deactivator {

	/**
	 * Deactivation Sequence
	 *
	 * Performs necessary actions such as removing instructor capabilities from admin and instructor.
	 *
	 * @since    3.5.0
	 *
	 * @param bool $network_deactivating    Whether the plugin is deactivated for all sites in the network or just the current site.
	 *                                      Multisite only. Default false.
	 */
	public function deactivate( $network_deactivating ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() && $network_deactivating ) {
			global $wpdb;

			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
				switch_to_blog( $blog_id );

				$wdm_admin = get_role( 'administrator' );
				if ( null !== $wdm_admin ) {
					$wdm_admin->remove_cap( 'instructor_reports' );
					$wdm_admin->remove_cap( 'instructor_page' );
				}
				// Get the role object.
				$wdm_instructor = get_role( 'wdm_instructor' );
				if ( null !== $wdm_instructor ) {
					// A list of capabilities to remove from Instructors.

					foreach ( $wdm_instructor->capabilities as $key_cap => $cap_value ) {
						$cap_value = $cap_value;
						if ( 'read' != $key_cap ) {
							// Remove the capability.
							$wdm_instructor->remove_cap( $key_cap );
						}
					}
				}
				restore_current_blog();
			}
		} else {
			$wdm_admin = get_role( 'administrator' );
			if ( null !== $wdm_admin ) {
				$wdm_admin->remove_cap( 'instructor_reports' );
				$wdm_admin->remove_cap( 'instructor_page' );
			}
			// Get the role object.
			$wdm_instructor = get_role( 'wdm_instructor' );
			if ( null !== $wdm_instructor ) {
				// A list of capabilities to remove from Instructors.
				foreach ( $wdm_instructor->capabilities as $key_cap => $cap_value ) {
						$cap_value = $cap_value;
					if ( 'read' != $key_cap ) {
						// Remove the capability.
						$wdm_instructor->remove_cap( $key_cap );
					}
				}
			}
		}
	}
}
