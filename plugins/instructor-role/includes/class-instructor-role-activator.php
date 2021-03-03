<?php
/**
 * Fired during plugin activation
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
class Instructor_Role_Activator {

	/**
	 * Activation Sequence
	 *
	 * Performs necessary actions such as adding instructor role and capabilities to admin.
	 *
	 * @since    3.5.0
	 *
	 * @param bool $network_wide    Whether to enable the plugin for all sites in the network or just the current site.
	 *                              Multisite only. Default false.
	 */
	public function activate( $network_wide ) {
		$this->add_instructor_role();
		if ( is_multisite() && $network_wide ) {
			global $wpdb;
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				$admin_role = get_role( 'administrator' );
				if ( null !== $admin_role ) {
					$admin_role->add_cap( 'instructor_reports' );
					$admin_role->add_cap( 'instructor_page' );
				}
				restore_current_blog();
			}
		} else {
			$admin_role = get_role( 'administrator' );
			if ( null !== $admin_role ) {
				$admin_role->add_cap( 'instructor_reports' );
				$admin_role->add_cap( 'instructor_page' );
			}
		}
	}

	/**
	 * Admin Activation Sequence
	 *
	 * Check for plugin dependencies on plugin activation.
	 *
	 * @since    3.5.0
	 */
	public function admin_activate() {
		if ( is_multisite() ) {
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}

			// Check if plugin is active in network or subsite.
			if ( ! is_plugin_active_for_network( 'sfwd-lms/sfwd_lms.php' ) && ! in_array( 'sfwd-lms/sfwd_lms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
				unset( $_GET['activate'] );
				add_action( 'admin_notices', array( $this, 'handle_admin_notices' ) );
			}
		} elseif ( ! class_exists( 'SFWD_LMS' ) || ! in_array( 'sfwd-lms/sfwd_lms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			unset( $_GET['activate'] );
			add_action( 'admin_notices', array( $this, 'handle_admin_notices' ) );
		}
	}

	/**
	 * Handle admin notices
	 */
	public function handle_admin_notices() {
		if ( ! class_exists( 'SFWD_LMS' ) ) {
			?>
			<div class='error'><p>
				<?php
				echo esc_html( __( "LearnDash LMS plugin is not active. In order to make the 'Instructor Role' plugin work, you need to install and activate LearnDash LMS first.", 'wdm_instructor_role' ) );
				?>
			</p></div>

			<?php
		}
	}

	/**
	 * Handle upgrade notices if any
	 *
	 * @param array $data
	 * @param array $response
	 *
	 * @since 4.1.0
	 */
	public function handle_update_notices( $data, $response ) {
		if ( isset( $data['upgrade_notice'] ) ) {
			printf(
				'<div class="update-message">%s</div>',
				wpautop( $data['upgrade_notice'] )
			);
		}
	}

	/**
	 * Add the instructor role
	 *
	 * @since   1.0
	 */
	public function add_instructor_role() {
		/**
		 * Allow filtering instructor role capabilities
		 *
		 * Note: You will need to re-activate the plugin for the changes to take effect.
		 *
		 * @since   3.3.0
		 */
		$instructor_caps = apply_filters(
			'ir_filter_instructor_capabilities',
			array(
				'wpProQuiz_show'               => true, // true allows this capability
				'wpProQuiz_add_quiz'           => true,
				'wpProQuiz_edit_quiz'          => true, // Use false to explicitly deny
				'wpProQuiz_delete_quiz'        => true,
				'wpProQuiz_show_statistics'    => true,
				'wpProQuiz_import'             => true,
				'wpProQuiz_export'             => true,
				'read_course'                  => true,
				'publish_courses'              => true,
				'edit_courses'                 => true,
				'delete_courses'               => true,
				'edit_course'                  => true,
				'delete_course'                => true,
				'edit_published_courses'       => true,
				'delete_published_courses'     => true,
				'edit_assignment'              => true,
				'edit_assignments'             => true,
				'publish_assignments'          => true,
				'read_assignment'              => true,
				'delete_assignment'            => true,
				'edit_published_assignments'   => true,
				'delete_published_assignments' => true,
				// 'propanel_widgets'                 => true,
				'read'                         => true,
				'edit_others_assignments'      => true,
				'instructor_reports'           => true, // very important, custom for course report submenu page
				'instructor_page'              => true, // very important, for showing instructor submenu page. added in 2.4.0 v
				'manage_categories'            => true,
				'wpProQuiz_toplist_edit'       => true, // to show leaderboard of quiz
				'upload_files'                 => true, // to upload files
				'delete_essays'                => true,  // added v 2.4.0 for essay
				'delete_others_essays'         => true,
				'delete_private_essays'        => true,
				'delete_published_essays'      => true,
				'edit_essays'                  => true,
				'edit_others_essays'           => true,
				'edit_private_essays'          => true,
				'edit_published_essays'        => true,
				'publish_essays'               => true,
				'read_essays'                  => true,
				'read_private_essays'          => true,
				'edit_posts'                   => true,
				'publish_posts'                => true,
				'edit_published_posts'         => true,
				'delete_posts'                 => true,
				'delete_published_posts'       => true,
				'view_h5p_contents'            => true,
				'edit_h5p_contents'            => true,
				'unfiltered_html'              => true,
				'delete_product'               => true,
				'delete_products'              => true,
				'delete_published_products'    => true,
				'edit_product'                 => true,
				'edit_products'                => true,
				'edit_published_products'      => true,
				'publish_products'             => true,
				'read_product'                 => true,
				'assign_product_terms'         => true,
			)
		);

		add_role(
			'wdm_instructor',
			__( 'Instructor', 'wdm_instructor_role' ),
			$instructor_caps
		);
	}
}
