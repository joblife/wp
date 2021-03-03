<?php
/**
 * Plugin Name:         LearnDash Ratings, Reviews, and Feedback
 * Plugin URI:          https://wisdmlabs.com/
 * Description:         This plugin provides a feature to rate, review and feedback on course.
 * Version:             2.0.1
 * Author:              WisdmLabs
 * Author URI:          https://wisdmlabs.com
 * Text Domain:         wdm_ld_course_review
 * Domain Path:         /languages
 *
 * @package RatingsReviewsFeedback
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'RRF_PLUGIN_FILE' ) ) {
	define( 'RRF_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'RRF_PLUGIN_PATH' ) ) {
	define( 'RRF_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'RRF_PLUGIN_URL' ) ) {
	define( 'RRF_PLUGIN_URL', plugins_url( '', __FILE__ ) );
}

add_action( 'plugins_loaded', 'rrf_load_textdomain' );
/**
 * Load plugin textdomain.
 */
function rrf_load_textdomain() {
	load_plugin_textdomain( 'wdm_ld_course_review', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// Loading all constants.
require_once 'includes/constants.php';
// Loading all the helper(pluggable) functions.
require_once 'includes/functions.php';
// Loading all deprecated functions.
require_once 'includes/deprecated-functions.php';

global $rrf_plugin_data;
/**
 * This has to be done on plugins_loaded or after that as it will need dependent plugin versions.
 */
add_action( 'plugins_loaded', 'rrf_load_licensing_module' );

if ( ! function_exists( 'rrf_load_licensing_module' ) ) {
	/**
	 * Change file paths if not accessed from main file
	 */
	function rrf_load_licensing_module() {
		global $rrf_plugin_data;
		$rrf_plugin_data = include_once( 'license.config.php' );
		require_once 'licensing/class-wdm-license.php';
		new \Licensing\WdmLicense( $rrf_plugin_data );
		$rrf_version = get_option( 'rrf_plugin_version', false );
		if ( false === $rrf_version ) {
			update_option( 'rrf_plugin_version', WDM_LD_COURSE_VERSION );
		}
	}
}

add_action( 'plugins_loaded', 'rrf_load_plugin_files', 1 );

if ( ! function_exists( 'rrf_load_plugin_files' ) ) {
	/**
	 * Load all the plugin files.
	 */
	function rrf_load_plugin_files() {
		require_once RRF_PLUGIN_PATH . 'admin/admin.php';        // loading all admin e.g(dashboard) related functionality.
		require_once RRF_PLUGIN_PATH . 'public/public.php';      // loading all front-end related functionality.
	}
}
if ( ! function_exists( 'wdm_crr_check_ld_activation' ) ) {
	/**
	 * To check whether LearnDash is activated or not. If not then notify the admin.
	 */
	function wdm_crr_check_ld_activation() {
		// if multisite.
		if ( is_multisite() ) {
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}

			if ( in_array( 'sfwd-lms/sfwd_lms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				return;
			}

			if ( ! is_plugin_active_for_network( 'sfwd-lms/sfwd_lms.php' ) ) {
				echo WDM_LD_COURSE_ACTIVATION_MSG;// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		} elseif ( ! in_array( 'sfwd-lms/sfwd_lms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			echo WDM_LD_COURSE_ACTIVATION_MSG;// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

// to notify user to activate LearnDash, if not activated.
add_action( 'admin_notices', 'wdm_crr_check_ld_activation' );


register_activation_hook( __FILE__, 'wdm_course_review_plugin_activate' );
if ( ! function_exists( 'wdm_course_review_plugin_activate' ) ) {
	/**
	 * Adding capabilites to admin and instructor role.
	 *
	 * @param bool $network_wide [is network wide].
	 */
	function wdm_course_review_plugin_activate( $network_wide ) {
		global $wp_roles;
		$roles = $wp_roles->roles;
		$all_role_ids = array();

		foreach ( $roles as $role_id => $role ) {
			$all_role_ids[] = $role_id;
			unset( $role );
		}

		$admin_capabilities = array(
			// for rating and review.
			'read_private_wdm_course_reviews',
			'publish_wdm_course_reviews',
			'edit_wdm_course_reviews',
			'edit_published_wdm_course_reviews',
			'edit_private_wdm_course_reviews',
			'edit_others_wdm_course_reviews',
			'delete_wdm_course_reviews',
			'delete_published_wdm_course_reviews',
			'delete_private_wdm_course_reviews',
			'delete_others_wdm_course_reviews',
			// for feedback.
			'read_private_wdm_course_feedbacks',
			'publish_wdm_course_feedbacks',
			'edit_wdm_course_feedbacks',
			'edit_published_wdm_course_feedbacks',
			'edit_private_wdm_course_feedbacks',
			'edit_others_wdm_course_feedbacks',
			'delete_wdm_course_feedbacks',
			'delete_published_wdm_course_feedbacks',
			'delete_private_wdm_course_feedbacks',
			'delete_others_wdm_course_feedbacks',
		);
		$limited_capabilites = array(
			'read_private_wdm_course_reviews',
			'edit_wdm_course_reviews',
			// 'edit_published_wdm_course_reviews' ,
			'edit_private_wdm_course_reviews',
		);
		$full_access_users = array(
			'administrator',
			// 'wdm_instructor',
		);

		$limited_access_users = array_diff( $all_role_ids, $full_access_users );

		if ( is_multisite() && $network_wide ) {
			global $wpdb;
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				rrf_add_capabilities( $full_access_users, $admin_capabilities );
				/*rrf_add_capabilities($limited_access_users, $limited_capabilites);*/
				restore_current_blog();
			}
		} else {
			rrf_add_capabilities( $full_access_users, $admin_capabilities );
			/*rrf_add_capabilities($limited_access_users, $limited_capabilites);*/
		}
		unset( $limited_capabilites );
		unset( $limited_access_users );
	}
}
