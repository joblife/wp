<?php
/**
 * This file is used to include the class which is used to design course metabox.
 *
 * @package RatingsReviewsFeedback\Admin\Reviews
 */

namespace ns_wdm_ld_course_review{

	/**
	 * Class to handle course metaboxes.
	 */
	class Course_MetaBox {

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
			// for removing comment and discussion metabox.
			// \wdm_add_hook(
			// 'admin_menu',
			// 'remove_comment_metabox',
			// $this,
			// array(
			// 'type' => 'action',
			// 'priority' => 10,
			// 'num_args' => 0,
			// )
			// );
			// Adding enable review metabox.
			\wdm_add_hook(
				'add_meta_boxes',
				'add_enable_review',
				$this,
				array(
					'type' => 'action',
					'priority' => 10,
					'num_args' => 0,
				)
			);
			// Adding student review metabox.
			\wdm_add_hook(
				'add_meta_boxes',
				'add_student_review',
				$this,
				array(
					'type' => 'action',
					'priority' => 10,
					'num_args' => 0,
				)
			);

			\wdm_add_hook(
				'save_post',
				'save_course_metaboxes',
				$this,
				array(
					'type' => 'action',
					'priority' => 20,
					'num_args' => 3,
				)
			);
			// adding a metabox in course edit page for feedback form setting.
			\wdm_add_hook(
				'add_meta_boxes',
				'add_feedback_setting',
				$this,
				array(
					'type' => 'action',
					'priority' => 10,
					'num_args' => 0,
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
		 * Adding a metabox in course edit page for feedback form setting.
		 */
		public function add_feedback_setting() {
			if ( current_user_can( 'edit_others_wdm_course_feedbacks' ) ) {
				add_meta_box( 'wdm_course_feedback', __( 'Course Feedback', 'wdm_ld_course_review' ), array( $this, 'render_feedback_stng' ), 'sfwd-courses', 'normal', 'high', null );
			}
		}
		/**
		 * Callback function of course feedback setting.
		 *
		 * @param object $post [post object].
		 */
		public function render_feedback_stng( $post ) {
			$setting = get_post_meta( $post->ID, 'wdm_course_feedback_setting', true );
			if ( empty( $setting ) ) {
				$setting = 0;
			}
			$default = array(
				0 => __( 'Use global setting', 'wdm_ld_course_review' ),
				1 => __( 'Yes', 'wdm_ld_course_review' ),
				2 => __( 'No', 'wdm_ld_course_review' ),
			);

			$feedback_link = site_url( '/wp-admin/edit.php?post_type=wdm_course_feedback&wdm_feedback_course_id=' . $post->ID );
			$course_feedbacks = '<a href="' . $feedback_link . '" target="_blank">' . __( 'View all feedbacks', 'wdm_ld_course_review' ) . '</a>';
			// $course_feedbacks .= __(' to view all feedbacks of this course.', 'wdm_ld_course_review');
			?>
			<label>
			<?php
			esc_html_e( 'Do you want to use feedback form ?', 'wdm_ld_course_review' );
			?>
			<select name="wdm_course_feedback_setting">
			<?php
			foreach ( $default as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $setting, $key ) . '>' . esc_html( $value ) . '</option>';
			}
			?>
			</select>
			</label>
			<?php
			if ( current_user_can( 'manage_options' ) ) {
				?>
			<div>
				<?php
				$feedback_setting = site_url( '/wp-admin/admin.php?page=wdm_course_feedback_setting' );

				echo '<a href="' . esc_attr( $feedback_setting ) . '" target="_blank">' . esc_html__( 'Global setting', 'wdm_ld_course_review' ) . '</a>';
				?>
			</div>
				<?php
			}
			?>
			<!-- <br/> -->
			<div>
			<?php
			echo $course_feedbacks;// WPCS: XSS ok.
			?>
			</div>
			<?php
		}
		/**
		 * Saving the rating & review setting.
		 *
		 * @param int    $post_id [post id].
		 * @param object $post    [post object].
		 */
		public function save_course_metaboxes( $post_id, $post ) {
			// verify if this is an auto save routine.
			// If it is our form has not been submitted, so we dont want to do anything.
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
				return;
			}

			// Check permissions.
			if ( ! current_user_can( 'edit_courses', $post_id ) || ! isset( $_POST['wdm_learndash_course_nonce'] ) ) {
				return;
			}
			/*global $wpdb;*/
			$review_status = filter_input( INPUT_POST, 'wdm_review_allow', FILTER_SANITIZE_STRING );
			$reviews_position = filter_input( INPUT_POST, 'review_position', FILTER_SANITIZE_STRING );
			update_post_meta( $post_id, 'is_ratings_enabled', $review_status );
			update_post_meta( $post_id, 'reviews_position', $reviews_position );

			/*
			$wpdb->update(
			$wpdb->prefix . 'posts',
			array( 'comment_status' => $comment ),
			array( 'ID' => $post_id ),
			array( '%s' ),
			array( '%s' )
			);
			*/

			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times.
			if ( ! wp_verify_nonce( sanitize_key( $_POST['wdm_learndash_course_nonce'] ), 'wdm_learndash_course_nonce_' . $post_id ) ) {
				return;
			}
			// for saving rating setting.
			if ( array_key_exists( 'wdm_rating_review_setting', $_POST ) ) {
				update_post_meta( $post_id, 'wdm_rating_review_setting', sanitize_text_field( wp_unslash( $_POST['wdm_rating_review_setting'] ) ) );
			}
			// for saving feedback setting.
			if ( array_key_exists( 'wdm_course_feedback_setting', $_POST ) ) {
				update_post_meta( $post_id, 'wdm_course_feedback_setting', sanitize_text_field( wp_unslash( $_POST['wdm_course_feedback_setting'] ) ) );
			}
			unset( $post );
		}

		/**
		 * Adding meta field to show avg rating on course page.
		 */
		public function add_student_review() {
			if ( current_user_can( 'edit_others_wdm_course_reviews' ) ) {
				add_meta_box( 'wdm_course_student_review', __( 'Course Rating', 'wdm_ld_course_review' ), array( $this, 'render_student_review' ), 'sfwd-courses', 'normal', 'high', null );
			}
		}
		/**
		 * Call back function of Course Rating.
		 *
		 * @param object $post [post object].
		 */
		public function render_student_review( $post ) {
			?>

			<div class="wdm_course_student_review">
				<span>
				<?php
				$rating_args = array(
					'size'          => 'xs',
					'show-clear'    => false,
					'show-caption'  => false,
					'readonly'      => true,
					'course-id'     => $post->ID,
				);
				$rating_details = rrf_get_course_rating_details( $post->ID );
				$star_rating_html = rrf_get_star_html_struct( $post->ID, floatval( $rating_details['average_rating'] ), $rating_args );
				$total_stars = count( $rating_details['rating'] );
				echo __( 'Average rating :-', 'wdm_ld_course_review' ) . $star_rating_html . '<span style="font-size: smaller;"">' . floatval( $rating_details['average_rating'] ) . __( ' out of ', 'wdm_ld_course_review' ) . $total_stars . '</span>';// WPCS: XSS ok.

				$review_link = site_url( '/wp-admin/edit.php?post_type=wdm_course_review&wdm_reviews_course_id=' . $post->ID );

				$setting = get_post_meta( $post->ID, 'wdm_rating_review_setting', true );
				if ( empty( $setting ) ) {
					$setting = 0;
				}
				$lessons = learndash_get_course_lessons_list( $post );
				$default_options = array(
					0 => __( 'Enrollment', 'wdm_ld_course_review' ),
					$post->ID => rrf_get_course_label() . ' ' . __( 'completion', 'wdm_ld_course_review' ),
				);
				?>
					</span>
					<div style="margin-top: 1%;">
						<span>
							<?php
							esc_html_e( 'Allow rating and review after', 'wdm_ld_course_review' );
							?>
				<select name="wdm_rating_review_setting">
				<?php
				foreach ( $default_options as $key => $option ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $setting, $key ) . '>' . esc_html( $option ) . '</option>';
				}
				$lessons = learndash_get_course_lessons_list( $post );
				foreach ( $lessons as $lesson ) {
					echo '<option value="' . esc_attr( $lesson['post']->ID ) . '" ' . selected( $setting, $lesson['post']->ID ) . '>' . esc_html( $lesson['post']->post_title ) . '</option>';
					$lesson_topics_list = learndash_topic_dots( $lesson['post']->ID, false, 'array' );
					foreach ( $lesson_topics_list as $topic ) {
						echo '<option value="' . esc_attr( $topic->ID ) . '" ' . selected( $setting, $topic->ID ) . '>-' . esc_html( $topic->post_title ) . '</option>';
					}
				}

				$course_quiz_list = learndash_get_course_quiz_list( $post->ID );

				foreach ( $course_quiz_list as $quiz ) {
					echo '<option value="' . esc_attr( $quiz['post']->ID ) . '" ' . selected( $setting, $quiz['post']->ID ) . '>' . esc_html( $quiz['post']->post_title ) . '</option>';
				}
				?>
							</select>
							<?php
							wp_nonce_field( 'wdm_learndash_course_nonce_' . $post->ID, 'wdm_learndash_course_nonce' );
							?>
						</span>

					</div>
					<?php
					 echo '<div style="margin-top: 0.5%;"><a href="' . esc_attr( $review_link ) . '" target="_blank">' . esc_html__( 'All reviews', 'wdm_ld_course_review' ) . '</a></div>';
					?>
			</div>

			<?php
		}
		/**
		 * Adding meta box of enabling course review on course edit page.
		 *
		 * @version 1.0.0
		 */
		public function add_enable_review() {
			add_meta_box( 'wdm_course_enable_review', __( 'Course Review', 'wdm_ld_course_review' ), array( $this, 'render_enable_review_metaboxes' ), 'sfwd-courses', 'normal', 'high', null );
		}
		/**
		 * Showing enable course review HTML structure.
		 *
		 * @param object $post [current post object].
		 */
		public function render_enable_review_metaboxes( $post ) {
			if ( ! metadata_exists( 'post', $post->ID, 'is_ratings_enabled' ) ) {
				if ( 'open' == $post->comment_status ) {
					update_post_meta( $post->ID, 'is_ratings_enabled', 'yes' );
				} else {
					update_post_meta( $post->ID, 'is_ratings_enabled', 'yes' );
				}
			}
			$review_status = get_post_meta( $post->ID, 'is_ratings_enabled', true );
			?>
		<input name="advanced_view" type="hidden" value="1" />
		<p class="meta-options">
			<label for="review_status" class="selectit">
				<input name="review_status" type="checkbox" id="review_status" value="open" 
			<?php
			checked( $review_status, 'yes' );
			?>
			 /> <?php esc_html_e( 'Enable course rating and review', 'wdm_ld_course_review' ); ?></label><br />
			<input type="hidden" name="wdm_review_allow" id="wdm_review_allow" value="<?php echo esc_attr( $review_status ); ?>" />
<script>
jQuery("#review_status").change(function(){
		if(this.checked){
		jQuery("#wdm_review_allow").val("yes");
	}else{
		jQuery("#wdm_review_allow").val("no");
	}
});
</script>
			<?php
			$reviews_position = get_post_meta( $post->ID, 'reviews_position', true );
			if ( empty( $reviews_position ) ) {
				$reviews_position = 'after';
			}
			?>
		<label for="review_position"><?php esc_html_e( 'Show Course Reviews', 'wdm_ld_course_review' ); ?></label>
		<select id="review_position" name="review_position">
			<option value="after" <?php selected( $reviews_position, 'after' ); ?>><?php esc_html_e( 'After Content', 'wdm_ld_course_review' ); ?></option>
			<option value="before" <?php selected( $reviews_position, 'before' ); ?>><?php esc_html_e( 'Before Content', 'wdm_ld_course_review' ); ?></option>
			<option value="custom" <?php selected( $reviews_position, 'custom' ); ?>>
				<?php
				$shortcode = '[rrf_course_review course_id="' . $post->ID . '"]';
				/* translators: %s : Shortcode */
				echo esc_html( sprintf( __( 'Custom Location (use shortcode %s)', 'wdm_ld_course_review' ), $shortcode ) );
				?>
			</option>
		</select>
		<input type="hidden" name="review_shortcode" id="review_shortcode" class="review_shortcode" value="<?php echo esc_html( $shortcode ); ?>" />
		<button class="copy-shortcode components-button is-button is-default" title="<?php echo esc_attr__( 'Copy Shortcode', 'wdm_ld_course_review' ); ?>"><i class="fa fa-files-o"></i></button>
		<script type="text/javascript">
			jQuery('.copy-shortcode').on('click', function(evnt){
				var self = this;
				evnt.preventDefault();
				evnt.stopPropagation();
				var copyText = document.getElementById("review_shortcode");
				var shortcode = copyText.value;
				const dummy = document.createElement('textarea');
				dummy.value = shortcode;
				document.body.appendChild(dummy);
				dummy.select();
				// copyText.setSelectionRange(0, 99999); /*For mobile devices*/
				document.execCommand('copy');
				document.body.removeChild(dummy);
				jQuery(this).after('<span class="rrf-copy-status">Copied Successfully!</span>');
				setTimeout(function(){
					jQuery(self).siblings('.rrf-copy-status').remove();
				}, 1000)
			});
		</script>
		</p>
			<?php
		}
		/**
		 * Removing wordpress comment and discussion metabox from course edit page.
		 *
		 * @version 1.0.0
		 */
		public function remove_comment_metabox() {
			remove_meta_box( 'commentstatusdiv', 'sfwd-courses', 'normal' );
			remove_meta_box( 'commentsdiv', 'sfwd-courses', 'normal' );
		}
	}
	Course_MetaBox::get_instance();
}
