<?php
/**
 * This class contains the logic to load templates related to the reviews section.
 *
 * @version 2.0.0
 * @package RatingsReviewsFeedback\Public\Reviews
 */

namespace ns_wdm_ld_course_review {
	if ( ! class_exists( 'Review_Submission' ) ) {
		/**
		 * This class is used for loading the review template for course single page and the shortcode for the same.
		 */
		class Review_Submission {
			/**
			 * This property contains the singleton instance of the class.
			 *
			 * @var Class Object.
			 */
			protected static $instance = null;

			/**
			 * This property contains the steps registered for review submission flow.
			 *
			 * @var array
			 */
			protected $steps = array();

			/**
			 * This property contains the type of steps being loaded.
			 *
			 * @var string
			 */
			protected $step_type = '';// add or edit or delete.

			/**
			 * This method is used to add all action/filter hooks.
			 */
			public function __construct() {
				add_action( 'init', array( $this, 'initialize_default_modal_configuration' ), 0 );
				add_action( 'get_footer', array( $this, 'enqueue_review_submission_assets' ) );
				add_action( 'wp_loaded', array( $this, 'register_steps' ) );
				add_action( 'wp_ajax_launch_modal', array( $this, 'modal_data_dynamic' ) );
				add_action( 'wp_ajax_submit_review', array( $this, 'submit_reviews' ) );
				add_action( 'wp_ajax_delete_review', array( $this, 'delete_reviews' ) );
				// for sending email to author after review.
				add_action( 'wdm_student_rated_course_successfully', array( $this, 'rrf_send_review_email_to_author' ), 10, 3 );
			}

			/**
			 * This function is used to fetch the instance of this class.
			 *
			 * @return Object returns class instance.]
			 */
			public static function get_instance() {
				if ( is_null( self::$instance ) ) {
					self::$instance = new self();
				}
				return self::$instance;
			}

			/**
			 * This method is used to initialize the default configurations used for the modal windows.
			 */
			public function initialize_default_modal_configuration() {
				global $rrf_modal_settings;
				$rrf_modal_settings = array(
					'closeExisting' => false, // Don't set it to true as we will not be able to stack multiple modal instances otherwise.
					'escapeClose'   => false,      // Allows the user to close the modal by pressing `ESC`.
					'clickClose'    => false,       // Allows the user to close the modal by clicking the overlay.
					'closeText'     => 'Close',     // Text content for the close <a> tag.
					'closeClass'    => 'rrf-close-all',         // Add additional class(es) to the close <a> tag. Note: do not remove this class otherwise JS data flushing on modal close as well as multiple modal closing will not work.
					'showClose'     => true,        // Shows a (X) icon/link in the top-right corner.
					'modalClass'    => 'modal',    // CSS class added to the element being displayed in the modal.
					'blockerClass'  => 'blocker',  // CSS class added to the overlay (blocker).

				// HTML appended to the default spinner during AJAX requests.
					'spinnerHtml'   => '<div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div>',

					'showSpinner'   => true,      // Enable/disable the default spinner during AJAX requests.
					'fadeDuration'  => 1,     // Number of milliseconds the fade transition takes (null means no transition).
					'fadeDelay'     => 1.0,          // Point during the overlay's fade-in that the modal begins to fade in (.5 = 50%, 1.5 = 150%, etc.).
				);
				$rrf_modal_settings = apply_filters( 'rrf_default_modal_settings', $rrf_modal_settings );
			}

			/**
			 * This method is used to enqueue all the star rating display related assets.
			 *
			 * @SuppressWarnings(PHPMD.LongVariable)
			 */
			public function enqueue_review_submission_assets() {
				global $rrf_modal_settings;// $learndash_shortcode_used,.
				if ( ! is_singular( 'sfwd-courses' ) /*&& ! is_post_type_archive( 'sfwd-courses' ) && ! $learndash_shortcode_used*/ ) {
					return;
				}
				global $post;
				if ( ! rrf_can_user_post_reviews( get_current_user_id(), $post->ID ) ) {
					return;
				}
				rrf_load_star_rating_lib();
				rrf_load_jquery_modal_lib();
				if ( ! is_rtl() ) {
					wp_enqueue_style( 'reviews-submission-css', plugins_url( 'public/css/submit-reviews.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'public/css/submit-reviews.css' ) );
				} else {
					wp_enqueue_style( 'reviews-submission-css', plugins_url( 'public/css/rtl/submit-reviews.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'public/css/rtl/submit-reviews.css' ) );
				}
				wp_enqueue_script( 'reviews-submission-js', plugins_url( 'public/js/submit-reviews.js', RRF_PLUGIN_FILE ), array( 'jquery', 'star-rating-js' ), filemtime( RRF_PLUGIN_PATH . 'public/js/submit-reviews.js' ), true );
				wp_localize_script(
					'reviews-submission-js',
					'review_details',
					array(
						'url'                   => admin_url( 'admin-ajax.php' ),
						'settings'              => $rrf_modal_settings,
						'add_review_nonce'      => wp_create_nonce( 'add_review' ),
						'edit_review_nonce'     => wp_create_nonce( 'edit_review' ),
						'delete_review_nonce'   => wp_create_nonce( 'delete_review' ),
						'submit_review_nonce'   => wp_create_nonce( 'submit_review' ),
						'review_text'           => __( 'Your Rating', 'wdm_ld_course_review' ),
						'alt_text'              => __( 'Edit rating', 'wdm_ld_course_review' ),
						'maxlength'             => RRF_REVIEW_DETAILS_MAX_LENGTH,
					)
				);
			}

			/**
			 * This step is used to register the default step type and the steps for each of the step types.
			 */
			public function register_steps() {
				$this->step_type = 'add';
				$this->steps = apply_filters(
					'rrf_review_submission_steps',
					array(
						'add'       => array( 'rating-submission', 'review-details', 'review-completion' ),
						'edit'      => array( 'rating-preview', 'review-details', 'review-completion' ),
						'delete'    => array( 'delete-confirmation' ),
					)
				);
			}

			/**
			 * This method is used to search for the template name passed to it.
			 *
			 * @param  string $template_name Name of the template.
			 * @return string/WP_Error returns the template path or WP_Error if the template isn't found.
			 */
			public static function get_template( $template_name ) {
				$lookup_paths = apply_filters(
					'rrf_get_template',
					array(
						get_stylesheet_directory() . '/wdm-course-review/',
						get_template_directory() . '/wdm-course-review/',
						RRF_PLUGIN_PATH . 'public/templates/',
					)
				);
				foreach ( $lookup_paths as $path ) {
					if ( file_exists( $path . $template_name ) ) {
						return $path . $template_name;
					}
				}
				/* translators: %s : Name of the template file. */
				return new \WP_Error( 'file_not_found', sprintf( __( "Template file %s doesn't exist in any of the given locations.", 'wdm_ld_course_review' ), $template_name ) );
			}

			/**
			 * This method is used to implement a custom filter to remove quotes encoding messing up the character length.
			 *
			 * @version 2.0.1 [Implemented to fix quotes disturbing the character limit issue.]
			 * @param  string $string [Input String].
			 * @return string Decoded String
			 */
			public function decode_string( $string ) {
				return html_entity_decode( $string, ENT_QUOTES );
			}

			/**
			 * This method returns the requested content to be shown in review modals.
			 *
			 * @SuppressWarnings("unused")
			 */
			public function modal_data_dynamic() {
				$course_id          = filter_input( INPUT_POST, 'course_id', FILTER_VALIDATE_INT );
				$stars              = filter_input( INPUT_POST, 'stars', FILTER_VALIDATE_INT );
				$review_title       = filter_input( INPUT_POST, 'title', FILTER_CALLBACK, array( 'options' => array( $this, 'decode_string' ) ) );
				$review_description = filter_input( INPUT_POST, 'body', FILTER_CALLBACK, array( 'options' => array( $this, 'decode_string' ) ) );
				$step_type          = filter_input( INPUT_POST, 'step_type', FILTER_SANITIZE_STRING );
				if ( ! in_array( $step_type, array( 'add', 'edit', 'delete' ) ) ) {
					include_once self::get_template( 'invalid-template.php' );
					exit;
				}
				if ( ! check_ajax_referer( $step_type . '_review', 'security' ) ) {
					include_once self::get_template( 'invalid-template.php' );
					exit;
				}
				$this->step_type    = $step_type;
				$steps              = $this->steps;
				$step_number        = filter_input( INPUT_POST, 'step_no', FILTER_VALIDATE_INT );
				$step               = $this->steps[ $this->step_type ][ $step_number - 1 ];
				include_once self::get_template( $step . '.php' );
				exit;
			}

			/**
			 * This method is used to insert/update the review posted by a user for a course.
			 */
			public function submit_reviews() {
				$course_id          = filter_input( INPUT_POST, 'course_id', FILTER_VALIDATE_INT );
				$stars              = filter_input( INPUT_POST, 'stars', FILTER_VALIDATE_INT );
				$review_title       = filter_input( INPUT_POST, 'title', FILTER_CALLBACK, array( 'options' => array( $this, 'decode_string' ) ) );
				$review_description = filter_input( INPUT_POST, 'body', FILTER_CALLBACK, array( 'options' => array( $this, 'decode_string' ) ) );
				$security           = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_STRING );
				$user_id            = get_current_user_id();
				$this->validate_review_input( $course_id, $stars, $review_title, $review_description, $security );
				$review = rrf_get_user_course_review_id( $user_id, $course_id );
				$status = 'pending';
				if ( learndash_is_admin_user( $user_id ) ) {
					$status = 'publish';
				}
				$args = array(
					'post_author'   => $user_id,
					'post_content'  => $review_description,
					'post_status'   => $status,
					'post_title'    => $review_title,
					'post_type'     => 'wdm_course_review',
				);
				if ( ! empty( $review ) ) {
					$args['ID'] = $review->ID;
				}
				$args['comment_status'] = $this->get_comment_status();
				$review_id = wp_insert_post( $args, true );
				if ( is_wp_error( $review_id ) ) {
					wp_send_json_error( $review_id->get_error_message() );
				}
				update_post_meta( $review_id, 'wdm_course_review_review_on_course', $course_id );
				update_post_meta( $review_id, 'wdm_course_review_review_rating', $stars );
				do_action( 'wdm_student_rated_course_successfully', $course_id, $review_id, $user_id );
				wp_send_json_success();
			}

			/**
			 * This method is used to validate the input for the review posted by a user for a course.
			 *
			 * @SuppressWarnings(PHPMD.NPathComplexity)
			 * @param integer $course_id [Course ID].
			 * @param integer $stars [Number of Stars].
			 * @param string  $review_title [Review Title].
			 * @param string  $review_description [Review Description].
			 * @param string  $security [Nonce for review submission request].
			 */
			private function validate_review_input( $course_id, $stars, $review_title, $review_description, $security ) {
				$user_id            = get_current_user_id();
				if ( ! wp_verify_nonce( $security, 'submit_review' ) ) {
					wp_send_json_error( __( 'Unauthorized request. Failed to verify nonce.', 'wdm_ld_course_review' ) );
				}
				if ( empty( $course_id ) ) {
					wp_send_json_error( __( 'Something went wrong. Could not find the target course for this review.', 'wdm_ld_course_review' ) );
				}
				if ( 0 === $user_id || ! rrf_can_user_post_reviews( $user_id, $course_id ) ) {
					wp_send_json_error( __( 'You have not met the qualification criteria for rating this course.', 'wdm_ld_course_review' ) );
				}
				if ( empty( $review_title ) ) {
					wp_send_json_error( __( 'Review Headline is a required field. Please go back to the previous step and add a headline for this review.', 'wdm_ld_course_review' ) );
				}
				if ( empty( $review_description ) ) {
					wp_send_json_error( __( 'Review Description is a required field. Please go back to the previous step and add a description for this review.', 'wdm_ld_course_review' ) );
				}
				if ( mb_strlen( $review_title ) > RRF_REVIEW_HEADLINE_MAX_LENGTH ) {
					/* translators: %d : Review Headline max length. */
					wp_send_json_error( sprintf( __( 'Review Headline cannot be greater than %d.', 'wdm_ld_course_review' ), RRF_REVIEW_HEADLINE_MAX_LENGTH ) );
				}
				if ( mb_strlen( $review_description ) > RRF_REVIEW_DETAILS_MAX_LENGTH ) {
					/* translators: %d : Review description max length. */
					wp_send_json_error( sprintf( __( 'Review Details cannot be greater than %d.', 'wdm_ld_course_review' ), RRF_REVIEW_DETAILS_MAX_LENGTH ) );
				}
				if ( empty( $stars ) ) {
					wp_send_json_error( __( 'You cannot rate 0 stars. Please go back to the previous step and select a minimum of 1 star rating.', 'wdm_ld_course_review' ) );
				}
			}

			/**
			 * This is an AJAX callback to delete the current User Review.
			 */
			public function delete_reviews() {
				$course_id          = filter_input( INPUT_POST, 'course_id', FILTER_VALIDATE_INT );
				$security           = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_STRING );
				$user_id            = get_current_user_id();
				if ( ! wp_verify_nonce( $security, 'delete_review' ) ) {
					wp_send_json_error( __( 'Unauthorized request. Failed to verify nonce.', 'wdm_ld_course_review' ) );
				}
				if ( empty( $course_id ) ) {
					wp_send_json_error( __( 'Something went wrong. Could not find the target course for this review.', 'wdm_ld_course_review' ) );
				}
				if ( 0 === $user_id || ! rrf_can_user_post_reviews( $user_id, $course_id ) ) {
					wp_send_json_error( __( 'You do not have qualification criteria for deleting this review.', 'wdm_ld_course_review' ) );
				}
				$review = rrf_get_user_course_review_id( $user_id, $course_id );
				if ( empty( $review ) ) {
					wp_send_json_error( __( 'No Review Found.', 'wdm_ld_course_review' ) );
				}
				$status = wp_delete_post( $review->ID, true );
				if ( is_null( $status ) || false === $status ) {
					wp_send_json_error( __( 'Failed to delete this review. Please try again later.', 'wdm_ld_course_review' ) );
				}
				wp_send_json_success();
			}

			/**
			 * This method checks whether comment on reviews is enabled.
			 *
			 * @return string $status open/close based on whether the reviews can be commented upon.
			 */
			private function get_comment_status() {
				$setting = get_option( 'wdm_course_review_setting', 1 );
				if ( $setting ) {
					return 'open';
				}
				return 'closed';
			}

			/**
			 * Sending mail to author on review submission.
			 *
			 * @param int $course_id   [course id].
			 * @param int $result [review id].
			 * @param int $user_id     [user id].
			 */
			public function rrf_send_review_email_to_author( $course_id, $result, $user_id ) {
				$is_email_enabled = get_option( 'wdm_send_email_after_review', 1 );
				if ( empty( $is_email_enabled ) ) {
					return;
				}
				// Course details.
				$course = get_post( $course_id );
				if ( ! $course ) {
					return;
				}
				$course_permalink = get_post_permalink( $course );
				$course_link = '<a href="' . $course_permalink . '">' . $course->post_title . '</a>';
				// Author and User details.
				$author_id = $course->post_author;
				$author_obj = get_userdata( $author_id );
				$user_info = get_userdata( $user_id );
				// Review details.
				$review = get_post( $result['review_id'] );
				if ( ! $review ) {
					return;
				}
				$review_permalink = get_edit_post_link( $result['review_id'] );
				$post_type_object = get_post_type_object( $review->post_type );
				if ( ! $post_type_object ) {
					return;
				}
				if ( $post_type_object->_edit_link ) {
					$review_permalink = admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $result['review_id'] ) );
				}
				$review_link = '';
				if ( user_can( $author_obj->ID, 'edit_others_wdm_course_reviews' ) ) {
					$review_link = '<a href="' . $review_permalink . '">' . __( 'review', 'wdm_ld_course_review' ) . '</a>';
				}
				$find = array(
					// user shortcode.
					'[user_first_name]',
					'[user_last_name]',
					'[user_display_name]',
					'[user_email_id]',
					'[user_id]',
					// author shortcode.
					'[author_first_name]',
					'[author_last_name]',
					'[author_display_name]',
					'[author_email_id]',
					'[author_id]',
					// course shortcode.
					 '[course_title]', // Course title.
					'[course_link]', // Course link (i.e course URL).
					'[course_id]', // Course ID.
					// review shortcode.
					'[review_headline]', // Review title of the user.
					'[review_content]', // Review of the user.
					'[review_link]', // Review link (i.e course URL).
					'[review_id]', // Review ID.
				);
				$replace = array(
					// user shortcode.
					$user_info->first_name, // [user_first_name].
					$user_info->last_name, // [user_last_name].
					$user_info->display_name, // [user_display_name].
					$user_info->user_email, // [user_email_id].
					$user_info->ID, // [user_id].
				   // author shortcode.
					$author_obj->first_name, // [author_first_name].
					$author_obj->last_name, // [author_last_name].
					$author_obj->display_name, // [author_display_name].
					$author_obj->user_email, // [user_email_id].
					$author_obj->ID, // [author_id].
				   // course shortcode.
					$course->post_title,
					$course_link,
					$course_id,
					// review shortcode.
					$review->post_title,
					$review->post_content,
					$review_link,
					$result['review_id'],
				);
				$email_subject = get_option( 'wdm_review_email_subject', WDM_LD_DEFAULT_REVIEW_SUBJECT );
				$email_body = get_option( 'wdm_review_email_body', WDM_LD_DEFAULT_REVIEW_BODY );

				$email_body = stripslashes( $email_body );
				$email_body = str_replace( $find, $replace, $email_body );
				$email_subject = stripslashes( $email_subject );
				$email_subject = str_replace( $find, $replace, $email_subject );

				$headers[] = "From: {$user_info->display_name} <{$user_info->user_email}>";
				$headers[] = "Reply-To: {$user_info->display_name} <{$user_info->user_email}";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';

				$email_to = array( $author_obj->user_email );

				$email_to = apply_filters( 'wdm_recipient_of_review_notification', $email_to, $user_id, $course, $review );

				wp_mail( $email_to, $email_subject, nl2br( $email_body ), $headers );
			}
		}
	}
	Review_Submission::get_instance();
}
