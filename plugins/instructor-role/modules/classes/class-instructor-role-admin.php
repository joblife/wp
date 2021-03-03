<?php
/**
 * Core Admin Module
 *
 * @since      3.5.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/modules/classes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Overview as Instructor_Role_Overview;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Admin' ) ) {
	/**
	 * Class Instructor Role Admin Module
	 */
	class Instructor_Role_Admin {


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
		 * Load instructor overview page
		 */
		public function load_overview_page() {
			if ( ! class_exists( 'Instructor_Role_Overview' ) ) {
				/**
				 * The class responsible for defining all actions to control overview related functionalities
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-instructor-role-overview.php';
			}

			if ( wdm_is_instructor() ) {
				Instructor_Role_Overview::add_page_instance();
			}
		}

		/**
		 * Added in v1.3
		 * Added filter for post types.
		 */
		public function wdmir_set_post_types() {
			global $wdm_ar_post_types;
			$wdm_ar_post_types = apply_filters( 'wdmir_set_post_types', $wdm_ar_post_types );
		}

		public function wdm_set_author( $query ) {
			// Check if admin
			if ( $query->is_admin ) {
				$wdm_user_id = get_current_user_id();

				if ( wdm_is_instructor( $wdm_user_id ) ) {
					$wdmir_exclude_posts = array(
						'sfwd-assignment',
						'achievement-type',
						'badges',
						'submission',
						'nomination',
						'badgeos-log-entry',
						'sfwd-essays',
						'acf-field-group',
						'acf-field',
						'points-type'
						// 'groups',
					); // added sfwd-essays in v2.4.0

					$wdmir_exclude_posts = apply_filters( 'wdmir_exclude_post_types', $wdmir_exclude_posts );

					$restrict_user = true;

					if ( array_key_exists( 'post_type', $query->query ) && is_array( $query->query['post_type'] ) ) {
						foreach ( $query->query['post_type'] as $post_type ) {
							if ( ! post_type_exists( $post_type ) ) {
								$restrict_user = false;
								break;
							}
							if ( in_array( $post_type, $wdmir_exclude_posts ) ) {
								$restrict_user = false;
							}
						}
					} elseif ( array_key_exists( 'post_type', $query->query ) && in_array( $query->query['post_type'], $wdmir_exclude_posts ) ) {
						$restrict_user = false;
					}

					if ( $restrict_user ) {
						$query->query['author__in']      = array( $wdm_user_id );
						$query->query_vars['author__in'] = array( $wdm_user_id );

						$query = apply_filters( 'ir_filter_instructor_query', $query );
					}
				}
			}

			return $query;
		}

		/**
		 * Function to enqueue scripts.
		 */
		public function wdmLoadScriptsAll() {
			if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'instuctor' || $_GET['page'] == 'instructor_lms_reports' ) ) {
				wp_enqueue_script( 'wdm_popup.js', plugin_dir_url( __DIR__ ) . 'js/wdm_popup.js', array( 'jquery' ), '0.0.1' );
				wp_enqueue_script( 'wdmHighcharts', plugin_dir_url( __DIR__ ) . 'js/highchart.js', array( 'jquery' ), '0.0.1' );
				// Data table for users who attempted course
				wp_enqueue_script( 'wdmDtGootable', plugin_dir_url( __DIR__ ) . 'js/footable.js', array( 'jquery' ), '0.0.1' );
				wp_enqueue_script( 'wdmDtFilter', plugin_dir_url( __DIR__ ) . 'js/footable.filter.js', array( 'jquery' ), '0.0.1' );
				wp_enqueue_script( 'wdmDtSort', plugin_dir_url( __DIR__ ) . 'js/footable.sort.js', array( 'jquery' ), '0.0.1' );

				// Custom css
				wp_enqueue_style( 'wdmCss', plugin_dir_url( __DIR__ ) . 'css/style.css' );
				// For data table
				wp_enqueue_style( 'wdmDtCssFootable', plugin_dir_url( __DIR__ ) . 'css/footable.core.css' );
				wp_enqueue_style( 'wdmDtCssFooStand', plugin_dir_url( __DIR__ ) . 'css/footable.standalone.css' );
				// For popup email form
				wp_enqueue_style( 'wdmPopEmailCss', plugin_dir_url( __DIR__ ) . 'css/wdm_popup_ins_mail.css' /*, array('editor-style.css')*/ );
			}

			// Instructor admin scripts and styles
			if ( wdm_is_instructor() ) {
				wp_enqueue_style( 'ir-instructor-styles', plugins_url( 'css/ir-instructor.css', __DIR__ ) );
				wp_enqueue_script( 'ir-instructor-scripts', plugins_url( 'js/ir-instructor.js', __DIR__ ), array( 'jquery' ) );
			}
		}

		/**
		 * Function to hide update notification from users those who can't update core.
		 */
		public function hide_update_notice_to_all_but_admin_users() {
			if ( ! current_user_can( 'update_core' ) ) {
				remove_action( 'admin_notices', 'update_nag', 3 );
			}
		}

		/*
		* To remove "dashboard" tab from admin menu
		*/
		public function wdm_remove_dashboard_tab() {
			if ( wdm_is_instructor() ) {
				global $menu;

				// to remove Contact Form 7 tab from Dashboard
				$arr_dash_tabs = apply_filters( 'wdmir_remove_dash_tabs', array( 'contact form 7' ) );

				foreach ( $menu as $key => $value ) {
					// To remove tabs from dashboard
					if ( isset( $value[3] ) && in_array( strtolower( $value[3] ), $arr_dash_tabs ) ) {
						unset( $menu[ $key ] );
					}
				}

				remove_menu_page( 'index.php' ); // dashboard
			}
		}

		/**
		 * to add restrictions on various pages to the instructor. As "edit_posts" is assigned to instructor, so to restrict creation of other posts other than LD, this function is used.
		 * It validates using current screen base name and $_POST data.
		 */
		function wdm_this_screen() {
			$currentScreen = get_current_screen();

			global $post, $wdm_ar_post_types;

			$is_ld = false;

			$arr_ld_post_types = array();
			$arr_ld_post_types = $wdm_ar_post_types;

			// array_push($arr_ld_post_types, 'sfwd-assignment'); // access for assignments.
			// array_push($arr_ld_post_types, 'sfwd-essays');
			if ( ( ! empty( $post ) || ! empty( $_POST['post_type'] ) ) &&
				( in_array( $post->post_type, $arr_ld_post_types ) || in_array( $_POST['post_type'], $arr_ld_post_types ) ) ) {
				$is_ld = true;
			}
			if ( wdm_is_instructor() ) {
				// if ($currentScreen->base == 'dashboard') {
				// header('Location: '.site_url().'/wp-admin/edit.php?post_type=sfwd-courses');
				// die();
				// }
				$this->wdmCheckScreenOfInstructor( $currentScreen, $_GET['page'], $_GET['post_type'], $is_ld, $arr_ld_post_types );
				// reduceCyclomaticOfWdmThisScreen($currentScreen->base, $_GET['page'], $_GET['post_type'], $is_ld, $arr_ld_post_types);
			}
		}

		/**
		 * Function to check a variable is set or not.
		 */
		public function checkIfSet( $page ) {
			if ( isset( $page ) ) {
				return $page;
			}

			return '';
		}

		/**
		 * This function checks the current screen page of instructor to permission checking.
		 */
		public function wdmCheckScreenOfInstructor( $currentScreen, $get_page, $get_post_type, $is_ld, $arr_ld_post_types ) {
			$current_scr_base = $currentScreen->base;

			$skipChecking = apply_filters( 'wdm_ir_check_current_page', false, $current_scr_base, $get_page, $get_post_type, $is_ld, $arr_ld_post_types );

			if ( $skipChecking ) {
				return;
			}

			$ld_category_page_ids = apply_filters( 'wdmir_ld_category_page_ids', array( 'edit-ld_course_category', 'edit-ld_course_tag' ) );

			if ( in_array( $currentScreen->id, $ld_category_page_ids ) ) {
				return;
			}

			global $post;

			if ( $post != null ) {
				if ( in_array( $post->post_type, $arr_ld_post_types ) ) {
					return;
				}
			} else {
				$post_type = get_post_type( @$_GET['post'] );
				if ( ! empty( $post_type ) && in_array( $post_type, $arr_ld_post_types ) ) {
					return;
				}
			}
			// Hook to restrict more pages. v3.0.0.
			do_action( 'wdm_ir_restrict_page', $current_scr_base, $get_page, $get_post_type, $is_ld, $arr_ld_post_types );
			$param_page = $this->checkIfSet( $get_page );

			$page_data = array(
				'current_scr_base'  => $current_scr_base,
				'get_post_type'     => $get_post_type,
				'is_ld'             => $is_ld,
				'arr_ld_post_types' => $arr_ld_post_types,
				'param_page'        => $param_page,
			);

			if ( apply_filters( 'ir_filter_deny_page_access', $this->irDenyPageAccess( $page_data ), $page_data ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page...', 'wdm_instructor_role' ) );
			}
		}

		/**
		 * Function to redirect current screen base.
		 */
		public function redirectIfCurrentScrBase( $current_scr_base ) {
			if ( $current_scr_base == 'dashboard' ) {
				// echo site_url();
				header( 'Location: ' . site_url() . '/wp-admin/edit.php?post_type=sfwd-courses' );
			}
		}

		/**
		 * Remove tab counts
		 */
		public function ir_remove_tab_counts() {
			global $wdm_ar_post_types;
			foreach ( $wdm_ar_post_types as $value ) {
				add_filter( 'views_edit-' . $value, array( $this, 'wdm_remove_counts' ) );
			}
		}

		/*
		* To remove posts,media,tools,etc. tabs from admin menu
		*/
		public function wdm_remove_admin_menus() {
			// Check that the built-in WordPress function remove_menu_page() exists in the current installation
			if ( function_exists( 'remove_menu_page' ) ) {
				if ( wdm_is_instructor() ) {
					$remove_menu_page = array(
						'posts'  => 'edit.php',
						'tools'  => 'tools.php',
						'media'  => 'upload.php',
						'themes' => 'themes.php',
					);
					$remove_menu_page = apply_filters( 'wdm_ir_remove_menu_page', $remove_menu_page ); // added v2.4.0
					foreach ( $remove_menu_page as $remove_page ) {
						remove_menu_page( $remove_page );
					}
				}
			}
		}

		/**
		 * To remove dashboard widgets from dashboard page in case header redirect fails.
		 */
		public function wdm_remove_dashboard_widgets() {
			if ( wdm_is_instructor() ) {
				remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' ); // right now
				remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' ); // recent comments
				remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' ); // incoming links
				remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' ); // plugins

				remove_meta_box( 'dashboard_quick_press', 'dashboard', 'normal' ); // quick draft
				remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'normal' ); // recent drafts
				remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' ); // WordPress blog
				remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' ); // other WordPress news
				remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
			}
		}

		/**
		 * To show message on dashboard page that no data to display to user  in case header redirect fails.
		 */
		public function show_admin_messages() {
			 $currentScreen = get_current_screen();

			if ( wdm_is_instructor() ) {
				if ( $currentScreen->base == 'dashboard' ) {
					echo '<div class="error"><p>' . __( 'No data to display', 'wdm_instructor_role' ) . '!</p></div>';
				}
			}
		}

		/**
		 * To remove default copy question ajax action, and load custom ajax action.
		 */
		public function wdm_remove_copy_question_action() {
			if ( wdm_is_instructor() ) {
				remove_all_actions( 'wp_ajax_wp_pro_quiz_load_question' );
				add_action( 'wp_ajax_wp_pro_quiz_load_question', array( $this, 'wdm_quiz_load_question_for_copy' ) );
			}
		}

		/**
		 * This function takes "quiz_id" as an argument and returns all quizzes with questions of same user only. It takes quiz_id argument to exclude current quiz questions.
		 *  Here most of the LD code used, with some changes in it.
		 */
		public function wdm_quiz_load_question_for_copy() {
			 $quizId = checkIfSet( $_GET['quiz_id'] );

			$wdm_current_user = get_current_user_id();

			if ( ! current_user_can( 'wpProQuiz_edit_quiz' ) ) {
				echo json_encode( array() );
				exit;
			}

			$questionMapper = new WpProQuiz_Model_QuestionMapper();
			$data           = array();

			global $wpdb;

			$res = array();

			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wp_pro_quiz_master ORDER BY id ASC", ARRAY_A );

			foreach ( $results as $row ) {
				if ( $row['result_grade_enabled'] ) {
					$row['result_text'] = unserialize( $row['result_text'] );
				}
				$res[] = new WpProQuiz_Model_Quiz( $row );
			}

			$quiz = $res;

			foreach ( $quiz as $qz ) {
				if ( $qz->getId() == $quizId ) {
					continue;
				}

				$quiz_post_id = learndash_get_quiz_id_by_pro_quiz_id( $qz->getId() );

				// $wdm_current_user
				$post_author_id = get_post_field( 'post_author', $quiz_post_id );

				if ( $wdm_current_user != $post_author_id ) {
					continue;
				}

				$question      = $questionMapper->fetchAll( $qz->getId() );
				$questionArray = array();

				foreach ( $question as $qu ) {
					$questionArray[] = array(
						'name' => $qu->getTitle(),
						'id'   => $qu->getId(),
					);
				}

				$data[] = array(
					'name'     => $qz->getName(),
					'id'       => $qz->getId(),
					'question' => $questionArray,
				);
			}

			echo json_encode( $data );

			exit;
		}

		/**
		 * to remove template field from edit question and edit quiz pages.
		 */
		public function wdm_remove_template_field() {
			if ( wdm_is_instructor() ) {
				echo '<style>
				input[name=templateName], select[name=templateSaveList], #wpProQuiz_saveTemplate {
					display:none !important;
				}
				select[name=templateLoadId], input[name=templateLoad] {
					display:none !important;
				}
				</style>';
				echo '<script>
				jQuery( document ).ready( function() {
					jQuery( "#wpProQuiz_saveTemplate" ).closest( "div" ).remove();
					jQuery("select[name=templateLoadId]").closest( "div" ).remove();
				});
				</script>';
			}
		}

		/**
		 *  To remove default author meta box and add custom author meta box, to list users having role "authors" or "Instructor" in LD custom post types.
		 */
		public function wdm_reset_author_metabox() {
			// if (is_super_admin()) {
			// Changed condition because, Instructors were not listing in author list when logged in as an subsite admin in multisite.
			if ( current_user_can( 'administrator' ) ) {
				global $wdm_ar_post_types;

				foreach ( $wdm_ar_post_types as $value ) {
					remove_meta_box( 'authordiv', $value, 'normal' );
					add_meta_box( 'authordiv', __( 'Author', 'wdm_instructor_role' ), 'wdm_post_author_meta_box', $value );
				}
			}
		}

		/**
		 * Remove/Add capabilities from Instructors.
		 * Checks if plugin's license is deactivated or not, if deactivated then removes all caps of 'wdm_instructor' role
		 * excluding 'read' cap, when next time, activate license then adds all caps again.
		 */
		public function wdm_set_capabilities() {
			// Get the role object.
			$wdm_instructor = get_role( 'wdm_instructor' );

			if ( null !== $wdm_instructor ) {

				// A list of capabilities to remove from Instructors.
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

				// if 'read_course' cap is not present then add all caps
				if ( ! isset( $wdm_instructor->capabilities['read_course'] ) || ! isset( $wdm_instructor->capabilities['delete_essays'] ) ) {
					foreach ( $instructor_caps as $key_cap => $val_cap ) {
						if ( 'read' != $key_cap ) {
							// add the capability.
							$wdm_instructor->add_cap( $key_cap );
							unset( $val_cap );
						}
					}
				}

				$wdmir_admin_settings = get_option( '_wdmir_admin_settings', array() );

				// if (isset($wdm_instructor->capabilities['instructor_page'])) {
				// if ( isset( $wdmir_admin_settings['instructor_commission'] ) && '1' == $wdmir_admin_settings['instructor_commission'] ) {
				// if ( ! class_exists( 'Instructor_Role_Commission' ) ) {
				// **
				// * The class responsible for defining all actions to control commission related functionalities
				// */
				// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-instructor-role-commission.php';
				// }
				// $commission_instance = Instructor_Role_Commission::get_instance();
				// remove_action( 'woocommerce_order_status_completed', array( $commission_instance, 'wdm_add_record_to_db' ) );
				// remove_action( 'added_post_meta', array( $commission_instance, 'wdm_instructor_updated_postmeta' ), 10 );
				// }

				$wdm_admin = get_role( 'administrator' );
				if ( ! isset( $wdm_admin->capabilities['instructor_page'] ) && null !== $wdm_admin ) {
					$wdm_admin->add_cap( 'instructor_page' );
				}
			}
		}

		/**
		 * @description: To edit "dashboard" tabs in admin menu
		 */
		public function wdmirAddDashboardTabs() {
			if ( wdm_is_instructor() ) {
				global $menu;

				// Default allowed tabs.
				$allowed_tabs = array(
					// 'products',
					'courses',
					'lessons',
					'quizzes',
					'assignments',
					'topics',
					'certificates',
					'profile',
					'learndash-lms',  // This menu has been added in LearnDash v2.4.0.
					'h5p',            // @since v3.2.1
					'edit.php?post_type=students_voice',
					// 'woocommerce',
				);
				// to remove Contact Form 7 tab from Dashboard.
				$allowed_tabs = apply_filters( 'wdmir_add_dash_tabs', $allowed_tabs );

				foreach ( $menu as $key => $value ) {
					// If not from an array, remove from the menu.
					if ( isset( $value[2] ) && in_array( strtolower( $value[2] ), $allowed_tabs ) ) {
						// Do nothing
					} else {
						unset( $menu[ $key ] );
					}
				}
				remove_menu_page( 'index.php' ); // dashboard
			}
		}

		/**
		 * wdm_remove_top_menu to remove the top menu of new content menu
		 */
		public function wdm_remove_top_menu() {
			if ( wdm_is_instructor() ) {
				// for removing comment and new content menu which appears at the top of the dashboard
				// remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60); //added v2.4.0
				remove_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 ); // added v2.4.0
			}
		}

		/**
		 * Remove help tab
		 *
		 * @since 3.1.0
		 */
		public function irRemoveHelpTab( $help, $screen_id, $screen ) {
			if ( wdm_is_instructor() ) {
				$screen->remove_help_tabs();
			}
			return $help;
		}

		/**
		 * Remove Apps and Integrations tab in EDD
		 *
		 * @since 3.1.0
		 */
		public function irRemoveEddTabs( $tabs ) {
			if ( wdm_is_instructor() ) {
				unset( $tabs['integrations'] );
			}
			return $tabs;
		}

		/**
		 * Admin Customizer Settings
		 *
		 * @since 3.1.0
		 */
		public function irEnableAdminCustomizerSettings() {
			require_once INSTRUCTOR_ROLE_ABSPATH . '/libs/admin_theme/everest-admin-theme.php';
		}

		/**
		 * Admin Customizer
		 *
		 * @since 3.1.0
		 */
		public function irAdminCustomizer() {
			$wdmir_admin_settings = get_option( '_wdmir_admin_settings', false );

			// Get required wp functions.
			if ( ! function_exists( 'wp_get_current_user' ) ) {
				require_once ABSPATH . WPINC . '/pluggable.php';
			}

			// Return if customizer disabled or if user is admin
			// if (empty($wdmir_admin_settings) || current_user_can('administrator')) {

			if ( empty( $wdmir_admin_settings ) ) {
				return;
			}

			// If enabled, then load admin customizer module
			if ( array_key_exists( 'admin_customizer_check', $wdmir_admin_settings ) && 1 == $wdmir_admin_settings['admin_customizer_check'] ) {
				$this->irEnableAdminCustomizerSettings();
			}
			// }
		}

		/**
		 * Remove admin notices for instructors.
		 *
		 * @since 3.1.0
		 */
		public function irRemoveAdminNotices() {
			if ( wdm_is_instructor() ) {
				remove_all_actions( 'admin_notices' );
			}
		}

		/**
		 * Register Instructor menu settings checkbox
		 *
		 * @since 3.3.0
		 */
		public function register_instructor_menu_setting() {
			register_nav_menu(
				'ir-instructor-menu',
				__( 'Instructor Dashboard Menu - Enabled if you have an template selected from the admin customizer', 'wdm_instructor_role' )
			);
		}

		/**
		 * Redirect instructor to respective page
		 *
		 * @param string $redirect_to   URL to redirect to
		 * @param string $requset       URL the user is coming from
		 * @param object $user          WP_User object of the user
		 *
		 * @return string $redirect_to   Updated URL, if user is instructor then instructor overiew page URL, else unchanged.
		 *
		 * @since 3.1.0
		 */
		function irInstructorRedirect( $redirect_to, $request, $user ) {
			if ( empty( $user ) || is_wp_error( $user ) ) {
				return $redirect_to;
			}

			if ( wdm_is_instructor( $user->ID ) ) {
				$redirect_to = add_query_arg( 'page', 'ir_instructor_overview', admin_url( 'admin.php' ) );

				$redirect_to = apply_filters( 'ir_login_redirect_filter', $redirect_to, $user );
			}

			return $redirect_to;
		}

		/**
		 * Redirect Instructors from Woocommerce Login
		 *
		 * @param string $redirect_to   URL to redirect to
		 * @param object $user          WP_User object of the user
		 *
		 * @return string $redirect_to   Updated URL, if user is instructor then instructor overiew page URL, else unchanged.
		 *
		 * @since 3.1.0
		 */
		function irWooInstructorRedirect( $redirect_to, $user ) {
			return $this->irInstructorRedirect( $redirect_to, '', $user );
		}

		/**
		 * Auto Enroll Instructors to their own courses
		 *
		 * @param bool $access      Whether the user has access to the post
		 * @param int  $post_id      ID of the post
		 * @param int  $user_id      ID of the user. If null, current user ID is used.
		 *
		 * @return bool             True if user has access to post, false otherwise
		 *
		 * @since   3.3.0
		 */
		function irAutoEnrollInstructorCourses( $access, $post_id, $user_id ) {
			if ( ! is_user_logged_in() || ! $post_id ) {
				return $access;
			}

			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			// Check if instructor.
			if ( ! wdm_is_instructor( $user_id ) ) {
				return $access;
			}

			$post          = get_post( $post_id );
			$ld_post_types = array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-question', 'sfwd-quiz', 'sfwd-topic' );

			if ( ! in_array( $post->post_type, $ld_post_types ) ) {
				return $access;
			}

			// Check if shared course
			$course_id = $post_id;
			if ( 'sfwd-courses' != $post->post_type ) {
				$course_id = learndash_get_course_id( $post_id );
			}
			$instructor_shared_courses = get_user_meta( $user_id, 'ir_shared_courses', 1 );
			$shared_courses            = explode( ',', $instructor_shared_courses );

			if ( ! empty( $shared_courses ) && in_array( $course_id, $shared_courses ) ) {
				return true;
			}

			if ( $user_id == $post->post_author ) {
				return true;
			}

			return $access;
		}

		/**
		 * This function is used to override linear progression in LearnDassh.
		 *
		 * @since   3.3.0
		 */
		public function irByPassInstructorLinearAccess( $original_status, $user_id, $course_id, $post ) {
			$bypass_course_limits_admin_users = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Admin_User', 'bypass_course_limits_admin_users' );
			if ( 'yes' === $bypass_course_limits_admin_users ) {
				$bypass_course_limits_admin_users = true;
			} else {
				$bypass_course_limits_admin_users = false;
			}
			if ( ! wdm_is_instructor( $user_id ) || ! $course_id ) {
				return $original_status;
			}

			// Check if instructor course.
			if ( 'sfwd-courses' !== $post->post_type ) {
				$course_id = learndash_get_course_id( $post );
			}

			$course = get_post( $course_id );
			if ( $user_id == $course->post_author ) {
				return $bypass_course_limits_admin_users;
			}

			// Check if shared course.
			$instructor_shared_courses = get_user_meta( $user_id, 'ir_shared_courses', 1 );
			$shared_courses            = explode( ',', $instructor_shared_courses );

			if ( ! empty( $shared_courses ) && in_array( $course_id, $shared_courses ) ) {
				return $bypass_course_limits_admin_users;
			}
			return false;
		}

		/**
		 * This method is used to show previous as completed for instructors as learndash doesn't honor bypass logic filter for linear progression but instead uses previous complete checks and forcefully sets it true for admin with bypass.
		 *
		 * @since   3.3.0
		 */
		public function irByPassInstructorPreviousCompleted( $is_previous_completed, $post_id, $user_id ) {
			$course_id = learndash_get_course_id( $post_id );
			if ( ! wdm_is_instructor( $user_id ) || ! $course_id ) {
				return $is_previous_completed;
			}

			// Check if instructor course.
			$course = get_post( $course_id );
			if ( $user_id == $course->post_author ) {
				return true;
			}

			// Check if shared course.
			$instructor_shared_courses = get_user_meta( $user_id, 'ir_shared_courses', 1 );
			$shared_courses            = explode( ',', $instructor_shared_courses );

			if ( ! empty( $shared_courses ) && in_array( $course_id, $shared_courses ) ) {
				return true;
			}
			return $is_previous_completed;
		}
		/**
		 * Bypass instructor user access to actions
		 *
		 * @param bool   $can_bypass    Whether user can bypass.
		 * @param int    $user_id       ID of user.
		 * @param string $context       The specific action to check bypassing for.
		 * @param array  $args          Optional array of arguments.
		 *
		 * @return bool                 True if instructor can bypass, false otherwise.
		 */
		public function ir_bypass_instructor_user_access( $can_bypass, $user_id, $context, $args ) {
			if ( wdm_is_instructor( $user_id ) && ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Admin_User', 'bypass_course_limits_admin_users' ) ) ) {
				$course_id          = learndash_get_course_id( $args );
				$instructor_courses = ir_get_instructor_complete_course_list();

				if ( in_array( $course_id, $instructor_courses ) ) {
					$can_bypass = true;
				}
			}
			return $can_bypass;
		}

		/**
		 * Update instructor course list on ld_profile shortcode to display all instructor and shared courses.
		 *
		 * @param array $args
		 * @return array
		 * @since 3.4.1
		 */
		public function irUpdateInstructorProfileCourses( $args ) {
			if ( wdm_is_instructor() ) {
				$instructor_courses   = ir_get_instructor_complete_course_list();
				$args['user_courses'] = array_unique( array_merge( $args['user_courses'], $instructor_courses ) );
			}
			return $args;
		}

		/**
		 * Deny access to a page to instructors
		 *
		 * @param array $data   Details about the current page.
		 * @return bool         Updated access for current screen
		 * @since 3.1.5
		 */
		public function irDenyPageAccess( $data ) {
			$restricted_base_screens = apply_filters(
				'ir_restricted_base_screens',
				array(
					'edit-tags',
					'tools',
					'upload',
					'media',
				)
			);

			if ( in_array( $data['current_scr_base'], $restricted_base_screens ) ) {
				return true;
			}

			if ( $data['current_scr_base'] == 'post' && ! isset( $data['get_post_type'] ) && ! $data['is_ld'] ) {
				return true;
			}

			if ( $data['current_scr_base'] == 'edit' && ! isset( $data['get_post_type'] ) && ! $data['is_ld'] ) {
				return true;
			}

			if ( $data['current_scr_base'] == 'edit' && isset( $data['get_post_type'] ) && ! in_array( trim( $data['get_post_type'] ), $data['arr_ld_post_types'] ) ) {
				return true;
			}

			if (
				$data['current_scr_base'] == 'post' &&
				isset( $data['get_post_type'] ) &&
				! in_array( trim( $data['get_post_type'] ), $data['arr_ld_post_types'] ) ||
				( $data['current_scr_base'] == 'appearance_page_' . $data['param_page'] )
			) {
				return true;
			}
		}

		/**
		 * Restrict access to jetpack page to instructors
		 *
		 * @param bool  $deny_access     Access to the screen.
		 * @param array $data           Data about current screen.
		 *
		 * @return bool                 Updated access for current screen
		 * @since 3.1.5
		 */
		public function irRestrictJetpackAccess( $deny_access, $data ) {
			if ( false !== strpos( $data['current_scr_base'], 'jetpack' ) ) {
				$deny_access = true;
			}
			return $deny_access;
		}

		/**
		 * Enable the user menu plugin settings to show/hide menu page links to instructors
		 *
		 * @since 3.1.5
		 */
		public function irEnableUserMenusForInstructors() {
			 // Check if User Menus plugin active.
			if ( class_exists( 'JP_User_Menus' ) ) {
				// Check if instructor.
				if ( wdm_is_instructor() ) {
					// Check if instructor dashboard.
					if ( is_admin() ) {
						// Enable user menu plugin features for instructor dashboard.
						$user_menus_instance = \JP_User_Menus::instance();
						require_once $user_menus_instance->plugin_path() . 'includes/classes/site/menus.php';
					}
				}
			}
		}

		/**
		 * Update all lesson,topic and quiz when course author is updated to an instructor.
		 *
		 * @param array $data
		 * @param array $post
		 *
		 * @return array
		 */
		public function irUpdateCourseToInstructors( $data, $post ) {
			// If not admin return.
			if ( ! current_user_can( 'manage_options' ) && apply_filters( 'ir_filter_default_course_author_update', true ) ) {
				return $data;
			}

			// Check if course edit page.
			if ( 'sfwd-courses' !== $data['post_type'] ) {
				return $data;
			}

			// Check if course author updated.
			if ( array_key_exists( 'post_author_override', $post ) && $post['post_author_override'] !== $data['post_author'] ) {
				$new_author_id = intval( $post['post_author_override'] );

				if ( ! wdm_is_instructor( $new_author_id ) ) {
					return $data;
				}

				$course_content_list = $this->irGetCourseContents( $post['ID'] );

				if ( ! empty( $course_content_list ) ) {
					foreach ( $course_content_list as $content_id ) {
						wp_update_post(
							array(
								'ID'          => $content_id,
								'post_author' => $new_author_id,
							)
						);
					}
				}
			}
			return $data;
		}

		/**
		 * Get all lessons, topics and quizzes for a specific Course
		 *
		 * @param int    $course_id    ID of the course.
		 * @param string $data
		 * @return array            List of all course contents.
		 */
		public function irGetCourseContents( $course_id, $data = '' ) {
			if ( empty( $data ) ) {
				$data = array();
			}

			// Get a list of lessons to loop.
			$lessons              = learndash_get_course_lessons_list( $course_id, null, array( 'num' => 0 ) );
			$course_contents_list = array();

			if ( ( is_array( $lessons ) ) && ( ! empty( $lessons ) ) ) {
				// Loop course's lessons.
				foreach ( $lessons as $lesson ) {
					$post = $lesson['post'];
					// Get lesson's topics.
					$topics = learndash_topic_dots( $post->ID, false, 'array', null, $course_id );

					if ( ( is_array( $topics ) ) && ( ! empty( $topics ) ) ) {
						// Loop Topics.
						foreach ( $topics as $topic ) {
							// Get topic's quizzes.
							$topic_quizzes = learndash_get_lesson_quiz_list( $topic->ID, null, $course_id );

							if ( ( is_array( $topic_quizzes ) ) && ( ! empty( $topic_quizzes ) ) ) {
								// Loop Topic's Quizzes.
								foreach ( $topic_quizzes as $quiz ) {
									$quiz_post              = $quiz['post'];
									$course_contents_list[] = $quiz_post->ID;
								}
							}
							$course_contents_list[] = $topic->ID;
						}
					}

					// Get lesson's quizzes.
					$quizzes = learndash_get_lesson_quiz_list( $post->ID, null, $course_id );

					if ( ( is_array( $quizzes ) ) && ( ! empty( $quizzes ) ) ) {
						// Loop lesson's quizzes.
						foreach ( $quizzes as $quiz ) {
							$quiz_post              = $quiz['post'];
							$course_contents_list[] = $quiz_post->ID;
						}
					}
					$course_contents_list[] = $post->ID;
				}
			}

			// Get a list of quizzes to loop.
			$quizzes = learndash_get_course_quiz_list( $course_id );

			if ( ( is_array( $quizzes ) ) && ( ! empty( $quizzes ) ) ) {
				// Loop course's quizzes.
				foreach ( $quizzes as $quiz ) {
					$post                   = $quiz['post'];
					$course_contents_list[] = $post->ID;
				}
			}

			return $course_contents_list;
		}

		/**
		 * Update Learndash data to allow access to course settings for instructors and disallow groups access.
		 *
		 * @param array $learndash_data     Learndash Data.
		 *
		 * @since 3.2.0
		 */
		public function updateLearnDashDataForInstructors( $learndash_data ) {
			global $post, $current_screen;

			// Check if instructor
			if ( ! is_user_logged_in() || ! wdm_is_instructor() ) {
				return $learndash_data;
			}

			// Check if course edit page.
			if ( 'sfwd-courses' != $post->post_type ) {
				return $learndash_data;
			}

			// Hide course settings option on course listing page.
			if ( 'edit-sfwd-courses' == $current_screen->id ) {
				foreach ( $learndash_data['tabs'] as $key => $header_tab ) {
					if ( 'sfwd-courses-settings' == $header_tab['id'] ) {
						unset( $learndash_data['tabs'][ $key ] );
					}
				}
			}

			return $learndash_data;
		}

		/**
		 * Add mobile menu icon on instructor dashboard.
		 *
		 * @since   3.2.1
		 */
		public function irAddMobileMenuIcon( $items, $args ) {
			// Check if admin side.
			if ( ! is_admin() ) {
				return $items;
			}
			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $items;
			}

			// Check if primary menu set.
			$menu_slug = 'ir-instructor-menu';

			// If empty, then return.
			if ( ! has_nav_menu( $menu_slug ) ) {
				return $items;
			}

			// Add mobile menu icon.
			$items = '<li class="wdm-mob-menu wdm-admin-menu-show wdm-hidden"><span class="dashicons dashicons-menu-alt"></span></li>' . $items;

			return $items;
		}

		/**
		 * Add menu links for instructor.
		 *
		 * @since 3.2.1.
		 */
		public function ir_add_instructor_dashboard_menu_items() {
			global $menu;

			// Check if menu set.
			if ( empty( $menu ) ) {
				return;
			}

			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return;
			}

			// Define additional menu items.
			$menu_items = array(
				array(
					__( 'Profile', 'wdm_instructor_role' ),
					'instructor_reports',
					get_edit_profile_url(),
					'',
					'menu-top',
					'',
					'dashicons-admin-users',
				),
				array(
					__( 'Logout', 'wdm_instructor_role' ),
					'instructor_reports',
					wp_logout_url( site_url() ),
					'',
					'menu-top',
					'',
					'dashicons-migrate',
				),
			);

			// Check whether profile menu item to be added.
			foreach ( $menu as $key => $menu_item ) {
				if ( in_array( 'profile.php', $menu_item, 1 ) ) {
					array_shift( $menu_items );
				}
			}

			// Allow 3rd party plugins to filter through menu items.
			$menu_items = apply_filters(
				'ir_filter_instructor_dashboard_menu_items',
				$menu_items
			);

			// Add menu links.
			foreach ( $menu_items as $item ) {
				array_push(
					$menu,
					$item
				);
			}
		}

		/**
		 * Filter author dropdown in post edit sidebar to fetch instructors and administrators
		 * for course, lesson, topic and quizzes.
		 *
		 * @author  Kumar Rajpurohit
		 */
		public function irFilterAuthorDropDown( $prepared_args, $request ) {
			// Check if valid request.
			if ( ! $request instanceof \WP_REST_Request ) {
				return $prepared_args;
			}

			// Verify headers.
			$headers = $request->get_headers();
			if ( empty( $headers ) || ! wp_verify_nonce( $headers['x_wp_nonce'][0], 'wp_rest' ) ) {
				return $prepared_args;
			}

			// Get post id from headers.
			$referer_url     = $headers['referer'][0];
			$referer_details = parse_url( $referer_url );
			$query_args      = array();

			// Extract post details.
			parse_str( $referer_details['query'], $query_args );

			$current_post_type = '';

			// Get post type from post
			if ( array_key_exists( 'post', $query_args ) && ! empty( $query_args['post'] ) ) {
				$post_id           = intval( $query_args['post'] );
				$post              = get_post( $post_id );
				$current_post_type = $post->post_type;
			}

			// or directly from query args if set.
			if ( array_key_exists( 'post_type', $query_args ) ) {
				$current_post_type = $query_args['post_type'];
			}

			$allowed_post_types = array(
				'sfwd-courses',
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-quiz',
			);

			// Check if valid post type.
			if ( ! in_array( $current_post_type, $allowed_post_types ) ) {
				return $prepared_args;
			}

			// Check if author request.
			if ( ! array_key_exists( 'who', $prepared_args ) && 'authors' == $prepared_args['who'] ) {
				return $prepared_args;
			}

			unset( $prepared_args['who'] );

			// Allow admin to be listed in authors.
			$prepared_args['role__in'] = array( 0 );

			return $prepared_args;
		}

		/**
		 * Display additional instructor settings.
		 *
		 * @since 3.5.2
		 */
		public function display_additional_instructor_settings() {
			$ir_admin_settings = get_option( '_wdmir_admin_settings', array() );
			$active_theme      = wp_get_theme()->template;

			$is_ld_category = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' );
			$is_wp_category = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'wp_post_category' );

			$additional_settings['enable_ld_category']       = isset( $ir_admin_settings['enable_ld_category'] ) ? $ir_admin_settings['enable_ld_category'] : '';
			$additional_settings['enable_wp_category']       = isset( $ir_admin_settings['enable_wp_category'] ) ? $ir_admin_settings['enable_wp_category'] : '';
			$additional_settings['enable_permalinks']        = isset( $ir_admin_settings['enable_permalinks'] ) ? $ir_admin_settings['enable_permalinks'] : '';
			$additional_settings['enable_elu_header']        = isset( $ir_admin_settings['enable_elu_header'] ) ? $ir_admin_settings['enable_elu_header'] : '';
			$additional_settings['enable_elu_layout']        = isset( $ir_admin_settings['enable_elu_layout'] ) ? $ir_admin_settings['enable_elu_layout'] : '';
			$additional_settings['enable_elu_cover']         = isset( $ir_admin_settings['enable_elu_cover'] ) ? $ir_admin_settings['enable_elu_cover'] : '';
			$additional_settings['enable_bb_cover']          = isset( $ir_admin_settings['enable_bb_cover'] ) ? $ir_admin_settings['enable_bb_cover'] : '';
			$additional_settings['enable_open_pricing']      = isset( $ir_admin_settings['enable_open_pricing'] ) ? $ir_admin_settings['enable_open_pricing'] : '';
			$additional_settings['enable_free_pricing']      = isset( $ir_admin_settings['enable_free_pricing'] ) ? $ir_admin_settings['enable_free_pricing'] : '';
			$additional_settings['enable_buy_pricing']       = isset( $ir_admin_settings['enable_buy_pricing'] ) ? $ir_admin_settings['enable_buy_pricing'] : '';
			$additional_settings['enable_recurring_pricing'] = isset( $ir_admin_settings['enable_recurring_pricing'] ) ? $ir_admin_settings['enable_recurring_pricing'] : '';
			$additional_settings['enable_closed_pricing']    = isset( $ir_admin_settings['enable_closed_pricing'] ) ? $ir_admin_settings['enable_closed_pricing'] : '';

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/settings/ir-additional-settings.template.php',
				array(
					'course_label'        => \LearnDash_Custom_Label::get_label( 'course' ),
					'lesson_label'        => \LearnDash_Custom_Label::get_label( 'lesson' ),
					'topic_label'         => \LearnDash_Custom_Label::get_label( 'topic' ),
					'group_label'         => \LearnDash_Custom_Label::get_label( 'group' ),
					'is_ld_category'      => $is_ld_category,
					'is_wp_category'      => $is_wp_category,
					'active_theme'        => $active_theme,
					'additional_settings' => $additional_settings,
				)
			);
		}

		/**
		 * Toggle LD and WP category settings on instructor dashboard.
		 *
		 * @since 3.5.2
		 *
		 * @param array $post_arguments List of LD post arguments.
		 */
		public function toggle_category_settings( $post_arguments ) {
			if ( ! wdm_is_instructor() ) {
				return $post_arguments;
			}

			$ir_admin_settings = get_option( '_wdmir_admin_settings', array() );

			$enable_ld_category = isset( $ir_admin_settings['enable_ld_category'] ) ? $ir_admin_settings['enable_ld_category'] : '';
			$enable_wp_category = isset( $ir_admin_settings['enable_wp_category'] ) ? $ir_admin_settings['enable_wp_category'] : '';

			if ( array_key_exists( 'ld_course_category', $post_arguments['sfwd-courses']['taxonomies'] ) && 'off' == $enable_ld_category ) {
				unset( $post_arguments['sfwd-courses']['taxonomies']['ld_course_category'] );
			}

			if ( array_key_exists( 'category', $post_arguments['sfwd-courses']['taxonomies'] ) && 'off' == $enable_wp_category ) {
				unset( $post_arguments['sfwd-courses']['taxonomies']['category'] );
			}

			return $post_arguments;
		}

		/**
		 * Toggle instructor metaboxes on instructor dashboard.
		 *
		 * @since 3.5.2
		 */
		public function toggle_instructor_metaboxes() {
			if ( wdm_is_instructor() ) {
				$ir_admin_settings = get_option( '_wdmir_admin_settings', array() );

				$enable_permalinks = isset( $ir_admin_settings['enable_permalinks'] ) ? $ir_admin_settings['enable_permalinks'] : '';
				$enable_elu_cover  = isset( $ir_admin_settings['enable_elu_cover'] ) ? $ir_admin_settings['enable_elu_cover'] : '';

				// Disable permalinks and/or other wp gutenberg panels.
				if ( 'off' == $enable_permalinks ) {
					/**
					 * Filter WP gutenberg panels to be removed.
					 *
					 * @var array $panels   List of wp metabox panels to be removed from the instructor dashboard.
					 * @var array $ir_admin_settings    List of instructor settings.
					 *
					 * @since 3.5.2
					 */
					$panels = apply_filters(
						'ir_filter_dashboard_wp_panels',
						array(
							'post-link',
						),
						$ir_admin_settings
					);
					$this->remove_wp_panels( $panels );
				}

				// Disable Elumine Cover Image metabox.
				if ( 'off' == $enable_elu_cover ) {
					remove_meta_box( 'elFeaturedMetaBox-2', 'sfwd-courses', 'side' );
				}
			}
		}

		/**
		 * Toggle Elumine theme metaboxes on instructor dashboard
		 *
		 * @since 3.5.2
		 *
		 * @param array $elumine_options    Elumine theme options.
		 */
		public function toggle_elumine_metaboxes( $elumine_options ) {
			if ( wdm_is_instructor() ) {
				$ir_admin_settings = get_option( '_wdmir_admin_settings', array() );

				$enable_elu_header = isset( $ir_admin_settings['enable_elu_header'] ) ? $ir_admin_settings['enable_elu_header'] : '';
				$enable_elu_layout = isset( $ir_admin_settings['enable_elu_layout'] ) ? $ir_admin_settings['enable_elu_layout'] : '';

				// Disable Elumine Header metabox.
				if ( 'off' == $enable_elu_header && array_key_exists( 'page-header-settings', $elumine_options ) ) {
					unset( $elumine_options['page-header-settings'] );
				}

				// Disable Elumine Layout metabox.
				if ( 'off' == $enable_elu_layout && array_key_exists( 'page-layout-settings', $elumine_options ) ) {
					unset( $elumine_options['page-layout-settings'] );
				}
			}
			return $elumine_options;
		}

		/**
		 * Remove WP gutenberg sections on instructor dashboard
		 *
		 * @since 3.5.2
		 *
		 * @param array $panels List of panels(metaboxes) to be removed.
		 */
		protected function remove_wp_panels( $panels ) {
			// Register additional settings custom script.
			wp_register_script(
				'ir-additional-settings-block-script',
				plugins_url( 'modules/js/ir-additional-settings-block-script.js', INSTRUCTOR_ROLE_BASE ),
				array( 'wp-blocks', 'wp-edit-post' ),
				filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/ir-additional-settings-block-script.js' ),
				false
			);
			// Localize the data to be used.
			wp_localize_script(
				'ir-additional-settings-block-script',
				'ir_settings_data',
				$panels
			);
			// Register block editor script.
			register_block_type(
				'ir/additional-settings-block',
				array(
					'editor_script' => 'ir-additional-settings-block-script',
				)
			);
		}

		/**
		 * Save additional instructor settings.
		 *
		 * @since 3.5.2
		 */
		public function save_additional_instructor_settings() {
			$additional_settings = array();
			$ir_admin_settings   = get_option( '_wdmir_admin_settings', array() );

			// Enable LD Category.
			$additional_settings['enable_ld_category'] = 'on';
			if ( ! array_key_exists( 'enable_ld_category', $_POST ) || empty( $_POST['enable_ld_category'] ) ) {
				$additional_settings['enable_ld_category'] = 'off';
			}
			// Enable WP Category.
			$additional_settings['enable_wp_category'] = 'on';
			if ( ! array_key_exists( 'enable_wp_category', $_POST ) || empty( $_POST['enable_wp_category'] ) ) {
				$additional_settings['enable_wp_category'] = 'off';
			}
			// Enable Permalinks.
			$additional_settings['enable_permalinks'] = 'on';
			if ( ! array_key_exists( 'enable_permalinks', $_POST ) || empty( $_POST['enable_permalinks'] ) ) {
				$additional_settings['enable_permalinks'] = 'off';
			}
			// Enable Elumine Header.
			$additional_settings['enable_elu_header'] = 'on';
			if ( ! array_key_exists( 'enable_elu_header', $_POST ) || empty( $_POST['enable_elu_header'] ) ) {
				$additional_settings['enable_elu_header'] = 'off';
			}
			// Enable Elumine Layout.
			$additional_settings['enable_elu_layout'] = 'on';
			if ( ! array_key_exists( 'enable_elu_layout', $_POST ) || empty( $_POST['enable_elu_layout'] ) ) {
				$additional_settings['enable_elu_layout'] = 'off';
			}
			// Enable Elumine Cover.
			$additional_settings['enable_elu_cover'] = 'on';
			if ( ! array_key_exists( 'enable_elu_cover', $_POST ) || empty( $_POST['enable_elu_cover'] ) ) {
				$additional_settings['enable_elu_cover'] = 'off';
			}
			// Enable BuddyBoss Cover.
			$additional_settings['enable_bb_cover'] = 'on';
			if ( ! array_key_exists( 'enable_bb_cover', $_POST ) || empty( $_POST['enable_bb_cover'] ) ) {
				$additional_settings['enable_bb_cover'] = 'off';
			}

			// Enable Open Pricing.
			$additional_settings['enable_open_pricing'] = 'on';
			if ( ! array_key_exists( 'enable_open_pricing', $_POST ) || empty( $_POST['enable_open_pricing'] ) ) {
				$additional_settings['enable_open_pricing'] = 'off';
			}

			// Enable Free Pricing.
			$additional_settings['enable_free_pricing'] = 'on';
			if ( ! array_key_exists( 'enable_free_pricing', $_POST ) || empty( $_POST['enable_free_pricing'] ) ) {
				$additional_settings['enable_free_pricing'] = 'off';
			}

			// Enable Buy Pricing.
			$additional_settings['enable_buy_pricing'] = 'on';
			if ( ! array_key_exists( 'enable_buy_pricing', $_POST ) || empty( $_POST['enable_buy_pricing'] ) ) {
				$additional_settings['enable_buy_pricing'] = 'off';
			}

			// Enable Recurring Pricing.
			$additional_settings['enable_recurring_pricing'] = 'on';
			if ( ! array_key_exists( 'enable_recurring_pricing', $_POST ) || empty( $_POST['enable_recurring_pricing'] ) ) {
				$additional_settings['enable_recurring_pricing'] = 'off';
			}

			// Enable Closed Pricing.
			$additional_settings['enable_closed_pricing'] = 'on';
			if ( ! array_key_exists( 'enable_closed_pricing', $_POST ) || empty( $_POST['enable_closed_pricing'] ) ) {
				$additional_settings['enable_closed_pricing'] = 'off';
			}

			$ir_admin_settings = array_merge( $ir_admin_settings, $additional_settings );

			update_option( '_wdmir_admin_settings', $ir_admin_settings );
		}

		/**
		 * Update course and group pricing settings for instructors
		 *
		 * @param array  $settings_fields    List of LD settings.
		 * @param string $metabox_key        Key of the metabox setting.
		 *
		 * @return array                     Updated list of LD settings.
		 *
		 * @since 3.5.2
		 */
		public function update_instructor_course_pricing_options( $settings_fields, $metabox_key ) {
			if ( wdm_is_instructor() && ( 'learndash-course-access-settings' === $metabox_key || 'learndash-group-access-settings' === $metabox_key ) ) {
				$ir_admin_settings = get_option( '_wdmir_admin_settings', array() );

				$open      = isset( $ir_admin_settings['enable_open_pricing'] ) ? $ir_admin_settings['enable_open_pricing'] : '';
				$free      = isset( $ir_admin_settings['enable_free_pricing'] ) ? $ir_admin_settings['enable_free_pricing'] : '';
				$buy       = isset( $ir_admin_settings['enable_buy_pricing'] ) ? $ir_admin_settings['enable_buy_pricing'] : '';
				$recurring = isset( $ir_admin_settings['enable_recurring_pricing'] ) ? $ir_admin_settings['enable_recurring_pricing'] : '';
				$closed    = isset( $ir_admin_settings['enable_closed_pricing'] ) ? $ir_admin_settings['enable_closed_pricing'] : '';

				if ( 'learndash-course-access-settings' === $metabox_key ) {
					$key = 'course_price_type';
				}

				if ( 'learndash-group-access-settings' === $metabox_key ) {
					$key = 'group_price_type';
				}

				// Disable Open.
				if ( 'off' == $open && 'course_price_type' === $key ) {
					unset( $settings_fields[ $key ]['options']['open'] );
				}
				// Disable Free.
				if ( 'off' == $free ) {
					unset( $settings_fields[ $key ]['options']['free'] );
				}
				// Disable Buy.
				if ( 'off' == $buy ) {
					unset( $settings_fields[ $key ]['options']['paynow'] );
				}
				// Disable Recurring.
				if ( 'off' == $recurring ) {
					unset( $settings_fields[ $key ]['options']['subscribe'] );
				}
				// Disable Closed.
				if ( 'off' == $closed ) {
					unset( $settings_fields[ $key ]['options']['closed'] );
				}
			}
			return $settings_fields;
		}

		/**
		 * Whitelabel learndash text strings
		 *
		 * @since 3.5.2
		 *
		 * @param string $translation   Translation of the message.
		 * @param string $text          Original text message.
		 * @param string $domain        Text domain of the message.
		 */
		public function whitelabel_learndash_strings( $translation, $text, $domain ) {
			// Whitelabel LD strings in elumine and core LD strings.
			if ( 'elumine' === $domain || 'learndash' === $domain ) {
				// Authentication cookie check added as a fix for multisite installations.
				if ( defined( 'AUTH_COOKIE' ) && wdm_is_instructor() ) {
					if ( 'LearnDash Introduction Video' === $text ) {
						$translation = str_replace( 'LearnDash', '', $translation );
					}
					if ( 'LearnDash Login' === $text ) {
						$translation = str_replace( 'LearnDash', '', $translation );
					}

					if ( 'LearnDash Certificate Options' === $text ) {
						$translation = str_replace( 'LearnDash', '', $translation );
					}
				}
			}
			return $translation;
		}

		/**
		 * Whitelabel learndash context strings
		 *
		 * @since 3.5.2
		 *
		 * @param string $translation   Translation of the message.
		 * @param string $text          Original text message.
		 * @param string $context       Context information for the translators.
		 * @param string $domain        Text domain of the message.
		 */
		public function whitelabel_learndash_context_strings( $translation, $text, $context, $domain ) {
			// Whitelabel core LD strings.
			if ( 'learndash' === $domain ) {
				if ( defined( 'AUTH_COOKIE' ) && wdm_is_instructor() ) {
					if ( 'LearnDash %s Settings' === $text ) {
						$translation = str_replace( 'LearnDash', '', $translation );
					}
				}
			}
			return $translation;
		}

		/**
		 * Disable more help sections on instructor dashboard.
		 *
		 * @since 3.5.2
		 */
		public function disable_ld_more_help_section() {
			$custom_styles = 'div.ld-onboarding-more-help{ display: none; }';
			wp_add_inline_style( 'ir-instructor-styles', $custom_styles );
		}
	}
}
