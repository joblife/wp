<?php
/**
 * This file includes the star rating display logic in the frontend.
 *
 * @version 2.0.0
 * @package RatingsReviewsFeedback\Public\Feedback
 */

namespace ns_wdm_ld_course_review {
	if ( ! class_exists( 'Feedback_Handler' ) ) {
		/**
		 * This class is used for handling the entire logic for feedback.
		 */
		class Feedback_Handler {
			/**
			 * This property contains the singleton instance of the class.
			 *
			 * @var Class Object.
			 */
			protected static $instance = null;
			/**
			 * This method is used to add all action/filter hooks.
			 */
			public function __construct() {
				// filter to add feedback button.
				add_filter( 'ld_after_course_status_template_container', array( $this, 'show_feedback_button' ), 99, 4 );
				// for saving feedback form and creating a post for the same.
				add_action( 'wp_ajax_wdm_course_feedback_submission', array( $this, 'rrf_course_feedback_submission' ) );
				// for sending email to author after feedback.
				add_action( 'wdm_feedback_form_submitted_successfully', array( $this, 'rrf_send_feedback_email_to_author' ), 10, 3 );
				// changing from name and from email id.
				add_action( 'phpmailer_init', array( $this, 'rrf_wp_smtp' ), 999, 1 );
				// changing from email address of the feedback email notification.
				add_filter( 'wp_mail_from', array( $this, 'change_from_email_id' ), 10, 1 );
				// changing from name of the feedback email notification.
				add_filter( 'wp_mail_from_name', array( $this, 'change_from_name' ), 10, 1 );
			}

			/**
			 * This function is used to fetch the instance of this class.
			 *
			 * @return Object returns class instance.
			 */
			public static function get_instance() {
				if ( is_null( self::$instance ) ) {
					self::$instance = new self();
				}
				return self::$instance;
			}

			/**
			 * To show the feedback button on course single page after user completes the course.
			 *
			 * @param string $content       [default content].
			 * @param string $course_status [course status].
			 * @param int    $course_id     [course id].
			 * @param int    $user_id       [user id].
			 *
			 * @return string $content       [modified content]
			 */
			public function show_feedback_button( $content, $course_status, $course_id, $user_id ) {
				global $rrf_modal_settings;
				$course_status = $course_status;
				if ( learndash_course_completed( $user_id, $course_id ) ) {
					if ( ! rrf_is_feedback_form_enabled( $course_id ) ) {
						return $content;
					}
					if ( rrf_is_user_submitted_feedback_form( $user_id, $course_id ) ) {
						return $content;
					}
					$btn_text = get_option( 'wdm_course_feedback_btn_txt', __( 'Provide your feedback', 'wdm_ld_course_review' ) );
					if ( empty( $btn_text ) ) {
						$btn_text = __( 'Provide your feedback', 'wdm_ld_course_review' );
					}
					$btn_text = apply_filters( 'wdm_crr_feedback_button_text', $btn_text, $course_id );
					$path = apply_filters( 'wdm_crr_feedback_template', self::get_template( 'popup-feedback-form.php' ), $course_id );
					$maxlength = get_option( 'wdm_crr_feedback_limit', 0 );
					$ajax_nonce = wp_create_nonce( 'wdm-nonce-course-feedback' );
					if ( empty( $maxlength ) ) {
						$maxlength = 400;
					}
					rrf_load_jquery_modal_lib( true );
					if ( ! is_rtl() ) {
						wp_enqueue_style( 'popup-feedback-form-css', plugins_url( 'public/css/popup-feedback-form.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'public/css/popup-feedback-form.css' ) );
					} else {
						wp_enqueue_style( 'popup-feedback-form-css', plugins_url( 'public/css/rtl/popup-feedback-form.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'public/css/rtl/popup-feedback-form.css' ) );
					}
					wp_enqueue_script( 'popup-feedback-form-js', plugins_url( 'public/js/popup-feedback-form.js', RRF_PLUGIN_FILE ), array( 'jquery', 'jquery-modal-js' ), filemtime( RRF_PLUGIN_PATH . 'public/js/popup-feedback-form.js' ), true );
					wp_localize_script(
						'popup-feedback-form-js',
						'feedback_ajax_data',
						array(
							'url'           => admin_url( 'admin-ajax.php' ),
							'action'        => 'wdm_course_feedback_submission',
							'loader_url'    => plugins_url( 'public/images/loader.gif', RRF_PLUGIN_FILE ),
							'nonce'         => $ajax_nonce,
							'course_id'     => $course_id,
							'wait_message'  => __( 'Please wait', 'wdm_ld_course_review' ),
							'ajax_time'     => __( 'Failed from timeout', 'wdm_ld_course_review' ),
							'maxlength'     => $maxlength,
							'error_msg'     => __( 'Should not exceed ', 'wdm_ld_course_review' ) . $maxlength . __( ' characters.', 'wdm_ld_course_review' ),
							'rrf_modal_settings' => $rrf_modal_settings,
						)
					);
					include_once $path;
				}
				return $content;
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
			 * Ajax callback to create feedback post.
			 */
			public function rrf_course_feedback_submission() {
				$result = array();
				$result['success'] = false;
				$result['status'] = 'security_issue';
				$result['message'] = '';
				$message = '<div class="wdm_feedback_msg">
                    <span class="%s">
                    %s
                    </span>
                    </div>';
				$security = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_STRING );
				// security checking.
				if ( ! isset( $security ) || ! wp_verify_nonce( $security, 'wdm-nonce-course-feedback' ) ) {
					$result['status'] = 'security_issue';
					$result['message'] = sprintf( $message, 'wdm-failure', __( 'Something went wrong!', 'wdm_ld_course_review' ) );
					echo json_encode( $result );
					die();
				}
				$user_id = get_current_user_id();
				$course_id = rrf_check_if_post_set( $_POST, 'course_id' );
				$feedback = sanitize_text_field( rrf_check_if_post_set( $_POST, 'user_feedback' ) );
				if ( empty( $course_id ) ) {
					$result['status'] = 'empty_course_id';
					$result['success'] = false;
					$result['message'] = sprintf( $message, 'wdm-failure', __( 'Something went wrong!', 'wdm_ld_course_review' ) );
					echo json_encode( $result );
					die();
				} elseif ( empty( $feedback ) ) {
					$result['status'] = 'empty_feedback';
					$result['success'] = false;
					$result['message'] = sprintf( $message, 'wdm-failure', __( 'Please provide feedback', 'wdm_ld_course_review' ) );
					echo json_encode( $result );
					die();
				}

				$is_allowed = rrf_is_user_submitted_feedback_form( $user_id, $course_id );
				if ( $is_allowed ) {
					$result['status'] = 'already_submitted';
					$result['success'] = false;
					$result['message'] = sprintf( $message, 'wdm-failure', __( 'Sorry! You have already submitted the feedback form for this course.', 'wdm_ld_course_review' ) );
					echo json_encode( $result );
					die();
				}
				$user_info = get_userdata( $user_id );
				$feedback_title = 'By ' . $user_info->display_name;
				$args = array(
					'post_author' => $user_id,
					'post_content' => $feedback,
					'post_status' => 'publish',
					'post_title' => $feedback_title,
					'post_type' => 'wdm_course_feedback',
				);
				$feedback_id = wp_insert_post( $args, true );
				// Giving feedback for the first time.
				if ( ! is_wp_error( $feedback_id ) ) {
					update_post_meta( $feedback_id, 'wdm_course_feedback_feedback_on_course', $course_id );
					$result['status'] = 'submitted_successfully';
					$result['success'] = true;
					$result['message'] = sprintf( $message, 'wdm-success', __( 'Thank you for your feedback!', 'wdm_ld_course_review' ) );
					do_action( 'wdm_feedback_form_submitted_successfully', $user_id, $course_id, $feedback_id );
					echo json_encode( $result );
					die();
				}

				$result['success'] = false;
				$result['message'] = sprintf( $message, 'wdm-failure', __( 'Something went wrong!', 'wdm_ld_course_review' ) );
				echo json_encode( $result );
				die();
			}

			/**
			 * Sending mail to author on feedback submission.
			 *
			 * @param int $user_id     [user id].
			 * @param int $course_id   [course id].
			 * @param int $feedback_id [feedback id].
			 */
			public function rrf_send_feedback_email_to_author( $user_id, $course_id, $feedback_id ) {
				$is_email_enabled = get_option( 'wdm_send_email_after_feedback', 1 );
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

				// Feedback details.
				$feedback = get_post( $feedback_id );
				if ( ! $feedback ) {
					return;
				}
				$feedback_permalink = get_edit_post_link( $feedback_id );
				$post_type_object = get_post_type_object( $feedback->post_type );
				if ( ! $post_type_object ) {
					return;
				}
				if ( $post_type_object->_edit_link ) {
					$feedback_permalink = admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $feedback_id ) );
				}
				$feedback_link = '';
				if ( user_can( $author_obj->ID, 'edit_others_wdm_course_feedbacks' ) ) {
					$feedback_link = '<a href="' . $feedback_permalink . '">' . __( 'feedback', 'wdm_ld_course_review' ) . '</a>';
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
					// feedback shortcode.
					'[feedback_content]', // Feedback of the user.
					'[feedback_link]', // Feedback link (i.e course URL).
					'[feedback_id]', // Feedback ID.
				);

				$replace = array(
					// user shortcode.
					$user_info->first_name, // [user_first_name]
					$user_info->last_name, // [user_last_name]
					$user_info->display_name, // [user_display_name]
					$user_info->user_email, // [user_email_id]
					$user_info->ID, // [user_id]
					// author shortcode.
					$author_obj->first_name, // [author_first_name]
					$author_obj->last_name, // [author_last_name]
					$author_obj->display_name, // [author_display_name]
					$author_obj->user_email, // [user_email_id]
					$author_obj->ID, // [author_id]
					// course shortcode.
					$course->post_title,
					$course_link,
					$course_id,
					// feedback shortcode.
					$feedback->post_content,
					$feedback_link,
					$feedback_id,
				);
				$email_subject = get_option( 'wdm_feedback_email_subject', WDM_LD_DEFAULT_FEEDBACK_SUBJECT );
				$email_body = get_option( 'wdm_feedback_email_body', WDM_LD_DEFAULT_FEEDBACK_BODY );

				$email_body = stripslashes( $email_body );
				$email_body = str_replace( $find, $replace, $email_body );
				$email_subject = stripslashes( $email_subject );
				$email_subject = str_replace( $find, $replace, $email_subject );

				$headers[] = "From: {$user_info->display_name} <{$user_info->user_email}>";
				$headers[] = "Reply-To: {$user_info->display_name} <{$user_info->user_email}";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';

				$email_to = array( $author_obj->user_email );

				$email_to = apply_filters( 'wdm_recipient_of_feedback_notification', $email_to, $user_id, $course, $feedback );

				wp_mail( $email_to, $email_subject, nl2br( $email_body ), $headers );
			}
			/**
			 * Changing from name on feedback submission email.
			 *
			 * @param string $from_name [from name].
			 *
			 * @return string $from_name [from name]
			 */
			public function change_from_name( $from_name ) {
				$security = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_STRING );
				if ( empty( $security ) || ! wp_verify_nonce( $security, 'wdm-nonce-course-feedback' ) ) {
					return $from_name;
				}
				$current_user = wp_get_current_user();
				if ( $current_user ) {
					$from_name = $current_user->display_name;
				}

				return $from_name;
			}
			/**
			 * Changing from email on feedback submission email.
			 *
			 * @param string $from_email [from email].
			 *
			 * @return string $from_email [from email]
			 */
			public function change_from_email_id( $from_email ) {
				$security = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_STRING );
				if ( empty( $security ) || ! wp_verify_nonce( $security, 'wdm-nonce-course-feedback' ) ) {
					return $from_email;
				}

				$current_user = wp_get_current_user();
				if ( $current_user ) {
					$from_email = $current_user->user_email;
				}

				return $from_email;
			}
			/**
			 * Setting reply-to for feedback email.
			 *
			 * @param object $phpmailer email configuaration.
			 *
			 * @since  3.0.0
			 */
			public function rrf_wp_smtp( $phpmailer ) {
				$security = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_STRING );
				if ( empty( $security ) || ! wp_verify_nonce( $security, 'wdm-nonce-course-feedback' ) ) {
					return;
				}
				$current_user = wp_get_current_user();
				if ( $current_user ) {
					// phpcs:disable
					global $wsOptions;
					if ( ! is_email( $wsOptions['from'] ) || empty( $wsOptions['host'] ) || ! isset( $wsOptions ) ) {
						return;
					}
					$phpmailer->Mailer = 'smtp';
					$phpmailer->From = esc_html( $current_user->user_email );
					$wsOptions['from'] = esc_html( $current_user->user_email );
					$phpmailer->FromName = esc_html( $current_user->display_name );
					$phpmailer->Sender = esc_html( $current_user->user_email ); // Return-Path
					$phpmailer->AddReplyTo( $phpmailer->From, $phpmailer->FromName ); // Reply-To
					$phpmailer->Host = $wsOptions['host'];
					$phpmailer->SMTPSecure = $wsOptions['smtpsecure'];
					$phpmailer->Port = $wsOptions['port'];
					$phpmailer->SMTPAuth = ( $wsOptions['smtpauth'] == 'yes' ) ? true : false;
					if ( $phpmailer->SMTPAuth ) {
						$phpmailer->Username = $wsOptions['username'];
						$phpmailer->Password = $wsOptions['password'];
					}
					// phpcs:enable
				}
			}
		}
	}
	Feedback_Handler::get_instance();
}
