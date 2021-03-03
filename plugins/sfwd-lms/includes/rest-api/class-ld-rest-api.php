<?php
if ( ! defined( 'LEARNDASH_REST_API_NAMESPACE' ) ) {
	define( 'LEARNDASH_REST_API_NAMESPACE', 'ldlms' );
}

define( 'LEARNDASH_REST_API_DIR', dirname( __FILE__ ) );

require_once LEARNDASH_LMS_PLUGIN_DIR . 'includes/gutenberg/lib/class-ld-rest-gutenberg-posts-controller.php';

if ( ! class_exists( 'LearnDash_REST_API' ) ) {
	class LearnDash_REST_API {

		/**
		 * @var The reference to *Singleton* instance of this class
		 */
		private static $instance;

		private $controllers = array();

		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'rest_api_init' ), 10 );
		}

		/**
		 * Init function to all the LearnDash REST API namespace and endpoints.
		 */
		public function rest_api_init() {
			if ( self::enabled() ) {
				include_once dirname( __FILE__ ) . '/v1/class-ld-rest-posts-controller.php';
				include_once dirname( __FILE__ ) . '/v1/class-ld-rest-users-controller.php';

				// v1 controllers.
				$controllers_v1 = array(
					'LD_REST_Echo_Controller_V1'          => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v1/class-ld-rest-echo-controller.php',
					),
					'LD_REST_Courses_Controller_V1'       => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v1/class-ld-rest-courses-controller.php',
					),
					'LD_REST_Lessons_Controller_V1'       => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v1/class-ld-rest-lessons-controller.php',
					),
					'LD_REST_Topics_Controller_V1'        => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v1/class-ld-rest-topics-controller.php',
					),
					'LD_REST_Quizzes_Controller_V1'       => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v1/class-ld-rest-quizzes-controller.php',
					),
					'LD_REST_Groups_Controller_V1'        => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v1/class-ld-rest-groups-controller.php',
					),
					'LD_REST_Users_Groups_Controller_V1'  => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v1/class-ld-rest-users-groups-controller.php',
					),
					'LD_REST_Users_Courses_Controller_V1' => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v1/class-ld-rest-users-courses-controller.php',
					),
					'LD_REST_Questions_Controller_V1'     => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v1/class-ld-rest-questions-controller.php',
					),
					'LD_REST_Sections_Controller_V1'      => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v1/class-ld-rest-sections-controller.php',
					),
				);

				include_once dirname( __FILE__ ) . '/v2/class-ld-rest-posts-controller.php';
				include_once dirname( __FILE__ ) . '/v2/class-ld-rest-users-controller.php';

				$controllers_v2 = array(
					'LD_REST_Courses_Controller_V2'        => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-courses-controller.php',
					),
					'LD_REST_Lessons_Controller_V2'        => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-lessons-controller.php',
					),
					'LD_REST_Topics_Controller_V2'         => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-topics-controller.php',
					),
					'LD_REST_Quizzes_Controller_V2'        => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-quizzes-controller.php',
					),
					'LD_REST_Essays_Controller_V2'         => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-essays-controller.php',
					),
					'LD_REST_Questions_Controller_V2'      => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-questions-controller.php',
					),
					'LD_REST_Assignments_Controller_V2'    => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-assignments-controller.php',
					),
					'LD_REST_Groups_Controller_V2'         => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-groups-controller.php',
					),
					'LD_REST_Users_Courses_Controller_V2'  => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-users-courses-controller.php',
					),
					'LD_REST_Users_Groups_Controller_V2'   => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-users-groups-controller.php',
					),
					'LD_REST_Users_Course_Progress_Controller_V2' => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-users-course-progress-controller.php',
					),
					'LD_REST_Users_Quiz_Progress_Controller_V2' => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-users-quiz-progress-controller.php',
					),
					'LD_REST_Echo_Controller_V2'           => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-echo-controller.php',
					),
					'LD_REST_Price_Types_Controller_V2'    => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-price-types-controller.php',
					),
					'LD_REST_Question_Types_Controller_V2' => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-question-types-controller.php',
					),
					'LD_REST_Quiz_Statistics_Controller_V2' => array(
						'register_routes' => true,
						'file'            => LEARNDASH_REST_API_DIR . '/v2/class-ld-rest-quiz-statistics-controller.php',
					),
				);

				$this->controllers = array_merge( $controllers_v1, $controllers_v2 );

				/**
				 * Filters the list of REST API controllers.
				 *
				 * @param array $controllers An array of REST API controllers data.
				 */
				$this->controllers = apply_filters( 'learndash-rest-api-controllers', $this->controllers );
				if ( ! empty( $this->controllers ) ) {
					foreach ( $this->controllers as $controller_class => $set ) {

						if ( ( isset( $set['file'] ) ) && ( ! empty( $set['file'] ) ) && ( file_exists( $set['file'] ) ) ) {
							include_once $set['file'];

							if ( ( isset( $set['register_routes'] ) ) && ( true === $set['register_routes'] ) ) {
								$this->$controller_class = new $controller_class();
								$this->$controller_class->register_routes();
							}
						}
					}
				}
			}
		}

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @return The *Singleton* instance.
		 */
		public static function get_instance() {
			if ( null === static::$instance ) {
				static::$instance = new static();
			}

			return static::$instance;
		}

		/**
		 * Override class function for 'this'.
		 * This function handles out Singleton logic.
		 *
		 * @return reference to current instance
		 */
		public static function this() {
			return self::$instance;
		}

		/**
		 * Check if REST is enabled for post type.
		 *
		 * @param string $post_type Post Type slug to check.
		 *
		 * @return bool true is enable. Otherwise false.
		 */
		public static function enabled( $post_type = '' ) {
			$return = false;

			if ( ( defined( 'LEARNDASH_REST_API_ENABLED' ) ) && ( true === LEARNDASH_REST_API_ENABLED ) ) {
				if ( LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'enabled' ) === 'yes' ) {
					$return = true;
				}
			}

			/**
			 * Filters whether the LearnDash REST API is enabled or not.
			 *
			 * @param boolean $enabled   Whether the REST API is enabled or not.
			 * @param string  $post_type The slug of the post type to be checked.
			 */
			return apply_filters( 'learndash_rest_api_enabled', $return, $post_type );
		}

		/**
		 * Check if Gutenberg editor is enabled for post type.
		 *
		 * @param string $post_type Post Type slug to check.
		 *
		 * @return bool true is enable. Otherwise false.
		 */
		public static function gutenberg_enabled( $post_type = '' ) {
			$return = false;

			if ( ( defined( 'LEARNDASH_GUTENBERG' ) ) && ( LEARNDASH_GUTENBERG === true ) ) {
				$return = true;
			}

			/**
			 * Filters whether the Gutenberg editor is enabled for the plugin or not.
			 *
			 * @param boolean $enabled   Whether the Gutenberg editor is enabled or not.
			 * @param string  $post_type The slug of the post type to be checked.
			 */
			return apply_filters( 'learndash_gutenberg_enabled', $return, $post_type );
		}

		/**
		 * get REST controller for post type.
		 *
		 * @param string $post_type Post Type slug to check.
		 *
		 * @return string Class name.
		 */
		public static function get_controller( $post_type = '' ) {
			$rest_controller = '';

			if ( ! empty( $post_type ) ) {
				switch ( $post_type ) {
					case learndash_get_post_type_slug( 'course' ):
						if ( self::enabled( $post_type ) ) {
							$rest_controller = 'LD_REST_Courses_Controller_V1';
						} elseif ( self::gutenberg_enabled( $post_type ) ) {
							$rest_controller = 'LD_REST_Posts_Gutenberg_Controller';
						}
						break;

					case learndash_get_post_type_slug( 'lesson' ):
						if ( self::enabled( $post_type ) ) {
							$rest_controller = 'LD_REST_Lessons_Controller_V1';
						} elseif ( self::gutenberg_enabled( $post_type ) ) {
							$rest_controller = 'LD_REST_Posts_Gutenberg_Controller';
						}
						break;

					case learndash_get_post_type_slug( 'topic' ):
						if ( self::enabled( $post_type ) ) {
							$rest_controller = 'LD_REST_Topics_Controller_V1';
						} elseif ( self::gutenberg_enabled( $post_type ) ) {
							$rest_controller = 'LD_REST_Posts_Gutenberg_Controller';
						}
						break;

					case learndash_get_post_type_slug( 'quiz' ):
						if ( self::enabled( $post_type ) ) {
							$rest_controller = 'LD_REST_Quizzes_Controller_V1';
						} elseif ( self::gutenberg_enabled( $post_type ) ) {
							$rest_controller = 'LD_REST_Posts_Gutenberg_Controller';
						}
						break;

					case learndash_get_post_type_slug( 'group' ):
						if ( self::enabled( $post_type ) ) {
							$rest_controller = 'LD_REST_Groups_Controller_V1';
						} elseif ( self::gutenberg_enabled( $post_type ) ) {
							$rest_controller = 'LD_REST_Posts_Gutenberg_Controller';
						}
						break;

					default:
						break;
				}
			}
			return $rest_controller;
		}
	}
}
LearnDash_REST_API::get_instance();
