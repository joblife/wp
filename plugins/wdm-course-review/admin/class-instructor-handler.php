<?php
/**
 * This file is used to include the class which makes the plugin compatible with Instructor Role.
 *
 * @package RatingsReviewsFeedback\Admin
 */

namespace ns_wdm_ld_course_review{

	/**
	 * To make it compatible with Instructor Role.
	 */
	class Instructor_Handler {

		/**
		 * Instance of this class.
		 *
		 * @since    1.0.0
		 *
		 * @var object
		 */
		protected static $instance = null;
		/**
		 * Constructor for the class.
		 * Used to initialize all the hooks in the class.
		 */
		public function __construct() {

			// adding post types to allow instructor to view.
			\wdm_add_hook(
				'wdmir_set_post_types',
				'setting_post_type',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);

			// Allowing IR plugin to add menu.
			\wdm_add_hook(
				'wdmir_add_dash_tabs',
				'add_in_dashboard_menu',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);

			// excluding the post type from pre_get_posts hook.
			\wdm_add_hook(
				'wdmir_exclude_post_types',
				'exclude_post_types',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);
			// allowing author to view only his/her course review.
			\wdm_add_hook(
				'pre_get_posts',
				'remove_others_reviews',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);
		}
		/**
		 * Returns an instance of this class.
		 *
		 * @since     1.0.0
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
		/**
		 * Filtering reviews based on instructor course.
		 *
		 * @param  object $query [query].
		 * @return object $query [after adding meta query of course id]
		 */
		public function remove_others_reviews( $query ) {
			if ( $query->is_admin ) {
				$wdm_user_id = get_current_user_id();
				$allowed_post_type = array(
					'wdm_course_review_review_on_course' => 'wdm_course_review',
					'wdm_course_feedback_feedback_on_course' => 'wdm_course_feedback',
				);
				if ( ! function_exists( 'wdm_is_instructor' ) ) {
					return $query;
				}
				if ( wdm_is_instructor( $wdm_user_id ) && in_array( $query->query['post_type'], $allowed_post_type ) ) {
					$meta_key = array_search( $query->query['post_type'], $allowed_post_type ); // $key = 2;
					$all_course_ids = rrf_get_instructor_course_ids( $wdm_user_id );
					$query->query_vars['meta_query'][]    = array(
						'key'       => $meta_key,
						'value'     => $all_course_ids,
						'compare'   => 'IN',
					);
				}
			}
			return $query;
		}

		/**
		 * Excluding the rating,review and feedback CPT from pre_hook
		 *
		 * @param  array $wdmir_exclude_posts [array of post types].
		 * @return  array $wdmir_exclude_posts [array of post types]
		 */
		public function exclude_post_types( $wdmir_exclude_posts ) {
			if ( function_exists( 'wdm_is_instructor' ) && wdm_is_instructor() ) {
				array_push( $wdmir_exclude_posts, 'wdm_course_review' ); // CPT.
				array_push( $wdmir_exclude_posts, 'wdm_course_feedback' ); // CPT.
			}
				return $wdmir_exclude_posts;
		}
		/**
		 * Adding post types to allow instructor to view.
		 *
		 * @param  array $wdm_ar_post_types [allowed post type for instructor].
		 * @return array $wdm_ar_post_types [after adding new CPTs]
		 */
		public function setting_post_type( $wdm_ar_post_types ) {
			if ( function_exists( 'wdm_is_instructor' ) && wdm_is_instructor() ) {
				$wdm_ar_post_types[] = 'wdm_course_review';
				$wdm_ar_post_types[] = 'wdm_course_feedback';
			}
			return $wdm_ar_post_types;
		}

		/**
		 * Adding to IR dashboard menu.
		 *
		 * @param array $allowed_tabs [allowed dashboard menu].
		 * @return array $allowed_tabs [allowed dashboard menu]
		 */
		public function add_in_dashboard_menu( $allowed_tabs ) {

			if ( function_exists( 'wdm_is_instructor' ) && wdm_is_instructor() ) {
				array_push( $allowed_tabs, 'course reviews' ); // menu title (Remove after IR fixes).
				array_push( $allowed_tabs, 'course feedback' ); // menu title (Remove after IR fixes).

				array_push( $allowed_tabs, 'wdm_course_review' ); // menu slug.
				array_push( $allowed_tabs, 'wdm_course_feedback' ); // menu slug.
			}
			return $allowed_tabs;
		}
	}
	Instructor_Handler::get_instance();
}
