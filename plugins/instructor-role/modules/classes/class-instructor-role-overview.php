<?php

/**
 * Instructor Overview Module
 *
 * @since      3.5.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/modules/classes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Includes\Instructor_Role as Instructor_Role;

defined( 'ABSPATH' ) || exit;

if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( ! class_exists( 'Instructor_Role_Overview' ) ) ) {
	/**
	 * Class Instructor Role Overview Module
	 */
	class Instructor_Role_Overview extends \LearnDash_Settings_Page {


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

		/**
		 * Course Count
		 *
		 * @var int
		 * @since 3.1.0
		 */
		public $course_count = null;

		/**
		 * Student Count
		 *
		 * @var int
		 * @since 3.1.0
		 */
		public $student_count = null;

		/**
		 * Addon Details
		 *
		 * Details about woocommerce and edd data count.
		 *
		 * @var array
		 * @since 3.1.0
		 */
		public $addon_info = null;

		/**
		 * Instructor earnings
		 *
		 * @var array
		 * @since 3.1.0
		 */
		public $earnings = null;

		/**
		 * Page links for various instructor pages
		 *
		 * @var array
		 * @since 3.1.0
		 */
		public $page_links = null;

		/**
		 * Courses label for LearnDash courses
		 *
		 * @var string
		 * @since 3.4.0
		 */
		public $courses_label = '';

		public function __construct() {
			$this->plugin_slug          = INSTRUCTOR_ROLE_TXT_DOMAIN;
			$this->parent_menu_page_url  = 'admin.php?page=ir_instructor_overview';
			$this->menu_page_capability  = 'edit_courses';
			$this->settings_page_id      = 'ir_instructor_overview';
			$this->settings_page_title   = esc_html__( 'Instructor Overview', 'learndash' );
			$this->settings_tab_title    = esc_html__( 'Overview', 'learndash' );
			$this->settings_tab_priority = 0;
			$this->page_links            = array(
				'courses' => add_query_arg( array( 'post_type' => 'sfwd-courses' ), admin_url( 'edit.php' ) ),
				// 'students'	=>	admin_url()
				'woo'     => add_query_arg( array( 'post_type' => 'product' ), admin_url( 'edit.php' ) ),
				'edd'     => add_query_arg( array( 'post_type' => 'download' ), admin_url( 'edit.php' ) ),
			);

			// Commented since `LearnDash_Custom_Label` not present when instance is created.
			// $this->courses_label = __( 'Courses', 'wdm_instructor_role' );
			// if ( class_exists( '\LearnDash_Custom_Label' ) ) {
			// $this->courses_label = \LearnDash_Custom_Label::get_label( 'courses' );
			// }.

			// Get all the data.
			$this->irSetInstructorOverviewData();

			add_filter( 'learndash_submenu', array( $this, 'irAddSubmenuItem' ), 200 );
			add_filter( 'learndash_header_data', array( $this, 'admin_header' ), 40, 3 );
			add_action( 'admin_enqueue_scripts', array( $this, 'irOverviewEnqueueScripts' ) );
			add_action( 'wp_ajax_ir-update-course-chart', array( $this, 'ajaxUpdateCourseChart' ) );
			add_filter( 'ir_filter_earnings_localized_data', array( $this, 'addEarningsLocalizedData' ), 10, 1 );
			add_filter( 'ir_filter_chart_localized_data', array( $this, 'addChartLocalizedData' ), 10, 1 );

			parent::__construct();
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
		 * Control visibility of submenu items
		 *
		 * @since 3.1.0
		 *
		 * @param array $submenu Submenu item to check.
		 * @return array $submenu
		 */
		public function irAddSubmenuItem( $submenu ) {
			if ( ! isset( $submenu[ $this->settings_page_id ] ) ) {
				$submenu_save = $submenu;
				$submenu      = array();

				$submenu[ $this->settings_page_id ] = array(
					'name'  => $this->settings_tab_title,
					'cap'   => $this->menu_page_capability,
					'link'  => $this->parent_menu_page_url,
					'class' => 'submenu-ldlms-overview',
				);

				$submenu = array_merge( $submenu, $submenu_save );
			}

			return $submenu;
		}

		/**
		 * Filter the admin header data. We don't want to show the header panel on the Overview page.
		 *
		 * @since 3.0
		 * @param array  $header_data Array of header data used by the Header Panel React app.
		 * @param string $menu_key The menu key being displayed.
		 * @param array  $menu_items Array of menu/tab items.
		 *
		 * @return array $header_data.
		 */
		public function admin_header( $header_data = array(), $menu_key = '', $menu_items = array() ) {
			 // Clear out $header_data if we are showing our page.
			if ( $menu_key === $this->parent_menu_page_url ) {
				$header_data = array();
			}

			return $header_data;
		}

		/**
		 * Filter for page title wrapper.
		 *
		 * @since 3.0.0
		 */
		public function get_admin_page_title() {
			return apply_filters( 'learndash_admin_page_title', '<h1>' . $this->settings_page_title . '</h1>' );
		}

		/**
		 * Custom display function for page content.
		 *
		 * @since 3.1.0
		 */
		public function show_settings_page() {
			$course_list = ir_get_instructor_complete_course_list();
			$ajax_icon   = plugins_url( 'css/images/loading.svg', __DIR__ );

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/overview/ir-instructor-overview.template.php',
				array(
					'course_list' => $course_list,
					'ajax_icon'   => $ajax_icon,
					'instance'    => $this,
				)
			);
		}

		/**
		 * Set instructor overview data
		 */
		private function irSetInstructorOverviewData() {
			$this->course_count  = 0;
			$this->student_count = 0;

			$user_id = get_current_user_id();

			// Refresh shared courses
			ir_refresh_shared_course_details( $user_id );

			// Final instructor course list
			$course_list = ir_get_instructor_complete_course_list( $user_id );

			// No courses yet...
			if ( ! empty( $course_list ) && array_sum( $course_list ) > 0 ) {
				$this->course_count = count( $course_list );

				// Fetch the list of students in the courses.
				$all_students = array();
				foreach ( $course_list as $course_id ) {
					// Check if trashed course.
					if ( 'trash' == get_post_status( $course_id ) ) {
						$this->course_count--;
					}

					$students_list = ir_get_users_with_course_access( $course_id, array( 'direct', 'group' ) );

					if ( empty( $students_list ) ) {
						continue;
					}
					$all_students = array_merge( $all_students, $students_list );
				}

				$unique_students_list = array_unique( $all_students );
				$this->student_count  = count( $unique_students_list );
			}

			$this->setAddonDetails( $user_id );
		}

		/**
		 * Set addon details
		 *
		 * @param int $user_id      User ID
		 */
		private function setAddonDetails( $user_id = 0 ) {
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			// Check if woocommerce activated
			if ( class_exists( 'WooCommerce' ) && wdmCheckWooDependency() ) {
				$products                     = new \WP_Query(
					array(
						'post_type' => 'product',
						'author'    => $user_id,
					)
				);
				$this->addon_info['products'] = $products->found_posts;
			}

			// Check if edd activated
			if ( class_exists( 'Easy_Digital_Downloads' ) && wdmCheckEDDDependency() ) {
				$downloads                     = new \WP_Query(
					array(
						'post_type' => 'download',
						'author'    => $user_id,
					)
				);
				$this->addon_info['downloads'] = $downloads->found_posts;
			}
		}

		/**
		 * Fetch course data for the chart
		 *
		 * @param int $course_id    ID of the course
		 * @return array            Array of course chart data.
		 */
		protected function fetchCourseChartData( $course_id ) {
			 $enrolled_user_list = ir_get_users_with_course_access( $course_id, array( 'direct', 'group' ) );

			$chart_data = array(
				'title'       => get_the_title( $course_id ),
				'not_started' => 0,
				'in_progress' => 0,
				'completed'   => 0,
				'total'       => count( $enrolled_user_list ),
			);

			$chart_data = apply_filters( 'ir_filter_chart_localized_data', $chart_data );

			foreach ( $enrolled_user_list as $user_id ) {
				$course_status = learndash_course_status( $course_id, $user_id, true );
				switch ( $course_status ) {
					case 'not-started':
						$chart_data['not_started']++;
						break;
					case 'in-progress':
						$chart_data['in_progress']++;
						break;
					case 'completed':
						$chart_data['completed']++;
						break;
				}
			}

			return $chart_data;
		}

		/**
		 * Enqueue Overview scripts
		 */
		public function irOverviewEnqueueScripts() {
			global $current_screen;

			// Check if is instructor.
			if ( ! wdm_is_instructor() ) {
				return;
			}

			// Check if overview page.
			if ( 'admin_page_' . $this->settings_page_id != $current_screen->id ) {
				return;
			}

			// Get instructor complete course list.
			$course_list = ir_get_instructor_complete_course_list();

			wp_enqueue_style(
				'woo-icon-fonts',
				plugins_url( 'css/woo-fonts/style.css', __DIR__ )
			);

			if ( is_rtl() ) {
				$path = plugins_url( 'css/ir-instructor-overview-styles-rtl.css', __DIR__ );
			} else {
				$path = plugins_url( 'css/ir-instructor-overview-styles.css', __DIR__ );
			}
			wp_enqueue_style(
				'ir-instructor-overview-styles',
				$path
			);

			wp_enqueue_script( 'wdmHighcharts', plugins_url( 'js/highchart.js', __DIR__ ) );

			wp_enqueue_script(
				'ir-instructor-overview-script',
				plugins_url( 'js/ir-instructor-overview-script.js', __DIR__ ),
				array( 'wdmHighcharts' )
			);

			wp_enqueue_script(
				'ir-datatables-script',
				'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'
			);

			$earnings = self::calculateInstructorEarnings();

			$earnings = apply_filters( 'ir_filter_earnings_localized_data', $earnings );

			$course_id = '';

			if ( ! empty( $course_list ) ) {
				$course_id = array_shift( $course_list );
			}

			$chart_data = $this->fetchCourseChartData( $course_id );

			$chart_data = apply_filters( 'ir_filter_chart_localized_data', $chart_data );

			// Fetch Theme Colors.
			$colors = array(
				'#696969',
				'#85bb65',
				'#C3DD5A',
			);

			$theme      = get_option( 'eat_admin_theme_settings' );
			$visibility = false;

			if ( ! empty( $theme ) && array_key_exists( 'visibility', $theme ) ) {
				$visibility = $theme['visibility'];
			}

			if ( false !== $visibility && $visibility['enable_for_instructor'] ) {
				if ( ! empty( $theme['general-settings']['template'] ) ) {
					switch ( $theme['general-settings']['template'] ) {
						case 'temp-3':
							$colors = array(
								'#2f647d',
								'#444',
								'#fff',
							);
							break;

						case 'temp-14':
							$colors = array(
								'rgb(47, 47, 47)',
								'rgb(241, 177, 54)',
								'#fff',
							);
							break;

						case 'temp-13':
							$colors = array(
								'#20194d',
								'#3b1a2e',
								'#fff',
							);
							break;

						default:
							$colors = array(
								'#696969',
								'#85bb65',
								'#C3DD5A',
							);
							break;
					}
				}
			}

			$localized_data = array(
				'chart_data' => $chart_data,
				'course_id'  => $course_id,
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'earnings'   => $earnings,
				'colors'     => $colors,
				'is_rtl'     => true,
			);

			wp_localize_script( 'ir-instructor-overview-script', 'ir_data', $localized_data );
		}

		/**
		 * Update course chart via ajax
		 */
		public function ajaxUpdateCourseChart() {
			if ( empty( $_POST ) || ! ( array_key_exists( 'action', $_POST ) && 'ir-update-course-chart' == $_POST['action'] ) ) {
				die();
			}
			$course_id = filter_input( INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT );

			if ( empty( $course_id ) ) {
				echo json_encode( array( 'error' => __( 'No Data Found', 'wdm_instructor_role' ) ) );
				die();
			}

			$course_data = $this->fetchCourseChartData( $course_id );
			echo json_encode( $course_data );
			die();
		}

		/**
		 * Generate submission reports for the overview page
		 */
		public function generateSubmissionReports() {
			$no_of_records = 10;
			$page_no       = 1;

			/**
			 * Allow 3rd party plugins to filter through the submissions array.
			 *
			 * @since 3.1.0
			 */
			$submissions = apply_filters( 'ir_overview_submissions', $this->getSubmissionReportData( $page_no, $no_of_records ) );

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/overview/ir-submission-reports.template.php',
				array(
					'submissions' => $submissions,
					'instance'    => $this,
				)
			);
		}

		/**
		 * Calculate Instructor earnings
		 *
		 * @param int $user_id      ID of the user.
		 */
		public static function calculateInstructorEarnings( $user_id = 0 ) {
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			if ( empty( $user_id ) ) {
				return false;
			}

			$earnings = array(
				'paid'   => 0,
				'unpaid' => 0,
				'total'  => 0,
			);

			global $wpdb;
			$instructor_data = get_userdata( $user_id );

			$table = $wpdb->prefix . 'wdm_instructor_commission';
			$sql   = $wpdb->prepare( "SELECT commission_price FROM $table where user_id = %d", $user_id );

			$commissions = $wpdb->get_col( $sql );
			if ( empty( $commissions ) ) {
				return $earnings;
			}

			$total_commission = array_sum( $commissions );
			$paid_amount      = floatval( get_user_meta( $user_id, 'wdm_total_amount_paid', 1 ) );

			if ( empty( $paid_amount ) ) {
				$paid_amount = 0;
			}

			$earnings['paid']   = $paid_amount;
			$earnings['total']  = $total_commission;
			$earnings['unpaid'] = $total_commission - $paid_amount;

			return $earnings;
		}

		/**
		 * Get essay points
		 *
		 * @param int $essay_id         ID of the essay.
		 * @param int $question_id      ID of the question.
		 * @return mixed
		 */
		public function getEssayPoints( $essay_id, $question_id ) {
			 $essay = get_post( $essay_id );
			if ( empty( $essay ) || empty( $question_id ) ) {
				return false;
			}

			$author = $essay->post_author;

			$quiz_data = maybe_unserialize( get_user_meta( $author, '_sfwd-quizzes', 1 ) );

			if ( empty( $quiz_data ) ) {
				return false;
			}
			$grade_data = array_pop( array_column( $quiz_data, 'graded' ) );

			if ( ! array_key_exists( $question_id, $grade_data ) ) {
				return false;
			}

			return $grade_data[ $question_id ]['points_awarded'];
		}

		/**
		 * Add earnings localized data
		 *
		 * @param array $earnings       Earnings data to be localized.
		 * @return array
		 */
		public function addEarningsLocalizedData( $earnings ) {
			$earnings['title']               = __( 'Earnings', 'wdm_instructor_role' );
			$earnings['paid_label']          = __( 'Paid', 'wdm_instructor_role' );
			$earnings['unpaid_label']        = __( 'Unpaid', 'wdm_instructor_role' );
			$earnings['default_units_value'] = __( 'Units', 'wdm_instructor_role' );

			return $earnings;
		}

		/**
		 * Add Charts localized data
		 *
		 * @param array $chart_data     Chart data to be localized.
		 * @return array
		 * @since
		 */
		public function addChartLocalizedData( $chart_data ) {
			$chart_data['not_started_label']         = __( 'Not Started', 'wdm_instructor_role' );
			$chart_data['in_progress_label']         = __( 'In Progress', 'wdm_instructor_role' );
			$chart_data['completed_label']           = __( 'Completed', 'wdm_instructor_role' );
			$chart_data['default_user_value']        = __( 'Users', 'wdm_instructor_role' );
			$chart_data['default_course_chart_name'] = __( sprintf( '%s Chart Name', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' );

			return $chart_data;
		}

		/**
		 * Get submission reports data
		 *
		 * @param int $page_no
		 * @param int $no_of_records
		 * @return mixed
		 */
		public function getSubmissionReportData( $page_no, $no_of_records ) {
			$offset          = intval( $page_no * $no_of_records );
			$current_user_id = intval( get_current_user_id() );

			// Assignments
			$assignment_ids = get_posts(
				array(
					'post_type'   => 'sfwd-assignment',
					'numberposts' => -1,
					// 'posts_per_page'	=>		$no_of_records,
					'orderby'     => 'date',
					'order'       => 'DESC',
					'post_status' => 'publish',
					'fields'      => 'ids',
					// 'offset'		    =>		$offset,
				)
			);

			// Complete instructor course list
			$course_list = ir_get_instructor_complete_course_list( $current_user_id );

			$assignments = array();
			foreach ( $assignment_ids as $assignment_id ) {
				$assignment_details = get_post_meta( $assignment_id );

				if ( empty( $assignment_details ) ) {
					continue;
				}

				// $lesson_id = $assignment_details['lesson_id'][0];
				// $lesson_author = intval(get_post_field('post_author', $lesson_id));
				// $course_author = intval(get_post_field('post_author', $assignment_details['course_id'][0]));

				// Find the course related to the assignment.
				$course_id = $assignment_details['course_id'][0];

				// If course not owned or shared with current instructor, continue to next assignment
				// if ($current_user_id !== $lesson_author && $current_user_id !== $course_author) {
				if ( ! in_array( $course_id, $course_list ) ) {
					continue;
				}

				$course_title = get_the_title( $assignment_details['course_id'][0] );
				$course_title = empty( $course_title ) ? '-' : $course_title;

				$lesson_title = $assignment_details['lesson_title'][0];
				$lesson_title = empty( $lesson_title ) ? '-' : $lesson_title;

				$date = get_the_date( 'd M y, H:i', $assignment_id );
				// $date = empty($date) ? '-':$lesson_title;

				$points = array_key_exists( 'points', $assignment_details ) ? $assignment_details['points'][0] : '-';

				$status = array_key_exists(
					'approval_status',
					$assignment_details
				) ? $assignment_details['approval_status'][0] : 0;

				$download_link = $assignment_details['file_link'][0];
				$download_link = empty( $download_link ) ? '' : $download_link;

				array_push(
					$assignments,
					array(
						'title'     => $assignment_details['file_name'][0],
						'course'    => $course_title,
						'lesson'    => $lesson_title,
						'date'      => $date,
						'points'    => $points,
						'status'    => ( $status ) ? __( 'Approved', 'wdm_instructor_role' ) : __( 'Not Approved', 'wdm_instructor_role' ),
						'edit_link' => get_the_permalink( $assignment_id ),
						'link'      => add_query_arg(
							array(
								'post'   => $assignment_id,
								'action' => 'edit',
							),
							admin_url( 'post.php' )
						),
						'type'      => __( 'Assignment', 'wdm_instructor_role' ),
					)
				);
			}

			if ( count( $assignment_ids ) == $no_of_records ) {
				return $assignments;
			}

			// Essays
			$essay_ids = get_posts(
				array(
					'post_type'   => 'sfwd-essays',
					'numberposts' => -1,
					// 'posts_per_page'	=>		$no_of_records,
					'orderby'     => 'date',
					'order'       => 'DESC',
					'post_status' => array( 'graded', 'not_graded' ),
					// 'offset'		    =>		12,
					'fields'      => 'ids',
				)
			);

			$essays = array();
			foreach ( $essay_ids as $essay_id ) {
				$essay_details = get_post_meta( $essay_id );

				if ( empty( $essay_details ) ) {
					continue;
				}

				// $quiz_pro_id = learndash_get_quiz_id_by_pro_quiz_id($essay_details['quiz_pro_id'][0]);
				// $post_author = intval(get_post_field('post_author', $quiz_pro_id));

				// if ($current_user_id !== $post_author) {
				// continue;
				// }

				$essay_course_id = $essay_details['course_id'][0];

				if ( ! in_array( $essay_course_id, $course_list ) ) {
					continue;
				}

				$question_title = get_the_title( $essay_id );
				$question_title = empty( $question_title ) ? '-' : $question_title;

				$course_title = get_the_title( $essay_course_id );
				$course_title = empty( $course_title ) ? '-' : $course_title;

				$lesson_title = $essay_details['lesson_title'][0];
				$lesson_title = empty( $lesson_title ) ? '-' : $lesson_title;

				$date = get_the_date( 'd M y, H:i', $essay_id );
				// $date = empty($date) ? '-':$lesson_title;

				$points = $this->getEssayPoints( $essay_id, $essay_details['question_id'][0] );
				$points = ( false === $points ) ? '-' : $points;

				$status = get_post_status( $essay_id );

				array_push(
					$essays,
					array(
						'title'  => $question_title,
						'course' => $course_title,
						'lesson' => $lesson_title,
						'date'   => $date,
						'points' => $points,
						'status' => ( 'graded' == $status ) ? __( 'Graded', 'wdm_instructor_role' ) : __( 'Not Graded', 'wdm_instructor_role' ),
						'link'   => add_query_arg(
							array(
								'post'   => $essay_id,
								'action' => 'edit',
							),
							admin_url( 'post.php' )
						),
						'type'   => __( 'Essay', 'wdm_instructor_role' ),
					)
				);
			}

			$submissions = array_merge( $assignments, $essays );

			return $submissions;
		}

		/**
		 * Add ellipses to long titles
		 *
		 * @param string $title
		 * @return string
		 */
		public function addEllipses( $title ) {
			if ( empty( $title ) ) {
				return $title;
			}
			$length = strlen( $title );

			if ( 15 > $length ) {
				return $title;
			}

			return substr( $title, 0, 12 ) . '...';
		}
	}
}
