<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization and
 * all module hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      3.5.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/includes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace InstructorRole\Includes;

use \InstructorRole\Includes\Instructor_Role_Loader as Instructor_Role_Loader;
use \InstructorRole\Includes\Instructor_Role_I18n as Instructor_Role_I18n;
use \InstructorRole\Includes\Instructor_Role_Activator as Instructor_Role_Activator;
use \InstructorRole\Includes\Instructor_Role_Deactivator as Instructor_Role_Deactivator;
use \InstructorRole\Includes\Instructor_Role_License as Instructor_Role_License;

use \InstructorRole\Modules\Classes\Instructor_Role_Admin as Instructor_Role_Admin;
use \InstructorRole\Modules\Classes\Instructor_Role_Comments as Instructor_Role_Comments;
use \InstructorRole\Modules\Classes\Instructor_Role_Groups as Instructor_Role_Groups;
use \InstructorRole\Modules\Classes\Instructor_Role_Multiple_Instructors as Instructor_Role_Multiple_Instructors;
use \InstructorRole\Modules\Classes\Instructor_Role_Notifications as Instructor_Role_Notifications;
use \InstructorRole\Modules\Classes\Instructor_Role_Payouts as Instructor_Role_Payouts;
use \InstructorRole\Modules\Classes\Instructor_Role_Profile as Instructor_Role_Profile;
use \InstructorRole\Modules\Classes\Instructor_Role_Reports as Instructor_Role_Reports;
use \InstructorRole\Modules\Classes\Instructor_Role_Emails as Instructor_Role_Emails;
use \InstructorRole\Modules\Classes\Instructor_Role_Woocommerce as Instructor_Role_Woocommerce;
use \InstructorRole\Modules\Classes\Instructor_Role_LearnDash_Handler as Instructor_Role_LearnDash_Handler;
use \InstructorRole\Modules\Classes\Instructor_Role_LearnDash_Menu_Handler as Instructor_Role_LearnDash_Menu_Handler;
use \InstructorRole\Modules\Classes\Instructor_Role_Commission as Instructor_Role_Commission;
use \InstructorRole\Modules\Classes\Instructor_Role_Review as Instructor_Role_Review;
use \InstructorRole\Modules\Classes\Instructor_Role_Settings as Instructor_Role_Settings;

/**
 * Instructor Role core class
 */
class Instructor_Role {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    3.5.0
	 * @access   protected
	 * @var      Instructor_Role_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    3.5.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    3.5.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    3.5.0
	 */
	public function __construct() {
		if ( defined( 'INSTRUCTOR_ROLE_PLUGIN_VERSION' ) ) {
			$this->version = INSTRUCTOR_ROLE_PLUGIN_VERSION;
		} else {
			$this->version = '3.5.0';
		}
		$this->plugin_name = 'instructor-role';

		$this->load_dependencies();
		$this->define_licenses();
		$this->handle_activation();
		$this->handle_deactivation();
		$this->set_locale();

		/*
		// Licensing activation check - To be used for restricting features wrt license status.
		if ( Instructor_Role_License::is_available_license() ) {
			// Put code that requires the license to be active here.
		}
		*/
		$this->define_admin_hooks();
		$this->define_module_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Instructor_Role_Loader. Orchestrates the hooks of the plugin.
	 * - Instructor_Role_I18n. Defines internationalization functionality.
	 * - Instructor_Role_Admin. Defines all hooks for the admin area.
	 * - Instructor_Role_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    3.5.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for handling licensing functionalities of the
		 * core plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'licensing/class-wdm-license.php';
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-license.php';

		/**
		 * The class responsible for handling activation functionalities of the
		 * core plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-activator.php';

		/**
		 * The class responsible for handling deactivation functionalities of the
		 * plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-deactivator.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-i18n.php';

		/**
		 * The file responsible for defining common functionality
		 * of the plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/instructor-role-functions.php';

		/**
		 * The file responsible for defining common static variables
		 * of the plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/instructor-role-constants.php';

		/**
		 * The file responsible for handling deprecated functionality
		 * of the plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/instructor-role-deprecated.php';

		// Load Modules.

		/**
		 * The file responsible for handling core admin functionality
		 * of the plugin
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-admin.php';

		/**
		 * The class responsible for defining all actions to control comments related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-comments.php';

		/**
		 * The class responsible for defining all actions to control group related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-groups.php';

		/**
		 * The class responsible for defining all actions to control multiple instructors related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-multiple-instructors.php';

		/**
		 * The class responsible for defining all actions to control notifications related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-notifications.php';

		/**
		 * The class responsible for defining all actions to control payouts related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-payouts.php';

		/**
		 * The class responsible for defining all actions to control profile related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-profile.php';

		/**
		 * The class responsible for defining all actions to control reports related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-reports.php';

		/**
		 * The class responsible for defining all actions to control emails related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-emails.php';

		/**
		 * The class responsible for defining all actions to control woocommerce related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-woocommerce.php';

		/**
		 * The class responsible for defining all actions to control learndash handling related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-learndash-handler.php';

		/**
		 * The class responsible for defining all actions to control learndash menu handling related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-learndash-menu-handler.php';

		/**
		 * The class responsible for defining all actions to control commission related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-commission.php';

		/**
		 * The class responsible for defining all actions to control review related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-review.php';

		/**
		 * The class responsible for defining all actions to control settings related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-settings.php';

		$this->loader = new Instructor_Role_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Instructor_Role_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    3.5.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Instructor_Role_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Define the licensing for this plugin.
	 */
	private function define_licenses() {
		$plugin_license = new Instructor_Role_License();

		$this->loader->add_action( 'plugins_loaded', $plugin_license, 'load_license' );
	}

	/**
	 * Handle plugin activation
	 */
	private function handle_activation() {
		$plugin_activator = new Instructor_Role_Activator();

		$this->loader->add_action( 'activate_' . INSTRUCTOR_ROLE_BASE, $plugin_activator, 'activate' );
		$this->loader->add_action( 'admin_init', $plugin_activator, 'admin_activate' );
		$this->loader->add_action( 'in_plugin_update_message-instructor-role/instructor-role.php', $plugin_activator, 'handle_update_notices', 10, 2 );
	}

	/**
	 * Handle plugin deactivation
	 *
	 * @since 3.5.0
	 */
	private function handle_deactivation() {
		$plugin_deactivator = new Instructor_Role_Deactivator();

		$this->loader->add_action( 'deactivate_' . INSTRUCTOR_ROLE_BASE, $plugin_deactivator, 'deactivate' );
	}

	/**
	 * Define admin side functionality
	 *
	 * @since   3.5.0
	 * @access  private
	 */
	private function define_admin_hooks() {
		$plugin_admin = Instructor_Role_Admin::get_instance();

		$this->loader->add_action( 'learndash_settings_pages_init', $plugin_admin, 'load_overview_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wdmir_set_post_types' );
		$this->loader->add_filter( 'pre_get_posts', $plugin_admin, 'wdm_set_author' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'wdmLoadScriptsAll' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'hide_update_notice_to_all_but_admin_users', 1 );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wdm_remove_dashboard_tab', 99 );
		$this->loader->add_action( 'current_screen', $plugin_admin, 'wdm_this_screen' );
		// $this->loader->add_action( 'admin_init', $plugin_admin, 'ir_remove_tab_counts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wdm_remove_admin_menus', 999 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wdm_remove_dashboard_widgets' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'show_admin_messages' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wdm_remove_copy_question_action' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'wdm_remove_template_field' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wdm_reset_author_metabox' );
		$this->loader->add_action( 'init', $plugin_admin, 'wdm_set_capabilities' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wdmirAddDashboardTabs', 999 );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'wdm_remove_top_menu', 1 );
		$this->loader->add_filter( 'contextual_help', $plugin_admin, 'irRemoveHelpTab', 999, 3 );
		$this->loader->add_filter( 'edd_add_ons_tabs', $plugin_admin, 'irRemoveEddTabs' );
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'irAdminCustomizer', 100 );
		// $this->loader->add_action( 'admin_head', $plugin_admin, 'irRemoveAdminNotices' );
		$this->loader->add_action( 'after_setup_theme', $plugin_admin, 'register_instructor_menu_setting', 100 );
		$this->loader->add_filter( 'login_redirect', $plugin_admin, 'irInstructorRedirect', 999, 3 );
		$this->loader->add_filter( 'woocommerce_login_redirect', $plugin_admin, 'irWooInstructorRedirect', 10, 2 );
		$this->loader->add_filter( 'sfwd_lms_has_access', $plugin_admin, 'irAutoEnrollInstructorCourses', 10, 3 );
		$this->loader->add_filter( 'learndash_prerequities_bypass', $plugin_admin, 'irByPassInstructorLinearAccess', 10, 4 );
		$this->loader->add_filter( 'learndash_previous_step_completed', $plugin_admin, 'irByPassInstructorPreviousCompleted', 10, 3 );
		$this->loader->add_filter( 'learndash_user_can_bypass', $plugin_admin, 'ir_bypass_instructor_user_access', 10, 4 );
		$this->loader->add_filter( 'ld_template_args_profile', $plugin_admin, 'irUpdateInstructorProfileCourses', 10 );
		$this->loader->add_filter( 'ir_filter_deny_page_access', $plugin_admin, 'irRestrictJetpackAccess', 10, 2 );
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'irEnableUserMenusForInstructors' );
		$this->loader->add_filter( 'wp_insert_post_data', $plugin_admin, 'irUpdateCourseToInstructors', 10, 2 );
		$this->loader->add_filter( 'learndash_header_data', $plugin_admin, 'updateLearnDashDataForInstructors', 999, 1 );
		$this->loader->add_filter( 'wp_nav_menu_items', $plugin_admin, 'irAddMobileMenuIcon', 10, 2 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'ir_add_instructor_dashboard_menu_items' );
		$this->loader->add_filter( 'rest_user_query', $plugin_admin, 'irFilterAuthorDropDown', 10, 2 );
		// Additional Settings.
		$this->loader->add_action( 'wdmir_settings_after_table', $plugin_admin, 'display_additional_instructor_settings' );
		$this->loader->add_filter( 'learndash_post_args', $plugin_admin, 'toggle_category_settings' );
		$this->loader->add_action( 'do_meta_boxes', $plugin_admin, 'toggle_instructor_metaboxes' );
		$this->loader->add_filter( 'fw_post_options', $plugin_admin, 'toggle_elumine_metaboxes' );
		$this->loader->add_action( 'wdmir_settings_save_after', $plugin_admin, 'save_additional_instructor_settings' );
		$this->loader->add_filter( 'learndash_settings_fields', $plugin_admin, 'update_instructor_course_pricing_options', 10, 2 );
		// Whitelabelling.
		$this->loader->add_filter( 'gettext', $plugin_admin, 'whitelabel_learndash_strings', 100, 3 );
		$this->loader->add_filter( 'gettext_with_context', $plugin_admin, 'whitelabel_learndash_context_strings', 100, 4 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'disable_ld_more_help_section' );
	}

	/**
	 * Register all of the module hooks
	 *
	 * @since    3.5.0
	 * @access   private
	 */
	private function define_module_hooks() {

		// Fetch updated list of active modules.
		$modules = $this->loader->fetch_active_modules();

		// If empty, fetch default modules list.
		if ( false === $modules ) {
			$this->loader->set_default_active_modules();
			$modules = $this->loader->fetch_active_modules();
		}

		// Define all active module action and filter hooks.
		foreach ( $modules as $module ) {
			call_user_func( array( $this, 'define_' . $module . '_module_hooks' ) );
		}
	}

	/**
	 * Register all of the hooks related to the comments module functionality
	 * of the plugin.
	 *
	 * @since    3.5.0
	 * @access   private
	 */
	private function define_comments_module_hooks() {
		$module_comments = new Instructor_Role_Comments();

		$this->loader->add_filter( 'wdmir_add_dash_tabs', $module_comments, 'enable_comments_screen_access', 10, 1 );
		$this->loader->add_filter( 'wdmir_set_post_types', $module_comments, 'allow_comments_access', 10, 1 );
		$this->loader->add_filter( 'comments_clauses', $module_comments, 'filter_instructor_comments', 10, 2 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_comments, 'filter_instructor_comment_queries', 10, 1 );
		$this->loader->add_filter( 'user_has_cap', $module_comments, 'allow_shared_course_comments_access', 10, 4 );
	}

	/**
	 * Register all of the hooks related to the groups module functionality
	 * of the plugin.
	 *
	 * @since    3.5.0
	 * @access   private
	 */
	private function define_groups_module_hooks() {
		$module_groups = Instructor_Role_Groups::get_instance();

		// Add group leader caps.
		$this->loader->add_filter( 'admin_init', $module_groups, 'add_group_capabilities' );

		// Allow access to groups.
		$this->loader->add_filter( 'wdmir_set_post_types', $module_groups, 'enable_access_to_groups_post_type' );

		// Filter group users and leaders for instructors.
		$this->loader->add_filter( 'learndash_binary_selector_args', $module_groups, 'filter_selector_group_users', 10, 2 );

		// Filter group courses to include shared courses for instructors.
		$this->loader->add_filter( 'learndash_binary_selector_args', $module_groups, 'filter_selector_group_courses', 10, 2 );

		// Include instructors in list of group leaders.
		$this->loader->add_filter( 'learndash_binary_selector_args', $module_groups, 'add_instructors_to_group_leaders_for_admin', 10, 2 );

		// Filter groups accessible to instructor on course edit page.
		$this->loader->add_filter( 'learndash_binary_selector_args', $module_groups, 'filter_instructor_groups_for_course', 10, 2 );

		// Filter groups list on course edit page.
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_groups, 'ir_filter_course_group_tab_data', 10, 1 );

		// Filter group administration screen data.
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_groups, 'ir_filter_group_admin_screen_data', 10, 1 );

		// Filter group courses on groups edit screen.
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_groups, 'ir_filter_group_courses', 10, 1 );

		// Remove secondary author dropdown on groups edit page.
		$this->loader->add_filter( 'rest_user_query', $module_groups, 'remove_secondary_author_dropdown', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the multiple instructors module functionality
	 * of the plugin.
	 *
	 * @since    3.5.0
	 * @access   private
	 */
	private function define_multiple_instructors_module_hooks() {
		$module_multiple_instructors = Instructor_Role_Multiple_Instructors::get_instance();

		$this->loader->add_action( 'add_meta_boxes', $module_multiple_instructors, 'addCourseShareMetabox' );
		$this->loader->add_action( 'admin_enqueue_scripts', $module_multiple_instructors, 'enqueueCourseSharingScripts' );
		$this->loader->add_action( 'save_post_sfwd-courses', $module_multiple_instructors, 'saveSharedInstructorMeta', 10, 2 );

		$this->loader->add_filter( 'user_has_cap', $module_multiple_instructors, 'enableMultipleInstructors', 10, 4 );
		$this->loader->add_filter( 'posts_where_request', $module_multiple_instructors, 'filterInstructorCourseList', 10, 1 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_multiple_instructors, 'allowCourseAccessToInstructors', 10, 1 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_multiple_instructors, 'allowCourseContentAccessToInstructors', 10, 1 );
		$this->loader->add_filter( 'ir_filter_quiz_access', $module_multiple_instructors, 'allowQuestionAccessToInstructors', 10, 2 );
		$this->loader->add_filter( 'ir_filter_pro_quiz_access', $module_multiple_instructors, 'allowQuestionAccessToInstructors', 10, 2 );

		$this->loader->add_action( 'save_post_sfwd-question', array( $this, 'clearLDQuizDirtyFlag' ), 100, 3 );
		// $this->loader->add_filter('ir_filter_deny_page_access', $module_multiple_instructors, 'enableAccessForSharedInstructors', 10, 2);

		// Filter users accessible to instructor on course edit page.
		$this->loader->add_filter( 'learndash_binary_selector_args', $module_multiple_instructors, 'filter_instructor_users_for_course', 100, 2 );
		$this->loader->add_filter( 'learndash_settings_fields', $module_multiple_instructors, 'filter_instructor_shared_course_contents', 100, 2 );

		$this->loader->add_action( 'wp', $module_multiple_instructors, 'remove_course_step_check_for_instructors', 9 );
	}

	/**
	 * Register all of the hooks related to the notifications module functionality
	 * of the plugin.
	 *
	 * @since    3.5.0
	 * @access   private
	 */
	private function define_notifications_module_hooks() {
		// If LearnDash Notifications plugin not activated then return.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! is_plugin_active( 'learndash-notifications/learndash-notifications.php' ) ) {
			return;
		}

		$module_notifications = new Instructor_Role_Notifications();

		$this->loader->add_filter( 'wdmir_set_post_types', $module_notifications, 'allow_notifications_access', 10, 1 );
		$this->loader->add_filter( 'learndash_submenu', $module_notifications, 'enable_notifications_screen_access', 10, 1 );
		$this->loader->add_filter( 'user_has_cap', $module_notifications, 'override_notification_privileges_for_instructors', 10, 4 );
		$this->loader->add_filter( 'learndash_notifications_recipients', $module_notifications, 'add_instructor_in_recipients_list', 10, 1 );
		$this->loader->add_filter( 'learndash_notification_recipients_emails', $module_notifications, 'add_instructor_email_in_recipients_emails', 10, 4 );
		$this->loader->add_filter( 'learndash_notification_settings', $module_notifications, 'update_instructor_notification_settings', 10, 1 );
		$this->loader->add_filter( 'wdmir_exclude_post_types', $module_notifications, 'allow_notification_post_type_access' );
	}

	/**
	 * Register all of the hooks related to the payouts module functionality
	 * of the plugin.
	 *
	 * @since    3.5.0
	 * @access   private
	 */
	private function define_payouts_module_hooks() {
		$module_payouts = new Instructor_Role_Payouts();

		$this->loader->add_action( 'admin_init', $module_payouts, 'create_payouts_table', 10, 0 );
		$this->loader->add_action( 'show_user_profile', $module_payouts, 'add_instructor_payout_fields', 10, 1 );
		$this->loader->add_action( 'edit_user_profile', $module_payouts, 'add_instructor_payout_fields', 10, 1 );
		$this->loader->add_action( 'personal_options_update', $module_payouts, 'save_instructor_payout_details', 10, 1 );
		$this->loader->add_action( 'edit_user_profile_update', $module_payouts, 'save_instructor_payout_details', 10, 1 );
		$this->loader->add_filter( 'ir_filter_instructor_setting_tabs', $module_payouts, 'add_payouts_admin_settings_tab', 10, 2 );
		$this->loader->add_action( 'instuctor_tab_checking', $module_payouts, 'add_payouts_admin_settings_tab_content', 10, 1 );
		$this->loader->add_action( 'admin_init', $module_payouts, 'save_payouts_admin_settings' );
		$this->loader->add_filter( 'ir_filter_template_path', $module_payouts, 'add_payout_commissions_template', 10, 1 );
		$this->loader->add_action( 'admin_enqueue_scripts', $module_payouts, 'enqueue_payout_scripts' );
		$this->loader->add_action( 'wp_ajax_ir_payout_transaction', $module_payouts, 'ajax_ir_payout_transaction' );
		$this->loader->add_action( 'ir_action_commission_report_end', $module_payouts, 'add_paypal_transactions_report' );
		$this->loader->add_action( 'wp_ajax_ir-get-payout-transaction-details', $module_payouts, 'ajax_fetch_payout_transaction_details' );
		$this->loader->add_action( 'admin_init', $module_payouts, 'process_scheduled_payout_transactions' );
	}

	/**
	 * Register all of the hooks related to the instructor profile module functionality
	 * of the plugin.
	 *
	 * @since    3.5.0
	 * @access   private
	 */
	private function define_profile_module_hooks() {
		$module_profile = new Instructor_Role_Profile();

		// Add new rewrite rule for instructor profile.
		$this->loader->add_action( 'init', $module_profile, 'add_profile_rewrite_rule' );
		$this->loader->add_filter( 'query_vars', $module_profile, 'add_profile_query_var' );

		// Add the instructor profile template.
		$this->loader->add_filter( 'template_include', $module_profile, 'add_instructor_profile_template', 10, 1 );

		// Enqueue necessary styles and scripts.
		$this->loader->add_action( 'wp_enqueue_scripts', $module_profile, 'enqueue_profile_assets' );

		// Enqueue profile settings stlyes and scripts.
		$this->loader->add_action( 'admin_enqueue_scripts', $module_profile, 'enqueue_profile_settings_assets' );

		// Add profile settings tab.
		$this->loader->add_filter( 'ir_filter_instructor_setting_tabs', $module_profile, 'add_profile_settings_tab', 10, 2 );

		// Add profile settings tab contents.
		$this->loader->add_action( 'instuctor_tab_checking', $module_profile, 'add_profile_settings_tab_contents', 10, 1 );

		// Save profile settings.
		$this->loader->add_action( 'admin_init', $module_profile, 'save_profile_settings' );

		// Add additional profile info.
		$this->loader->add_action( 'edit_user_profile', $module_profile, 'add_extra_instructor_profile_fields', 100 );
		$this->loader->add_action( 'show_user_profile', $module_profile, 'add_extra_instructor_profile_fields', 100 );

		// Save additional profile info.
		$this->loader->add_action( 'personal_options_update', $module_profile, 'save_extra_instructor_profile_fields', 100, 1 );
		$this->loader->add_action( 'edit_user_profile_update', $module_profile, 'save_extra_instructor_profile_fields', 100, 1 );

		// Add introduction sections.
		$this->loader->add_action( 'edit_user_profile', $module_profile, 'add_instructor_introduction_sections', 100 );
		$this->loader->add_action( 'show_user_profile', $module_profile, 'add_instructor_introduction_sections', 100 );

		// Save introduction sections.
		$this->loader->add_action( 'personal_options_update', $module_profile, 'save_instructor_introduction_sections', 100, 1 );
		$this->loader->add_action( 'edit_user_profile_update', $module_profile, 'save_instructor_introduction_sections', 100, 1 );

		$this->loader->add_filter( 'ld_course_list_shortcode_attr_values', $module_profile, 'filter_ld_course_list_for_instructors', 10, 2 );

		// Load the Instructor metabox in the WP Nav Menu Admin UI.

		// Override buddypress author links to redirect to instructor profile.
		$this->loader->add_action( 'bp_core_get_user_domain', $module_profile, 'update_bp_course_author_links', 10, 4 );

		// Override buddyboss theme author links to redirect to instructor profile.
		$this->loader->add_action( 'author_link', $module_profile, 'update_buddyboss_course_author_links', 10, 3 );

		// Override elumine theme author links to redirect to instructor profile.
		$this->loader->add_action( 'elumine_author_metadata', $module_profile, 'update_elumine_course_author_links', 10, 1 );
	}

	/**
	 * Register all of the hooks related to the instructor reports module functionality
	 * of the plugin
	 *
	 * @since   3.5.0
	 * @access  private
	 */
	private function define_reports_module_hooks() {
		$module_reports = new Instructor_Role_Reports();

		$this->loader->add_action( 'admin_menu', $module_reports, 'add_report_menu_page', 999 );
		$this->loader->add_action( 'current_screen', $module_reports, 'send_mail_from_report_page' );

		// Ajax call for showing report.
		$this->loader->add_action( 'wp_ajax_wdm_get_report_html', $module_reports, 'ajax_fetch_course_reports' );
		$this->loader->add_action( 'wp_ajax_wdm_get_user_html', $module_reports, 'ajax_fetch_course_report_page' );
		$this->loader->add_action( 'admin_footer', $module_reports, 'display_message_section' );

		// Hook for ajax where it sends mail to individual user.
		$this->loader->add_action( 'wp_ajax_wdm_send_mail_to_individual_user', $module_reports, 'send_mail_to_individual_user' );

		// For exporting users data in CSV file.
		$this->loader->add_action( 'admin_init', $module_reports, 'export_course_report_to_csv' );

		// Instructor report Email configuration updates.
		$this->loader->add_filter( 'wp_mail_from', $module_reports, 'update_sender_email_id', 1, 1 );
		$this->loader->add_filter( 'wp_mail_from_name', $module_reports, 'update_sender_name', 999, 1 );
		$this->loader->add_action( 'phpmailer_init', $module_reports, 'configure_wp_smtp_settings', 999, 1 );
	}

	/**
	 * Register all of the hooks related to the instructor emails module functionality
	 * of the plugin
	 *
	 * @since   3.5.0
	 * @access  private
	 */
	private function define_emails_module_hooks() {
		$module_emails = new Instructor_Role_Emails();

		$this->loader->add_filter( 'ir_filter_instructor_setting_tabs', $module_emails, 'ir_add_instructor_email_tab', 10, 1 );
		$this->loader->add_action( 'instuctor_tab_checking', $module_emails, 'ir_add_instructor_email_tab_content', 10, 1 );
		$this->loader->add_action( 'woocommerce_order_status_completed', $module_emails, 'ir_send_course_purchase_email_to_instructor', 10, 1 );
	}

	/**
	 * Register all of the hooks related to the instructor woocommerce module functionality
	 * of the plugin
	 *
	 * @since   3.5.0
	 * @access  private
	 */
	private function define_woocommerce_module_hooks() {
		$module_woocommerce = new Instructor_Role_Woocommerce();

		$this->loader->add_filter( 'product_type_selector', $module_woocommerce, 'restrict_product_types' );
	}

	/**
	 * Register all of the hooks related to the learndash handler module functionality
	 * of the plugin
	 *
	 * @since   3.5.0
	 * @access  private
	 */
	private function define_learndash_handler_module_hooks() {
		$module_learndash_handler = new Instructor_Role_LearnDash_Handler();

		$this->loader->add_filter( 'pre_get_posts', $module_learndash_handler, 'wdm_show_assignments_of_my_course', 11 );
		$this->loader->add_action( 'admin_init', $module_learndash_handler, 'wdm_restrict_assignment_edit' );
		// $this->loader->add_action( 'admin_init', $module_learndash_handler, 'wdm_assignment_actions', 11 );
		$this->loader->add_action( 'admin_init', $module_learndash_handler, 'wdm_remove_assignment_author' );

		// $this->loader->add_filter( 'wp', $module_learndash_handler, 'ir_allow_submission_permissions', 9 );

		$this->loader->add_filter( 'wp', $module_learndash_handler, 'ir_allow_essay_permissions', 9 );
		$this->loader->add_filter( 'wp', $module_learndash_handler, 'ir_allow_assignment_permissions', 9 );
		$this->loader->add_filter( 'learndash_current_admin_tabs_on_page', $module_learndash_handler, 'wdm_remove_tabs_certi', 10, 4 );
		$this->loader->add_filter( 'learndash_current_admin_tabs_on_page', $module_learndash_handler, 'wdm_remove_tabs_course', 10, 4 );
		$this->loader->add_filter( 'learndash_select_a_course', $module_learndash_handler, 'wdm_load_my_courses' );
		$this->loader->add_filter( 'learndash_current_admin_tabs_on_page', $module_learndash_handler, 'wdm_remove_tabs_lessons', 10, 4 );
		$this->loader->add_action( 'admin_footer', $module_learndash_handler, 'wdm_prerequisite_remove_others' );
		$this->loader->add_action( 'admin_init', $module_learndash_handler, 'wdm_restrict_quiz_edit' );
		$this->loader->add_action( 'admin_init', $module_learndash_handler, 'skip_user_filtering_for_instructors' );
		$this->loader->add_filter( 'learndash_question_quiz_post_options', $module_learndash_handler, 'filter_instructor_shared_quizzes', 100, 2 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_learndash_handler, 'allow_fetching_shared_quizzes', 10 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_learndash_handler, 'filter_learndash_queries' );

		$this->loader->add_filter( 'learndash_listing_selectors', $module_learndash_handler, 'remove_post_listing_filters', 10, 2 );
		$this->loader->add_filter( 'learndash_listing_selector_post_type_query_args', $module_learndash_handler, 'allow_learndash_post_type_filters', 100, 3 );
		$this->loader->add_filter( 'learndash_listing_selector_value', $module_learndash_handler, 'set_listing_selector_values', 100, 2 );
		$this->loader->add_filter( 'learndash_listing_table_query_vars_filter', $module_learndash_handler, 'process_learndash_post_type_filters', 100, 3 );
	}

	/**
	 * Register all of the hooks related to the learndash menu handler module functionality
	 * of the plugin
	 *
	 * @since   3.5.0
	 * @access  private
	 */
	private function define_learndash_menu_handler_module_hooks() {
		if ( class_exists( 'Instructor_Role_LearnDash_Menu_Handler' ) ) {
			$module_learndash_menu_handler = new Instructor_Role_LearnDash_Menu_Handler();

			$this->loader->add_action( 'learndash_admin_tabs_set', $module_learndash_menu_handler, 'learndash_admin_menu_early' );
			$this->loader->add_action( 'admin_init', $module_learndash_menu_handler, 'prevent_others_access' );
		}
	}

	/**
	 * Register all of the hooks related to the commission handler module functionality
	 * of the plugin
	 *
	 * @since   3.5.0
	 * @access  private
	 */
	private function define_commission_module_hooks() {
		$module_commission = new Instructor_Role_Commission();

		$this->loader->add_action( 'admin_head', $module_commission, 'wdm_instructor_table_setup' );
		$this->loader->add_action( 'wp_ajax_wdm_amount_paid_instructor', $module_commission, 'wdm_amount_paid_instructor' );
		if ( ! ir_admin_settings_check( 'instructor_commission' ) ) {
			$this->loader->add_action( 'woocommerce_order_status_completed', $module_commission, 'wdm_add_record_to_db' );
			$this->loader->add_action( 'added_post_meta', $module_commission, 'wdm_instructor_updated_postmeta', 10, 4 );
		}
		$this->loader->add_filter( 'woocommerce_prevent_admin_access', $module_commission, 'wdmAllowDashboardAccess' );
		$this->loader->add_filter( 'wdmir_set_post_types', $module_commission, 'wdmAddWoocommercePostType' );
		$this->loader->add_filter( 'wdmir_add_dash_tabs', $module_commission, 'wdmAddWoocommerceMenu' );
		$this->loader->add_filter( 'user_has_cap', $module_commission, 'allowInstructorsToRelateCourses', 10, 4 );
	}

	/**
	 * Register all of the hooks related to the review module functionality
	 * of the plugin
	 *
	 * @since   3.5.0
	 * @access  private
	 */
	private function define_review_module_hooks() {
		$module_review = new Instructor_Role_Review();
		$wp_version    = get_bloginfo( 'version' );

		$this->loader->add_action( 'save_post', $module_review, 'approve_instructor_product_updates', 999, 2 );
		/* $this->loader->add_action('admin_footer', $module_review, 'wdmir_hide_publish_product', 999); */
		$this->loader->add_action( 'publish_product', $module_review, 'wdmir_product_published_notification', 10, 2 );
		$this->loader->add_filter( 'learndash_content', $module_review, 'wdmir_approval_course_content', 100, 2 );
		$this->loader->add_action( 'save_post', $module_review, 'approve_instructor_course_updates', 11, 2 );
		$this->loader->add_action( 'admin_init', $module_review, 'wdmir_approval_meta_box' );
		$this->loader->add_action( 'post_submitbox_misc_actions', $module_review, 'wdmir_approve_field_publish' );
		if ( '5.0.0' < $wp_version ) {
			$this->loader->add_action( 'admin_enqueue_scripts', $module_review, 'wdmir_approve_field_publish_test' );
		}
		$this->loader->add_action( 'wp_ajax_approve_instructor_update_ajax', $module_review, 'approveInstructorUpdateAjaxHandler' );
		$this->loader->add_action( 'save_post', $module_review, 'wdmir_ld_approve_content', 11, 2 );
		$this->loader->add_action( 'save_post', $module_review, 'wdmir_on_course_approval_update', 11, 2 );
		$this->loader->add_filter( 'manage_sfwd-courses_posts_columns', $module_review, 'wdmir_pending_column_head', 10 );
		$this->loader->add_action( 'manage_sfwd-courses_posts_custom_column', $module_review, 'wdmir_pending_column_content', 10, 2 );
		$this->loader->add_action( 'enqueue_block_editor_assets', $module_review, 'enqueue_block_editor_review_messages' );
		$this->loader->add_action( 'admin_notices', $module_review, 'display_review_notifications' );
	}

	/**
	 * Register all of the hooks related to the settings module functionality
	 * of the plugin
	 *
	 * @since   3.5.0
	 * @access  private
	 */
	private function define_settings_module_hooks() {
		$module_settings = new Instructor_Role_Settings();

		$this->loader->add_filter( 'admin_menu', $module_settings, 'instuctor_menu', 2000 );
		$this->loader->add_action( 'admin_init', $module_settings, 'wdmir_email_settings_save' );
		$this->loader->add_action( 'admin_init', $module_settings, 'wdm_export_csv_date_filter' );
		$this->loader->add_action( 'admin_init', $module_settings, 'save_instructor_mail_template_data' );
		$this->loader->add_action( 'learndash_quiz_completed', $module_settings, 'send_email_to_instructor', 10, 1 );
		$this->loader->add_action( 'wp_ajax_wdm_update_commission', $module_settings, 'wdm_update_commission' );
		$this->loader->add_action( 'admin_init', $module_settings, 'wdm_export_commission_report' );
		$this->loader->add_action( 'admin_init', $module_settings, 'wdmir_settings_save' );
		$this->loader->add_action( 'admin_head', $module_settings, 'hide_category_links' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    3.5.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Instructor_Role_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
