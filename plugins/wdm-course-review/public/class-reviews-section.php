<?php
/**
 * This class contains the logic to load templates related to the reviews section.
 *
 * @version 2.0.0
 * @package RatingsReviewsFeedback\Public\Reviews
 */

namespace ns_wdm_ld_course_review {
	if ( ! class_exists( 'Reviews_Section' ) ) {
		/**
		 * This class is used for loading the review template for course single page and the shortcode for the same.
		 *
		 * @SuppressWarnings(PHPMD.ShortVariable)
		 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
		 */
		class Reviews_Section {
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
				add_action( 'wp', array( $this, 'reviews_template_location' ) );
				add_action( 'wp_ajax_update_helpful_count', array( $this, 'update_helpful_count' ) );
				add_action( 'wp_ajax_nopriv_update_helpful_count', array( $this, 'update_helpful_count' ) );
				add_action( 'wp_ajax_get_paged_reviews', array( $this, 'get_paged_reviews' ) );
				add_action( 'wp_ajax_nopriv_get_paged_reviews', array( $this, 'get_paged_reviews' ) );
				add_shortcode( 'rrf_course_review', array( $this, 'reviews_template_shortcode' ) );
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
			 * This method is used for processing the logic for review template location.
			 *
			 * @SuppressWarnings(PHPMD.NPathComplexity)
			 */
			public function reviews_template_location() {
				global $post;
				if ( is_admin() && ! wp_doing_ajax() ) {
					return;
				}
				$course_taxonomies = get_object_taxonomies( 'sfwd-courses' );
				if ( is_tax( $course_taxonomies ) || is_post_type_archive( 'sfwd-courses' ) || ( isset( $post ) && ( has_shortcode( $post->post_content, 'ld_course_list' ) || has_block( 'learndash/ld-course-list', $post ) ) ) ) {
					remove_shortcode( 'rrf_course_review' );
					return;
				}
				if ( isset( $post ) && ! empty( $post ) ) {
					$course_id = learndash_get_course_id( $post );
					if ( 'sfwd-courses' == $post->post_type ) {
						$course_id = $post->ID;
					}
					if ( empty( $course_id ) && ! has_shortcode( $post->post_content, 'rrf_course_review' ) ) {
						return;
					}
					$reviews_location = get_post_meta( $course_id, 'reviews_position', true );
					if ( empty( $reviews_location ) ) {
						if ( ! has_shortcode( $post->post_content, 'rrf_course_review' ) ) {
							$reviews_location = 'after';
						} else {
							$reviews_location = 'custom';
						}
					}

					switch ( $reviews_location ) {
						case 'after':
							if ( 'ld30' === \LearnDash_Theme_Register::get_active_theme_key() ) {
								add_action( 'learndash-course-after', array( $this, 'render_reviews_template' ), 9999, 2 );
							} else {
								add_filter( 'the_content', array( $this, 'show_reviews_template' ), 9999, 1 );
							}
							break;
						case 'before':
							add_filter( 'the_content', array( $this, 'show_reviews_template' ), 10, 1 );
							break;
						case 'custom':
							break;
						default:
							break;
					}
				}
			}

			/**
			 * This method is used to render review template for LD 3,0 when review position setting is set as 'after'.
			 *
			 * @SuppressWarnings("unused")
			 * @param [integer] $post_id [Post ID].
			 * @param  [integer] $course_id [Course ID].
			 */
			public function render_reviews_template( $post_id, $course_id ) {
				global $post;
				if ( 'sfwd-courses' != $post->post_type ) { // Show only on course single page.
					return;
				}
				$course = get_post( $course_id );
				if ( ! metadata_exists( 'post', $course->ID, 'is_ratings_enabled' ) ) {
					if ( 'open' == $course->comment_status ) {
						update_post_meta( $course->ID, 'is_ratings_enabled', 'yes' );
					} else {
						update_post_meta( $course->ID, 'is_ratings_enabled', 'yes' );
					}
				}
				$review_status = get_post_meta( $course->ID, 'is_ratings_enabled', true );
				if ( empty( $review_status ) || 'no' == $review_status ) {
					return;
				}
				$review_content = $this->get_reviews_section( $course_id );
				echo $review_content;// WPCS : XSS ok.
			}

			/**
			 * This method is used to render reviews shortcode.
			 *
			 * @param  array $atts [must contain course_id key].
			 * @return string Reviews Shortcode Content.
			 */
			public function reviews_template_shortcode( $atts ) {
				global $post, $rrf_modal_settings;
				$course_taxonomies = get_object_taxonomies( 'sfwd-courses' );
				if ( is_post_type_archive( 'sfwd-courses' ) || is_tax( $course_taxonomies ) ) {
					return '';
				}
				$atts = shortcode_atts(
					array(
						'course_id' => '0',
					),
					$atts,
					'rrf_course_review'
				);
				$course_id = (int) $atts['course_id'];
				if ( empty( $course_id ) ) {
					if ( 'sfwd-courses' == $post->post_type ) {
						$course_id = $post->ID;
					} else {
						$course_id = learndash_get_course_id( $post );
					}
				}
				$course = get_post( $course_id );
				if ( ! metadata_exists( 'post', $course->ID, 'is_ratings_enabled' ) ) {
					if ( 'open' == $course->comment_status ) {
						update_post_meta( $course->ID, 'is_ratings_enabled', 'yes' );
					} else {
						update_post_meta( $course->ID, 'is_ratings_enabled', 'yes' );
					}
				}
				$review_status = get_post_meta( $course->ID, 'is_ratings_enabled', true );
				if ( empty( $review_status ) || 'no' == $review_status ) {
					return '';
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
				$review_content = $this->get_reviews_section( $course_id );
				return $review_content;
			}

			/**
			 * This method is used to append Reviews section to the content (for LearnDash Legacy).
			 *
			 * @param  string $content Post Content.
			 * @return string $content
			 */
			public function show_reviews_template( $content ) {
				global $post;
				if ( 'sfwd-courses' !== $post->post_type ) {
					return $content;
				}
				if ( ! metadata_exists( 'post', $post->ID, 'is_ratings_enabled' ) ) {
					if ( 'open' == $post->comment_status ) {
						update_post_meta( $post->ID, 'is_ratings_enabled', 'yes' );
					} else {
						update_post_meta( $post->ID, 'is_ratings_enabled', 'yes' );
					}
				}
				$review_status = get_post_meta( $post->ID, 'is_ratings_enabled', true );
				if ( empty( $review_status ) || 'no' == $review_status ) {
					return $content;
				}
				$course_id = $post->ID;
				$reviews_location = get_post_meta( $course_id, 'reviews_position', true );
				if ( empty( $reviews_location ) ) {
					$reviews_location = 'after';
				}
				$review_content = $this->get_reviews_section( $course_id );
				if ( 'before' === $reviews_location ) {
					return $review_content . $content;
				}
				return $content . $review_content;
			}

			/**
			 * This method is used to fetch the review section template HTML.
			 *
			 * @SuppressWarnings(PHPMD.ShortVariable)
			 * @SuppressWarnings("unused")
			 * @param integer $course_id [Course ID].
			 * @return string Review Section HTML
			 */
			private function get_reviews_section( $course_id = 0 ) {
				global $wp; // phpcs:ignore 
				$current_url = home_url( add_query_arg( $_GET, $wp->request ) );
				ob_start();
				if ( ! is_rtl() ) {
					wp_enqueue_style( 'reviews-css', plugins_url( 'public/css/reviews-shortcode.css', RRF_PLUGIN_FILE ), array(), WDM_LD_COURSE_VERSION );
				} else {
					wp_enqueue_style( 'reviews-css', plugins_url( 'public/css/rtl/reviews-shortcode.css', RRF_PLUGIN_FILE ), array(), WDM_LD_COURSE_VERSION );
				}
				wp_enqueue_script( 'reviews-js', plugins_url( 'public/js/review-helpful.js', RRF_PLUGIN_FILE ), array( 'jquery' ), WDM_LD_COURSE_VERSION );
				wp_localize_script(
					'reviews-js',
					'helpful_object',
					array(
						'url' => admin_url( 'admin-ajax.php' ),
						'action' => 'update_helpful_count',
						'nonce' => wp_create_nonce( 'update_helpful_count' ),
					)
				);
				wp_localize_script(
					'reviews-js',
					'reviews_filter_query',
					array(
						'current_url' => $current_url,
					)
				);
				wp_localize_script(
					'reviews-js',
					'reviews_paginate_query',
					array(
						'url'       => admin_url( 'admin-ajax.php' ),
						'action'    => 'get_paged_reviews',
						'nonce'     => wp_create_nonce( 'get_paged_reviews' ),
					)
				);
				include Review_Submission::get_template( 'reviews-section.php' );
				return ob_get_clean();
			}

			 /**
			  * To update the helpful count of the review.
			  * $_POST array
			  *   e.g
			  *   action:wdm_course_review_bar
			  *   review_id:6 (review_id id)
			  *   answer:yes/no
			  *   security:ddd08415c9 (nonce).
			  */
			public function update_helpful_count() {
				// check to see if the submitted nonce matches with the
				// generated nonce we created earlier.
				$result = array(
					'success' => false,
					'review_id' => 0,
					'status' => '',
					'message' => '',
					'display_msg' => '',
					'validation_pass' => false,
				);
				if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_key( $_POST['security'] ), 'update_helpful_count' ) ) {
					echo json_encode( $result );
					die();
				}
				$answer = filter_input( INPUT_POST, 'answer', FILTER_SANITIZE_STRING );
				$review_id = filter_input( INPUT_POST, 'review_id', FILTER_VALIDATE_INT );
				if ( empty( $answer ) || ! in_array( $answer, array( 'yes', 'no' ) ) || empty( $review_id ) ) {
					echo json_encode( $result );
					die();
				}
				if ( ! is_user_logged_in() ) {
					$parameters = array(
						'was_review_helpful' => $answer,
						'review_id' => $review_id,
					);

					$encoded = urlencode( base64_encode( serialize( $parameters ) ) );
					if ( ! isset( $_COOKIE['rrf-query'] ) ) {
						setcookie( 'rrf-query', $encoded, time() + 10, COOKIEPATH, COOKIE_DOMAIN );
					}

					$current_url = add_query_arg( 'temp', 'ceq32bv5yww#wdm_review_id_' . $review_id, $_SERVER['HTTP_REFERER'] );// phpcs:ignore
					$result['redirecturl'] = wp_login_url( $current_url );
					echo json_encode( $result );
					die();
				}
				$user_id = get_current_user_id();
				$result['success'] = true;
				update_review_helpful_meta( $user_id, $review_id, $answer );
				$result['message'] = '<span class="wdm-success">' . __( 'Thank you for your feedback', 'wdm_ld_course_review' ) . '</span>';
				$postmeta_key = 'wdm_helpful_yes';
				$count = get_post_meta( $review_id, $postmeta_key, true );
				$result['display_msg'] = rrf_get_helpful_message( intval( $count ) );
				echo json_encode( $result );
				die();
			}

			/**
			 * This method is used to query paginated reviews for view more reviews.
			 */
			public function get_paged_reviews() {
				$course_id = filter_input( INPUT_POST, 'course_id', FILTER_VALIDATE_INT );
				$paged = filter_input( INPUT_POST, 'page', FILTER_VALIDATE_INT );
				$sortby = filter_input( INPUT_POST, 'sortby', FILTER_SANITIZE_STRING );
				$filterby = filter_input( INPUT_POST, 'filterby', FILTER_VALIDATE_INT );
				$security = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_STRING );
				if ( empty( $course_id ) || empty( $paged ) || empty( $sortby ) || empty( $filterby ) || empty( $security ) ) {
					$error = new \WP_Error( '001', __( 'Invalid invocation(data missing)', 'wdm_ld_course_review' ) );
					wp_send_json_error( $error );
				}
				if ( ! wp_verify_nonce( $security, 'get_paged_reviews' ) ) {
					$error = new \WP_Error( '002', __( 'Invalid Nonce', 'wdm_ld_course_review' ) );
					wp_send_json_error( $error );
				}
				$review_args = array(
					'posts_per_page'    => apply_filters( 'rrf_number_of_reviews_per_page', get_option( 'posts_per_page', 10 ) ),
					// 'posts_per_page'    => 1,
					'orderby'           => $sortby,
					'paged'             => $paged,
				);
				if ( '-1' != $filterby ) {
					$review_args['meta_query'] = array(
						array(
							'key'       => 'wdm_course_review_review_on_course',
							'value'     => $course_id,
							'compare'   => '=',
						),
					);
					$review_args['meta_query'][] = array(
						array(
							'key' => 'wdm_course_review_review_rating',
							'value' => sanitize_key( $filterby ),
							'compare' => '=',
						),
					);
				}
				$reviews = rrf_get_all_course_reviews(
					$course_id,
					$review_args
				);
				ob_start();
				?>
				<div class="review_listing">
					<?php
					if ( ! empty( $reviews ) ) {
						foreach ( $reviews as $review ) {
							$review = $review;
							include \ns_wdm_ld_course_review\Review_Submission::get_template( 'single-review.php' );
						}
					} else {
						?>
						<div><?php esc_html_e( 'No Reviews Found!', 'wdm_ld_course_review' ); ?> </div>
						<?php
					}
					?>
				</div><!-- .review_listing closing -->
				<?php
				$data = ob_get_clean();
				wp_send_json_success( array( 'html' => $data ) );
			}
		}
	}
	Reviews_Section::get_instance();
}
