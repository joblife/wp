<?php
/**
 * LD Admin Menu Handler Module
 *
 * @since      3.5.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/modules/classes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Learndash_Admin_Menus_Tabs' ) ) {
	$is_plugin_active = false;

	if ( is_multisite() ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active_for_network( 'sfwd-lms/sfwd_lms.php' ) ) {
			// in the network
			$is_plugin_active = true;
		} elseif ( in_array( 'sfwd-lms/sfwd_lms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			// in the subsite
			$is_plugin_active = true;
		}
	} elseif ( in_array( 'sfwd-lms/sfwd_lms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$is_plugin_active = true;
	}

	// If LearnDash LMS is active then add the file.
	if ( $is_plugin_active ) {
		// $plugin_dir = plugin_dir_path(dirname(dirname(__FILE__)));
		$plugin_dir = WP_PLUGIN_DIR;

		// If we do not get a class, include LearnDash LMS file.
		if ( file_exists( $plugin_dir . '/sfwd-lms/includes/admin/class-learndash-admin-menus-tabs.php' ) ) {
			include_once $plugin_dir . '/sfwd-lms/includes/admin/class-learndash-admin-menus-tabs.php';
		}
	}
}

if ( ! class_exists( 'Instructor_Role_LearnDash_Menu_Handler' ) && class_exists( 'Learndash_Admin_Menus_Tabs' ) ) {
	/**
	 * Class Instructor Role LearnDash Menu Handler Module
	 */
	class Instructor_Role_LearnDash_Menu_Handler extends \Learndash_Admin_Menus_Tabs {


		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 3.3.0
		 */
		protected static $instance = null;

		/**
		 * Plugin Slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 3.3.0
		 */
		protected $plugin_slug = '';

		public function __construct() {
			 $this->plugin_slug = INSTRUCTOR_ROLE_TXT_DOMAIN;
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since   3.5.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Removes admin menu where instructor should not have access to.
		 *
		 * @return void
		 */
		public function learndash_admin_menu_early() {

			if ( wdm_is_instructor() ) {

				$parent = parent::get_instance();

				$tabs_to_remove = apply_filters(
					'wdmir_admin_tabs_to_remove',
					array(
						'sfwd-quiz_page_ldAdvQuiz',
						'sfwd-quiz_page_ldAdvQuiz_globalSettings',
					)
				);

				foreach ( $parent->admin_tab_sets['edit.php?post_type=sfwd-quiz'] as $key => $value ) {
					if ( in_array( $value['id'], $tabs_to_remove ) ) {
						unset( $parent->admin_tab_sets['edit.php?post_type=sfwd-quiz'][ $key ] );
					}
				}
			}
		}

		/**
		 * Prevent access to admin pages. We have removed some pages from the "admin_tab_sets" array of "Learndash_Admin_Menus_Tabs" class,
		 * but instructor still can access the tabs. This function restricts access.
		 *
		 * @return void
		 */
		public function prevent_others_access() {
			// If LearnDash- WP Pro Quiz admin page
			if ( wdm_is_instructor() && isset( $_GET['page'] ) && 'ldAdvQuiz' == $_GET['page'] ) {

				global $wpdb;

				if ( isset( $_GET['quiz_id'] ) || isset( $_GET['id'] ) ) {

					$pro_quiz_id = ( isset( $_GET['quiz_id'] ) && ! empty( $_GET['quiz_id'] ) ) ? $_GET['quiz_id'] : '';

					// If we do not get quiz_id then check for ID. It's pro quiz id.
					$pro_quiz_id = ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) ? $_GET['id'] : $pro_quiz_id;

					$post_id = $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'quiz_pro_id' AND  meta_value = '" . $pro_quiz_id . "' LIMIT 1" );

					if ( $post_id ) {
						$post_author_id = get_post_field( 'post_author', $post_id );

						$should_die = apply_filters( 'wdmir_die_learndash_admin_tabs', true, $post_id );

						$pro_quiz_access = true;
						if ( ( get_current_user_id() != $post_author_id ) && $should_die ) {
							$pro_quiz_access = false;
						}
						$pro_quiz_access = apply_filters( 'ir_filter_pro_quiz_access', $pro_quiz_access, $post_id );
						if ( ! $pro_quiz_access ) {
							wp_die( __( 'You do not have sufficient permissions to access this page', 'wdm_instructor_role' ) );
						}
					}
				}
			}

		}
	}
}
