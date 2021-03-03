<?php
/**
 * Multiple Instructors Module
 *
 * @since      3.5.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/modules/classes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Multiple_Instructors' ) ) {
	/**
	 * Class Instructor Role Multiple Instructors Module
	 */
	class Instructor_Role_Multiple_Instructors {


		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 3.3.0
		 */
		protected static $instance = null;

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
		 * Add share course metabox
		 *
		 * @since   3.2.x
		 */
		public function addCourseShareMetabox() {
			global $screen;
			add_meta_box(
				'ir-share-course-meta-box',
				__( sprintf( 'Share %s', \LearnDash_Custom_Label::get_label( 'course' ), 'wdm_instructor_role' )
				),
				array( $this, 'displayShareCourseMetabox' ),
				'sfwd-courses',
				'normal',
				'low'
			);
		}

		/**
		 * Display course sharing metabox
		 *
		 * @since   3.2.x
		 */
		public function displayShareCourseMetabox() {
			$course_id = get_the_ID();

			// Fetch list of all instructors except course author
			$exclude = array();
			if ( ! empty( $course_id ) ) {
				$course  = get_post( $course_id );
				$exclude = array( $course->post_author );
			}
			$all_instructors = ir_get_instructors( array( 'exclude' => $exclude ) );

			$shared_instructor_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
			$shared_instructor_ids  = explode( ',', $shared_instructor_list );

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/settings/ir-share-course-metabox.template.php',
				array(
					'course'                => $course,
					'all_instructors'       => $all_instructors,
					'shared_instructor_ids' => $shared_instructor_ids,
				)
			);
		}

		/**
		 * Enqueue course sharing scripts
		 *
		 * @since   3.2.x
		 */
		public function enqueueCourseSharingScripts() {
			 global $current_screen;

			// Return if not course edit screen.
			if ( 'sfwd-courses' !== $current_screen->id ) {
				return;
			}

			wp_enqueue_style( 'ir-select2-style', 'https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css' );
			wp_enqueue_script( 'ir-select2-script', 'https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js' );
			wp_enqueue_style(
				'ir-share-course-styles',
				plugins_url( 'css/ir-share-course-style.css', __DIR__ )
			);
			wp_enqueue_script(
				'ir-share-course-script',
				plugins_url( 'js/ir-share-course-script.js', __DIR__ ),
				array( 'jquery' )
			);
			wp_localize_script(
				'ir-share-course-script',
				'ir_loc',
				array(
					'placeholder' => __( 'Click for Search...', 'wdm_instructor_role' ),
				)
			);
		}

		/**
		 * Enable access to shared courses and content to co-instructors.
		 *
		 * @param array  $all_caps           List of all user capabilities.
		 * @param array  $requested_caps     List of requested capabilites.
		 * @param array  $args               Additional arguments.
		 * @param object $user              WP_User object of the user to provide access.
		 *
		 * @return array                    Updated list of all user capabilities.
		 */
		public function enableMultipleInstructors( $all_caps, $requested_caps, $args, $user ) {
			 global $post, $current_screen;

			$current_post = $post;

			// Check if course, lesson, topic or quiz page
			if ( empty( $current_post ) ) {
				// Check if rest request
				if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
					$current_post = $this->getPostFromRest();
				}
			}

			// Check for file uploads.
			if ( empty( $current_post ) ) {
				if ( in_array( 'edit_others_courses', $requested_caps ) && array_key_exists( '_wpnonce', $_POST ) && wp_verify_nonce( $_POST['_wpnonce'], 'media-form' ) ) {
					// Get attachment post
					$current_post = get_post( intval( $_POST['post_id'] ) );
				}
			}

			// Check arguments for 'current_user_can' function callback,
			if ( empty( $current_post ) ) {
				if ( 'edit_post' === $args[0] ) {
					$current_post = get_post( intval( $args[2] ) );
				}
			}

			if ( empty( $current_post ) && ! ( $current_post instanceof \WP_Post ) ) {
				return $all_caps;
			}

			$sfwd_post_types = array(
				'sfwd-courses',
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-quiz',
				'sfwd-question',
				'sfwd-assignment',
			);

			if ( ! in_array( $current_post->post_type, $sfwd_post_types ) ) {
				return $all_caps;
			}

			// Check if logged in and instructor.
			if ( empty( $user ) || ! wdm_is_instructor( $user->ID ) ) {
				return $all_caps;
			}

			$current_user_id = get_current_user_id();

			$course_id = $current_post->ID;

			if ( 'sfwd-courses' != $current_post->post_type ) {
				// If shared steps enabled, then get original course id
				if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
					$course_id = $this->getCourseIdFromSteps( $current_post->ID, $current_post->post_type, 1 );
				}

				// If still empty or same as current post, then try this
				if ( empty( $course_id ) || $current_post->ID == $course_id ) {
					$course_id = learndash_get_course_id( $current_post->ID );
				}
			}

			if ( in_array( 'edit_others_courses', $requested_caps ) || in_array( 'edit_courses', $requested_caps ) ) {
				if ( $this->isSharedCourse( $course_id ) || ( ! empty( $current_screen ) && 'edit-sfwd-courses' === $current_screen->id ) || $this->isSharedCourseQuestion( $current_post ) ) {
					// Provide capability to edit shared course.
					$all_caps['edit_others_courses'] = 1;

					/**
					 * Allow 3rd party extensions to update instructor caps for shared courses.
					 *
					 * @since 3.3.0
					 */
					$all_caps = apply_filters( 'ir_filter_instructor_course_sharing_caps', $all_caps, $course_id, $current_post );
				}
			}
			return $all_caps;
		}

		/**
		 * Check if a course is shared with an instructor
		 *
		 * @param int $course_id        ID of the course.
		 * @param int $instructor_id    ID of the instructor to check for share access.
		 *
		 * @return boolean          True if this is a shared course, false otherwise.
		 */
		public function isSharedCourse( $course_id, $instructor_id = 0 ) {
			$shared = false;

			if ( empty( $course_id ) ) {
				return $shared;
			}

			if ( empty( $instructor_id ) ) {
				$instructor_id = get_current_user_id();
			}

			// Fetch shared instructor list
			$shared_instructors_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
			$shared_instructors      = explode( ',', $shared_instructors_list );

			if ( ! empty( $shared_instructors ) && in_array( $instructor_id, $shared_instructors ) ) {
				$shared = true;
			}

			// If current instructor is course author then allow access to shared instructors.
			$course = get_post( $course_id );
			if ( $instructor_id == $course->post_author ) {
				$shared = true;
			}

			return apply_filters( 'ir_filter_is_shared_course', $shared, $course_id );
		}

		/**
		 * Save list of instructors with share access in course and instructor <meta class="">
		 *
		 * @param int       $course_id  ID of the course.
		 * @param WP_Object $course     Course object.
		 */
		public function saveSharedInstructorMeta( $course_id, $course ) {
			if ( empty( $course_id ) ) {
				return;
			}

			$current_user_id = get_current_user_id();

			// If current user is not course author or administrator, then return.
			if ( ( $course->post_author != $current_user_id ) && ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Remove previously set shared course meta.
			$this->deleteSharedInstructorMeta( $course_id );

			// Check if course sharing meta set.
			if ( ! array_key_exists( 'shared_instructors', $_POST ) ) {
				return;
			}

			$shared_instructor_ids = $_POST['shared_instructors'];

			// Save list of co-instructors in course meta.
			$shared_instructor_list = implode( ',', $shared_instructor_ids );
			update_post_meta( $course_id, 'ir_shared_instructor_ids', $shared_instructor_list );

			// Save course id in co-instructor's usermeta.
			foreach ( $shared_instructor_ids as $co_instructor_id ) {
				$shared_courses = $this->getInstructorSharedCourses( $co_instructor_id );
				if ( in_array( $course_id, $shared_courses ) ) {
					continue;
				}
				array_push( $shared_courses, $course_id );
				$this->saveInstructorSharedCourses( $co_instructor_id, $shared_courses );
			}
		}

		/**
		 * Delete all shared course meta saved for this course from course and instructor meta.
		 *
		 * @param int $course_id    ID of the course.
		 */
		public function deleteSharedInstructorMeta( $course_id ) {
			if ( empty( $course_id ) ) {
				return;
			}

			// Get the instructors with shared course access.
			$shared_instructors_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );

			$shared_instructors = explode( ',', $shared_instructors_list );

			// Remove shared course access from instructor meta.
			foreach ( $shared_instructors as $co_instructor_id ) {
				$this->removeInstructorSharedCourses( $co_instructor_id, $course_id );
			}

			// Remove shared course access from course meta.
			delete_post_meta( $course_id, 'ir_shared_instructor_ids' );
		}

		/**
		 * Filter course list to include shared courses for instructors
		 *
		 * @param string $where_sql     SQL query executed to fetch course listing results.
		 *
		 * @return string               Updatd SQL to fetch course listing results.
		 */
		public function filterInstructorCourseList( $where_sql ) {
			global $post_type, $wpdb, $current_screen;

			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $where_sql;
			}

			// Check if dashboard.
			if ( ! is_admin() ) {
				return $where_sql;
			}

			$sfwd_post_types = array(
				'sfwd-courses',
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-quiz',
				'sfwd-question',
			);

			$sfwd_post_type_screens = array(
				'edit-sfwd-courses',
				'edit-sfwd-lessons',
				'edit-sfwd-topic',
				'edit-sfwd-quiz',
				'edit-sfwd-question',
			);

			// Check if not trashed.
			if ( array_key_exists( 'post_status', $_GET ) && 'trash' == $_GET['post_status'] ) {
				return $where_sql;
			}

			// Check if course, lesson, topic, quiz or questions listing screen.
			if ( empty( $current_screen ) || ! in_array( $current_screen->id, $sfwd_post_type_screens ) ) {
				return $where_sql;
			}

			// Check if valid post
			if ( empty( $post_type ) || ! in_array( $post_type, $sfwd_post_types ) ) {
				return $where_sql;
			}

			// Add shared courses query filter check
			$current_instructor_id = get_current_user_id();
			$shared_courses        = $this->getSharedCourseContents( $current_instructor_id, $post_type );
			$shared_courses_list   = implode( ',', $shared_courses );

			// Update query with shared course results.
			if ( ! empty( $shared_courses ) ) {
				$where_sql .= " OR ( {$wpdb->prefix}posts.ID IN ($shared_courses_list) )";
			}

			return $where_sql;
		}

		/**
		 * Get list of shared course contents for an instructor.
		 *
		 * @param int    $instructor_id    ID of the instructor.
		 * @param string $post_type        Type of course content to fetch.(eg. lesson, topic, quiz, course or question).
		 *
		 * @return array                   List of courses shared with this instructor, else empty array.
		 */
		public function getSharedCourseContents( $instructor_id, $post_type ) {
			 global $wpdb;
			$shared_course_contents_list = array();

			// Check if instructor.
			if ( empty( $instructor_id ) ) {
				$instructor_id = get_current_user_id();
			}

			$table = $wpdb->prefix . 'postmeta';
			$sql   = $wpdb->prepare(
				"SELECT post_id FROM $table WHERE meta_key = 'ir_shared_instructor_ids' AND FIND_IN_SET (%d, meta_value)",
				$instructor_id
			);

			$shared_course_list          = $wpdb->get_col( $sql );
			$shared_course_contents_list = $shared_course_list;

			// Check what to return
			if ( 'sfwd-courses' != $post_type ) {
				switch ( $post_type ) {
					case 'sfwd-lessons':
						$shared_course_contents_list = $this->getSharedCourseLessons( $shared_course_list );
						break;
					case 'sfwd-topic':
						$shared_course_contents_list = $this->getSharedCourseTopics( $shared_course_list );
						break;
					case 'sfwd-quiz':
						$shared_course_contents_list = $this->getSharedCourseQuizzes( $shared_course_list );
						break;
					case 'sfwd-question':
						$shared_course_contents_list = $this->getSharedCourseQuestions( $shared_course_list );
						break;
				}
			}

			return $shared_course_contents_list;
		}

		/**
		 * Allow access to shared courses to other instructors.
		 *
		 * @param WP_Query $query   WP Query formed to fetch all course details.
		 *
		 * @return WP_Query         Updated WP Query data to fetch all course details.
		 */
		public function allowCourseAccessToInstructors( $query ) {
			// Check if instructor
			if ( ! wdm_is_instructor() ) {
				return $query;
			}

			global $post;
			$instructor_id = get_current_user_id();

			// Check if course edit page
			if ( empty( $post ) || 'sfwd-courses' != $post->post_type ) {
				return $query;
			}

			// Check if shared course.
			if ( ! $this->isSharedCourse( $post->ID ) ) {
				return $query;
			}

			// Allowing course access to instructors by removing author filter.
			$query->set( 'author__in', array() );

			return $query;
		}

		/**
		 * Allow access to lessons, topics, quiz and question under shared courses to other instructors
		 *
		 * @param WP_Query $query   WP Query formed to fetch all course details.
		 *
		 * @return WP_Query         Updated WP Query data to fetch all course details.
		 */
		public function allowCourseContentAccessToInstructors( $query ) {
			// Check if instructor
			if ( ! wdm_is_instructor() ) {
				return $query;
			}

			global $post;
			$instructor_id = get_current_user_id();

			$content_types = array(
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-quiz',
				'sfwd-question',
			);

			// Check if lesson, topic or quiz edit page
			if ( empty( $post ) || ! in_array( $post->post_type, $content_types ) ) {
				return $query;
			}

			// Get associated course ID.
			$course_id = learndash_get_course_id( $post->ID );

			if ( empty( $course_id ) && 'sfwd-question' != $post->post_type ) {
				return $query;
			}

			if ( 'sfwd-question' == $post->post_type ) {
				// Get quiz id
				$quiz_id = learndash_get_quiz_id( $question->ID );

				// If no quiz id then return.
				if ( empty( $quiz_id ) ) {
					return $query;
				}

				// Get associated course ID.
				$course_id = learndash_get_course_id( $quiz_id );
			}

			$course = get_post( $course_id );

			// Check if course
			if ( empty( $course ) || 'sfwd-courses' !== $course->post_type ) {
				return $query;
			}

			if ( ! $this->isSharedCourse( $course->ID ) ) {
				return $query;
			}

			// Allowing course access to instructors by removing author filter.
			$query->set( 'author__in', array( $instructor_id, $course->post_author ) );

			return $query;
		}

		/**
		 * Allow access to quiz questions of shared courses to other instructors.
		 *
		 * @param boolean $allow_access     Access to edit current quiz questions.
		 * @param int     $post_id              ID of the post page.
		 *
		 * @return boolean                  Updated access to edit current quiz questions.
		 */
		public function allowQuestionAccessToInstructors( $allow_access, $post_id ) {
			// Get associated course ID.
			$course_id = learndash_get_course_id( $post_id );

			if ( empty( $course_id ) ) {
				return $allow_access;
			}

			$course = get_post( $course_id );

			// Check if course
			if ( empty( $course ) || 'sfwd-courses' !== $course->post_type ) {
				return $allow_access;
			}

			$instructor_id = get_current_user_id();
			// Check if shared course and current user is not author of course (Since he would already have access then).
			if ( ! $this->isSharedCourse( $course->ID ) && $instructor_id !== $course->post_author ) {
				return $allow_access;
			}

			$allow_access = true;

			return $allow_access;
		}

		/**
		 * Get post details from a REST request
		 *
		 * @return WP_Post      Post object if found, else empty string.
		 */
		public function getPostFromRest() {
			 global $wp;

			$post = '';

			$route    = $wp->request;
			$api_root = rest_url();

			$route_params = array();

			if ( get_option( 'permalink_structure' ) && 0 === strpos( site_url() . '/' . $route, $api_root ) ) {
				$route_params = explode( '/', $route );
			}

			if ( empty( $route_params ) ) {
				return $post;
			}

			$sfwd_post_types = array(
				'sfwd-courses',
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-quiz',
				'sfwd-questions',
			);
			$sfwd_post_type  = '';

			foreach ( $sfwd_post_types as $post_type ) {
				if ( in_array( $post_type, $route_params ) ) {
					$sfwd_post_type = $post_type;
				}
			}

			if ( empty( $sfwd_post_type ) ) {
				return $post;
			}

			$post_id_key = intval( array_search( $sfwd_post_type, $route_params ) ) + 1;

			if ( ! array_key_exists( $post_id_key, $route_params ) ) {
				return $post;
			}

			$post_id = intval( $route_params[ $post_id_key ] );

			if ( 'sfwd-courses' != $sfwd_post_type && 'sfwd-questions' != $sfwd_post_type ) {
				$post_id = learndash_get_course_id( $post_id );
			}

			// if empty course and not a question then return.
			if ( empty( $post_id ) && 'sfwd-questions' != $sfwd_post_type ) {
				// Check for shared steps
				if ( ! \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
					return $post;
				}
				$post_id = $this->getCourseIdFromSteps( intval( $route_params[ $post_id_key ] ), $sfwd_post_type );
			}

			// Final check for post id
			if ( empty( $post_id ) ) {
				return $post;
			}

			// If a question, then fetch course id properly.
			if ( 'sfwd-questions' == $sfwd_post_type ) {
				// Get quiz id
				$quiz_id = learndash_get_quiz_id( $post_id );

				// If no quiz id then return.
				if ( empty( $quiz_id ) ) {
					return $post;
				}

				// Get associated course ID.
				$post_id = learndash_get_course_id( $quiz_id );
			}

			// Return course object.
			return get_post( $post_id );
		}

		/**
		 * Check if the question is part of the shared course
		 *
		 * @param object $question      Question to check if part of course.
		 *
		 * @return boolean              True if part of shared course, false otherwise.
		 */
		public function isSharedCourseQuestion( $question ) {
			if ( empty( $question ) || 'sfwd-question' != $question->post_type ) {
				return false;
			}

			$quiz_id = filter_input( INPUT_GET, 'quiz_id', FILTER_VALIDATE_INT );

			if ( empty( $quiz_id ) ) {
				$quiz_id = learndash_get_quiz_id( $question->ID );

				if ( empty( $quiz_id ) ) {
					return false;
				}
			}

			$course_id = learndash_get_course_id( $quiz_id );

			if ( empty( $course_id ) ) {
				return false;
			}

			if ( $this->isSharedCourse( $course_id ) ) {
				return true;
			}

			return apply_filters( 'ir_filter_is_shared_question', false, $course_id );
		}

		/**
		 * Get shared Course Lessons
		 *
		 * @param array $shared_course_list     List of courses to fetch Lessons from.
		 *
		 * @return array                        List of Lessons.
		 */
		public function getSharedCourseLessons( $shared_course_list ) {
			 $owner_shared_lessons = array();

			// Get owner shared lessons.
			if ( ! empty( $shared_course_list ) ) {
				$owner_shared_lessons = $this->getOwnerSharedContent( $shared_course_list, 'sfwd-lessons' );
			}

			// Get co-instructor shared lessons.
			$co_instructor_shared_lessons = $this->getCoInstructorSharedContent( 'sfwd-lessons' );

			$shared_lessons = array_unique( array_merge( $owner_shared_lessons, $co_instructor_shared_lessons ) );

			return $shared_lessons;
		}

		/**
		 * Get shared Course Topics
		 *
		 * @param array $shared_course_list     List of courses to fetch Topics from.
		 *
		 * @return array                        List of Topics.
		 */
		public function getSharedCourseTopics( $shared_course_list ) {
			global $wpdb;

			$table1        = $wpdb->prefix . 'posts';
			$table2        = $wpdb->prefix . 'postmeta';
			$shared_topics = array();

			// Get owner shared topics.
			if ( ! empty( $shared_course_list ) ) {
				$owner_shared_topics = $this->getOwnerSharedContent( $shared_course_list, 'sfwd-topic' );
			}

			// Get co-instructor shared topics.
			$co_instructor_shared_topics = $this->getCoInstructorSharedContent( 'sfwd-topic' );

			$shared_topics = array_unique( array_merge( $owner_shared_topics, $co_instructor_shared_topics ) );

			return $shared_topics;
		}

		/**
		 * Get shared Course Quizzes
		 *
		 * @param array $shared_course_list     List of courses to fetch Quizzes from.
		 *
		 * @return array                        List of Quizzes.
		 */
		public function getSharedCourseQuizzes( $shared_course_list ) {
			 global $wpdb;

			$table1         = $wpdb->prefix . 'posts';
			$table2         = $wpdb->prefix . 'postmeta';
			$shared_quizzes = array();

			// Get owner shared quizzes.
			if ( ! empty( $shared_course_list ) ) {
				$owner_shared_quizzes = $this->getOwnerSharedContent( $shared_course_list, 'sfwd-quiz' );
			}

			// Get co-instructor shared quizzes.
			$co_instructor_shared_quizzes = $this->getCoInstructorSharedContent( 'sfwd-quiz' );

			$shared_quizzes = array_unique( array_merge( $owner_shared_quizzes, $co_instructor_shared_quizzes ) );

			return $shared_quizzes;
		}

		/**
		 * Get shared Course Questions
		 *
		 * @param array $shared_course_list     List of courses to fetch Questions from.
		 *
		 * @return array                        List of Questions.
		 */
		public function getSharedCourseQuestions( $shared_course_list ) {
			global $wpdb;

			$table1           = $wpdb->prefix . 'posts';
			$table2           = $wpdb->prefix . 'postmeta';
			$shared_questions = array();

			// Get owner shared questions.
			if ( ! empty( $shared_course_list ) ) {
				$shared_questions = $this->getSharedQuestions( $shared_course_list );
			}

			return $shared_questions;
		}

		/**
		 * Get list of all courses shared with an instructor.
		 *
		 * @param int $instructor_id   ID of the Instructor.
		 *
		 * @return array                     List of courses shared with the instructor.
		 */
		public function getInstructorSharedCourses( $instructor_id ) {
			$shared_courses = array();

			if ( empty( $instructor_id ) ) {
				return $shared_courses;
			}

			$shared_courses_list = get_user_meta( $instructor_id, 'ir_shared_courses', 1 );

			if ( empty( $shared_courses_list ) ) {
				return $shared_courses;
			}

			$shared_courses = explode( ',', $shared_courses_list );

			return $shared_courses;
		}

		/**
		 * Save list of courses shared with an instructor.
		 *
		 * @param int   $instructor_id      ID of the instructor.
		 * @param array $shared_courses     List of courses shared with the instructor.
		 *
		 * @return int|boolean              Meta ID if the key didn't exist, true on successful update, false on failure.
		 */
		public function saveInstructorSharedCourses( $instructor_id, $shared_courses ) {
			if ( empty( $instructor_id ) ) {
				return false;
			}
			$shared_course_list = '';
			$shared_course_list = implode( ',', $shared_courses );

			return update_user_meta( $instructor_id, 'ir_shared_courses', $shared_course_list );
		}

		/**
		 * Remove a course from the list of courses shared with an instructor.
		 *
		 * @param int $instructor_id    ID of the instructor.
		 * @param int $course_id        ID of the course.
		 *
		 * @return boolean              True if successfully removed shared access for the course, false otherwise.
		 */
		public function removeInstructorSharedCourses( $instructor_id, $course_id ) {
			if ( empty( $instructor_id ) ) {
				return false;
			}

			// Get shared course list for the instructor.
			$instructor_shared_courses = $this->getInstructorSharedCourses( $instructor_id );

			// Check if current course is shared and get its key index.
			$key = array_search( $course_id, $instructor_shared_courses );

			// Remove the course id from the list.
			if ( false === $key ) {
				return false;
			}

			// Remove course id from instructor course share list.
			unset( $instructor_shared_courses[ $key ] );

			// Update instructor shared course meta.
			$shared_course_list = implode( ',', $instructor_shared_courses );
			update_user_meta( $instructor_id, 'ir_shared_courses', $shared_course_list );

			return true;
		}

		/**
		 * Get owned shared contents
		 *
		 * @param array  $shared_course_list     List of shared courses
		 * @param string $post_type             Type of the post
		 * @return array                        Array of owner shared course contents
		 */
		public function getOwnerSharedContent( $shared_course_list, $post_type ) {
			global $wpdb;

			$table1          = $wpdb->prefix . 'posts';
			$table2          = $wpdb->prefix . 'postmeta';
			$shared_contents = array();

			foreach ( $shared_course_list as $course_id ) {
				// If shared steps enabled, fetch proper details
				if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
					$course_contents = $this->getSharedStepContents( $course_id, $post_type );
					$shared_contents = array_merge( $shared_contents, $course_contents );

					continue;
				}

				// Since shared steps disabled, fetch course details the normal way.
				$sql = $wpdb->prepare(
					"SELECT $table1.ID from $table1 INNER JOIN $table2 ON $table1.ID = $table2.post_id 
                    WHERE $table2.meta_key = %s AND $table2.meta_value = %d AND $table1.post_type = %s",
					'course_id',
					$course_id,
					$post_type
				);

				$results = $wpdb->get_col( $sql, 0 );
				if ( ! empty( $results ) ) {
					$shared_contents = array_merge( $shared_contents, $results );
				}
			}

			return $shared_contents;
		}

		/**
		 * Get co-instructor shared course contents
		 *
		 * @param string $post_type     Type of the post
		 * @param int    $instructor_id    User ID of the instructor
		 *
		 * @return array                Array of co-instructor shared course contents.
		 */
		public function getCoInstructorSharedContent( $post_type, $instructor_id = 0 ) {
			$shared_content = array();

			if ( empty( $instructor_id ) ) {
				$instructor_id = get_current_user_id();
			}

			// Check if instructor ID.
			if ( ! wdm_is_instructor( $instructor_id ) ) {
				return $shared_content;
			}

			// Get all courses shared by this instructor.
			global $wpdb;
			$table1 = $wpdb->prefix . 'posts';
			$table2 = $wpdb->prefix . 'postmeta';

			$sql = $wpdb->prepare(
				"SELECT $table1.ID from $table1 INNER JOIN $table2 ON $table1.ID = $table2.post_id WHERE $table2.meta_key = %s AND $table1.post_type = %s AND $table1.post_author = %d",
				'ir_shared_instructor_ids',
				'sfwd-courses',
				$instructor_id
			);

			$course_ids = $wpdb->get_col( $sql, 0 );
			// $instructor_ids = $wpdb->get_col($sql, 1);

			if ( empty( $course_ids ) ) {
				return $shared_content;
			}

			foreach ( $course_ids as $key => $course_id ) {
				$shared_course_instructors = array();

				// $shared_course_instructors = explode(',', $instructor_ids[$key]);
				// if (! in_array($instructor_id, $shared_course_instructors)) {
				// continue;
				// }

				$sql = $wpdb->prepare(
					"SELECT $table1.ID from $table1 INNER JOIN $table2 ON $table1.ID = $table2.post_id WHERE $table2.meta_key = %s AND $table2.meta_value = %d AND $table1.post_type = %s",
					'course_id',
					$course_id,
					$post_type
				);

				$contents = $wpdb->get_col( $sql, 0 );
				if ( ! empty( $contents ) ) {
					$shared_content = array_merge( $shared_content, $contents );
				}
			}

			return $shared_content;
		}

		/**
		 * Get shared questions list
		 *
		 * @param array $shared_course_list     List of shared course contentt
		 * @return array                        Array of shared questions.
		 */
		public function getSharedQuestions( $shared_course_list ) {
			 global $wpdb;

			// Get owner shared questions
			$shared_quizzes = $this->getSharedCourseQuizzes( $shared_course_list );

			$table            = $wpdb->prefix . 'postmeta';
			$shared_questions = array();

			// Get all quiz questions
			$sql = $wpdb->prepare(
				"SELECT post_id, meta_value FROM $table WHERE meta_key = %s",
				'_sfwd-question'
			);

			$all_questions   = $wpdb->get_col( $sql, 0 );
			$related_quizzes = $wpdb->get_col( $sql, 1 );

			if ( empty( $all_questions ) ) {
				return $shared_questions;
			}

			// Find shared questions
			foreach ( $related_quizzes as $key => $related_quiz ) {
				$related_quiz_details = maybe_unserialize( $related_quiz );
				if ( in_array( $related_quiz_details['sfwd-question_quiz'], $shared_quizzes ) ) {
					array_push( $shared_questions, $all_questions[ $key ] );
				}
			}

			return $shared_questions;
		}

		/**
		 * Clear dirty quiz flag set by LD to reset questions, since we allow course sharing we need to retain the updated quiz questions.
		 */
		public function clearLDQuizDirtyFlag( $post_id = 0, $post = null, $update = false ) {
			// Check if post id and object set
			if ( ! $post_id || empty( $post ) ) {
				return;
			}

			// Check if instructor
			if ( ! wdm_is_instructor() ) {
				return;
			}

			// Check if question edit
			if ( 'sfwd-question' != $post->post_type ) {
				return;
			}

			// Check if shared question
			// Get quiz id
			$quiz_id = learndash_get_quiz_id( $post_id );

			// If no quiz id then return.
			if ( empty( $quiz_id ) ) {
				return;
			}

			// Get associated course ID.
			$course_id = learndash_get_course_id( $quiz_id );

			if ( empty( $course_id ) ) {
				return;
			}

			// Check if shared course
			if ( ! $this->isSharedCourse( $course_id ) ) {
				return;
			}

			// Clear dirty flag
			delete_post_meta( $quiz_id, 'ld_quiz_questions_dirty' );
		}

		/**
		 * Get course ID from in case shared steps are enabled
		 *
		 * @since 3.3.0
		 *
		 * @param int    $post_id               ID of the LD post.
		 * @param string $post_type             Post type of the content.
		 * @param bool   $check_co_instructor   Whether to check if current instructor is co-instructor
		 *                                      for the course, default false.
		 *
		 * @return int                          ID of the course.
		 */
		public function getCourseIdFromSteps( $post_id, $post_type, $check_co_instructor = false ) {
			$course_id = false;

			if ( empty( $post_id ) || empty( $post_type ) ) {
				return false;
			}

			global $wpdb;

			$table = $wpdb->prefix . 'postmeta';

			$meta_value_pattern = '%' . $post_type . ':' . $post_id . '%';
			$sql                = $wpdb->remove_placeholder_escape(
				$wpdb->prepare(
					"SELECT post_id from $table where meta_key = %s AND meta_value like %s",
					'ld_course_steps',
					$meta_value_pattern
				)
			);

			$results = $wpdb->get_col( $sql );

			if ( ! empty( $results ) ) {
				$course_id = $results[0];
				// Check if current instructor is co-instructor for some course in the results
				if ( $check_co_instructor ) {
					foreach ( $results as $c_id ) {
						// If not shared course, then continue
						if ( ! $this->isSharedCourse( $c_id ) ) {
							continue;
						}
						// If shared course, then set as course id to be returned.
						$course_id = $c_id;
					}
				}
			}

			return $course_id;
		}

		/**
		 * Get shared steps contents
		 *
		 * @param int    $course_id        ID of the course
		 * @param string $post_type     Type of the post
		 *
		 * @return array                Shared steps contents.
		 */
		public function getSharedStepContents( $course_id, $post_type ) {
			$course_contents = array();

			// If empty return
			if ( empty( $course_id ) || empty( $post_type ) ) {
				return $course_contents;
			}

			$course_steps = maybe_unserialize( get_post_meta( $course_id, 'ld_course_steps', 1 ) );

			if ( empty( $course_steps ) ) {
				return $course_contents;
			}

			// Get type 't' filtered data from the course steps
			$course_contents = $course_steps['t'][ $post_type ];

			return $course_contents;
		}

		/**
		 * Filter instructor users on course edit page
		 *
		 * @since 3.4.0
		 *
		 * @param array  $args      List of arguments for the LD Binary selector
		 * @param string $class     Class of the LD binary selector
		 *
		 * @return array            Updated list of arguments for the LD Binary selector
		 */
		public function filter_instructor_users_for_course( $args, $class ) {
			if ( ! wdm_is_instructor() ) {
				return $args;
			}

			if ( 'Learndash_Binary_Selector_Course_Users' === $class ) {
				$instructor_courses = ir_get_instructor_complete_course_list();
				$user_list          = array();
				foreach ( $instructor_courses as $course_id ) {
					$course_users = ir_get_users_with_course_access( $course_id, array( 'direct' ) );
					$user_list    = array_merge( $course_users, $user_list );
				}
				if ( empty( $user_list ) ) {
					$user_list = array( 0 );
				}
				$args['included_ids'] = $user_list;
			}
			return $args;
		}

		/**
		 * Remove course step check while displaying any course content for instructors
		 *
		 * @param object $wp
		 * @since 3.3.5
		 */
		public function remove_course_step_check_for_instructors( $wp ) {
			// Return if not instructor
			if ( ! wdm_is_instructor() ) {
				return;
			}
			// Check if single page
			if ( ! is_single() ) {
				return;
			}

			global $post;
			// Check if LD post type and if nested permalinks enabled
			if ( ( in_array( $post->post_type, array( 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ) ) === true ) && ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_Permalinks', 'nested_urls' ) == 'yes' ) ) {
				remove_action( 'wp', 'learndash_check_course_step', 10 );
			}
		}

		/**
		 * Add instructor shared courses to associated course access setting sections
		 *
		 * @since   3.4.0
		 *
		 * @param array  $settings_fields   Array of settings for the course content.
		 * @param string $metabox_key       Metabox key for the settings section.
		 *
		 * @return array                    Updated settings array for the course content.
		 */
		public function filter_instructor_shared_course_contents( $settings_fields, $metabox_key ) {
			if ( ! wdm_is_instructor() ) {
				return $settings_fields;
			}

			$user_id        = get_current_user_id();
			$shared_courses = ir_get_instructor_shared_course_list( $user_id );

			// If no shared courses, then return
			if ( empty( $shared_courses ) ) {
				return $settings_fields;
			}

			// Add shared course details
			if ( 'learndash-lesson-access-settings' == $metabox_key || 'learndash-topic-access-settings' == $metabox_key || 'learndash-quiz-access-settings' == $metabox_key ) {
				foreach ( $shared_courses as $course_id ) {
					$settings_fields['course']['options'][ $course_id ] = get_the_title( $course_id );
				}
			}

			return $settings_fields;
		}
	}
}
