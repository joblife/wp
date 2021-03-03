<?php
/**
 * This file includes the star rating display logic in the frontend.
 *
 * @version 2.0.0
 * @package RatingsReviewsFeedback\Public\Ratings
 */

namespace ns_wdm_ld_course_review {
	if ( ! class_exists( 'Ratings_Star_Display' ) ) {
		/**
		 * This class is used for showing the star rating after the course title on each page.
		 */
		class Ratings_Star_Display {
			/**
			 * This property contains the singleton instance of the class.
			 *
			 * @var Class Object.
			 */
			protected static $instance = null;
			/**
			 * This property contains the array of exceptions.
			 *
			 * @var Array.
			 */
			protected $allowed_pages = '';
			/**
			 * This method is used to add all action/filter hooks.
			 */
			public function __construct() {
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_star_rating_display_assets' ) );
				add_action( 'wp', array( $this, 'set_course_transient' ) );
				add_action( 'get_footer', array( $this, 'localize_course_ratings' ), 999 );
				add_action( 'init', array( $this, 'initialize_default_ratings_configuration' ), 0 );
			}

			/**
			 * This query is used for setting ratings global for all courses.
			 */
			public function set_course_transient() {
				if ( is_admin() ) {
					return;
				}
				$courses = get_posts(
					array(
						'post_type' => 'sfwd-courses',
						'posts_per_page' => -1,
					)
				);
				foreach ( $courses as $course ) {
					$this->ratings_global_set( $course );
				}
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
			 * This method is used to enqueue all the star rating display related assets.
			 */
			public function enqueue_star_rating_display_assets() {
				global $post;

				$this->allowed_pages = apply_filters( 'rrf_exception_for_pages', array() );

				$by_pass = false;
				if ( ! empty( $post ) && ! empty( $this->allowed_pages ) ) {
					$by_pass = in_array( $post->ID, $this->allowed_pages );
				}

				// If this is not an exception page, then check other conditions.
				if ( ! $by_pass ) {
					$course_taxonomies = get_object_taxonomies( 'sfwd-courses' );

					if ( ! is_singular( 'sfwd-courses' ) && ! is_post_type_archive( 'sfwd-courses' ) && ! is_tax( $course_taxonomies ) ) {
						return;
					}
				}

				rrf_load_star_rating_lib();
				if ( ! is_rtl() ) {
					wp_enqueue_style( 'star-rating-display-css', plugins_url( 'public/css/ratings.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'public/css/ratings.css' ) );
				} else {
					wp_enqueue_style( 'star-rating-display-css', plugins_url( 'public/css/rtl/ratings.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'public/css/rtl/ratings.css' ) );
				}
				wp_register_script( 'star-rating-display-js', plugins_url( 'public/js/ratings.js', RRF_PLUGIN_FILE ), array( 'jquery', 'star-rating-js' ), filemtime( RRF_PLUGIN_PATH . 'public/js/ratings.js' ), true );
			}

			/**
			 * This function is used to pass the ratings values to the ratings JS.
			 *
			 * @SuppressWarnings(PHPMD.LongVariable)
			 */
			public function localize_course_ratings() {
				global $rrf_course_ratings, $rrf_ratings_settings, $learndash_shortcode_used;
				if ( empty( $rrf_course_ratings ) ) {
					return;
				}
				if ( ! wp_script_is( 'star-rating-display-js', 'registered' ) ) {
					if ( ! $learndash_shortcode_used ) {
						return;
					}
					rrf_load_star_rating_lib();
					if ( ! is_rtl() ) {
						wp_enqueue_style( 'star-rating-display-css', plugins_url( 'public/css/ratings.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'public/css/ratings.css' ) );
					} else {
						wp_enqueue_style( 'star-rating-display-css', plugins_url( 'public/css/rtl/ratings.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'public/css/rtl/ratings.css' ) );
					}
					wp_register_script( 'star-rating-display-js', plugins_url( 'public/js/ratings.js', RRF_PLUGIN_FILE ), array( 'jquery', 'star-rating-js' ), filemtime( RRF_PLUGIN_PATH . 'public/js/ratings.js' ), true );
				}
				$rrf_ratings_settings['displayOnly'] = true;
				$course_taxonomies = get_object_taxonomies( 'sfwd-courses' );
				if ( is_singular( 'sfwd-courses' ) ) {
					$rrf_ratings_settings['hasSeparateReviewsSections'] = true;
					$rrf_ratings_settings['size']                       = 'sm';
					$rrf_ratings_settings                               = apply_filters( 'rrf_course_single_page_ratings_settings', $rrf_ratings_settings );
				} elseif ( is_post_type_archive( 'sfwd-courses' ) || is_tax( $course_taxonomies ) ) {
					// $rrf_ratings_settings['showTotalReviews']           = false;
					// $rrf_ratings_settings['allowReviewSubmission']      = true;
					$rrf_ratings_settings                               = apply_filters( 'rrf_course_archive_page_ratings_settings', $rrf_ratings_settings );
				} elseif ( $learndash_shortcode_used ) {
					// $rrf_ratings_settings['showTotalReviews']           = false;
					// $rrf_ratings_settings['allowReviewSubmission']      = true;
					$rrf_ratings_settings                               = apply_filters( 'rrf_ld_shortcode_page_ratings_settings', $rrf_ratings_settings );
				}
				wp_enqueue_script( 'star-rating-display-js' );
				wp_localize_script( 'star-rating-display-js', 'rating_details', $rrf_course_ratings );
				wp_localize_script( 'star-rating-display-js', 'rating_settings', $rrf_ratings_settings );
			}

			/**
			 * Calculate global value for each course.
			 *
			 * @SuppressWarnings(PHPMD.NPathComplexity)
			 * @param  WP_Post object $post The post being processed.
			 * @version 2.0.0
			 */
			public function ratings_global_set( $post ) {
				/*
				Check for course query.
				if ( 'sfwd-courses' !== $post->post_type ) {
					return;
				}
				if ( is_admin() && ! wp_doing_ajax() ) {
					return;
				}
				*/
				global $rrf_course_ratings;
				if ( ! isset( $rrf_course_ratings[ $post->ID ] ) ) {
					if ( ! metadata_exists( 'post', $post->ID, 'is_ratings_enabled' ) ) {
						if ( 'open' == $post->comment_status ) {
							update_post_meta( $post->ID, 'is_ratings_enabled', 'yes' );
						} else {
							update_post_meta( $post->ID, 'is_ratings_enabled', 'yes' );
						}
					}
					$review_status = get_post_meta( $post->ID, 'is_ratings_enabled', true );
					if ( empty( $review_status ) || 'no' == $review_status ) {
						return;
					}
					$user_id            = get_current_user_id();
					$can_submit_rating  = rrf_can_user_post_reviews( $user_id, $post->ID );
					$course_ratings     = rrf_get_course_rating_details( $post->ID );
					$class              = 'not-allowed';
					if ( $can_submit_rating ) {
						$user_ratings   = rrf_get_user_course_review_id( $user_id, $post->ID );
						if ( empty( $user_ratings ) ) {
							$course_ratings['user_rating']  = 0.0;
							$class                          = 'not-rated';
						} else {
							$course_ratings['user_rating']  = intval( get_post_meta( $user_ratings->ID, 'wdm_course_review_review_rating', true ) );
							$class                          = 'already-rated';
						}
					}
					$rrf_course_ratings[ $post->ID ]                        = $course_ratings;
					$rrf_course_ratings[ $post->ID ]['title']               = $post->post_title;
					$rrf_course_ratings[ $post->ID ]['can_submit_rating']   = $can_submit_rating;
					$rrf_course_ratings[ $post->ID ]['class']               = $class;
				}
			}

			/**
			 * This method is used to initialize the default configurations used for the star rating.
			 *
			 * @see  rrf_default_rating_settings [Filter used to modify the default settings.]
			 * Note: This filter is only used to modify the default values, not the actual values used. There are different filters for each usage of the star rating lib i.e., on course single page, course archive page etc. Any specific setting modified there will override this filter. So, if your changes are not being applied you might try checking those configurations.
			 */
			public function initialize_default_ratings_configuration() {
				global $rrf_ratings_settings;
				$rrf_ratings_settings = array(
					'theme'                         => 'krajee-fa',
					'stars'                         => apply_filters( 'rrf_max_star_rating', 5 ),
					'max'                           => apply_filters( 'rrf_max_star_rating', 5 ),
					'min'                           => 0,
					'step'                          => 1,
					'disabled'                      => false,   // whether the input is disabled.
					'readonly'                      => false,   // whether the input is read only.
					'displayOnly'                   => false,   // whether the widget is a display only control. This is a bit different than disabled and readonly. It actually provides a fast shortcut method, to only display a rating with highlighted stars in a view and hides the caption and clear button. It also prevent any edits to the rating control by the user.
					'rtl'                           => is_rtl(),
					'animate'                       => true,    // whether to animate the stars when the rating stars are highlighted on hover or click.
					'showClear'                     => true,    // whether the clear button is to be displayed.
					'showCaption'                   => false,    // whether the rating caption is to be displayed.
					'size'                          => 'xs',    // size of the rating control. One of xl, lg, md, sm, or xs.
					// 'starCaptions'                  => array(
					// '0.5'     => __( 'Half Star', 'wdm_ld_course_review' ),
					// '1'       => __( 'One Star', 'wdm_ld_course_review' ),
					// '1.5'     => __( 'One & Half Star', 'wdm_ld_course_review' ),
					// '2.5'     => __( 'Two & Half Stars', 'wdm_ld_course_review' ),
					// '2'       => __( 'Two Stars', 'wdm_ld_course_review' ),
					// '3'       => __( 'Three Stars', 'wdm_ld_course_review' ),
					// '3.5'     => __( 'Three & Half Stars', 'wdm_ld_course_review' ),
					// '4'       => __( 'Four Stars', 'wdm_ld_course_review' ),
					// '4.5'     => __( 'Four & Half Stars', 'wdm_ld_course_review' ),
					// '5'       => __( 'Five Stars', 'wdm_ld_course_review' ),
					// ),
					// defaultCaption - additional config to change the default caption i.e., {rating} Stars.
					// starCaptions - the caption titles corresponding to each of the star rating selected - default shown below.

					/*
					{
					0.5: 'Half Star',
					1: 'One Star',
					1.5: 'One & Half Star',
					2: 'Two Stars',
					2.5: 'Two & Half Stars',
					3: 'Three Stars',
					3.5: 'Three & Half Stars',
					4: 'Four Stars',
					4.5: 'Four & Half Stars',
					5: 'Five Stars'
					}
					*/
					// clearValue - the value to clear the input to, when the clear button is clicked. Defaults to min if not set.
					// clearCaption - the caption displayed when clear button is clicked. Defaults to Not Rated.
					'hoverEnabled'                  => true,   // whether hover functionality is enabled.
					'hoverChangeCaption'            => true,   // control whether the caption should dynamically change on mouse hover.
					'hoverChangeStars'              => true,   // control whether the stars should dynamically change on mouse hover.
					'hoverOnClear'                  => true,   // whether to dynamically clear the rating on hovering the clear button.
					'showTotalReviews'              => true,   // whether to show ratings count after star rating.
					'allowReviewSubmission'         => false,  // controls whether to show `Leave a review` or `edit review` links.
					'hasSeparateReviewsSections'    => false, // set true on single pages to add scroll to review details section by clicking star ratings.
					'selectors'                     => apply_filters(
						'rrf_rating_selectors',
						array( 'h1', 'h1 > span', 'h2', 'h2 > span', 'h3', 'h3 > span', 'a' )
					),
				);
				require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
				$supported_locales = array( 'ar', 'bn', 'de', 'es', 'fa', 'fr', 'gr', 'it', 'kk', 'ko', 'pl', 'pt-BR', 'ro', 'ru', 'tr', 'ua', 'zh' );
				$wp_local_package = get_locale();
				$translations = wp_get_available_translations();
				if ( ! empty( $wp_local_package ) && isset( $translations[ $wp_local_package ] ) ) {
					if ( isset( $translations[ $wp_local_package ] ) ) {
						$language = $translations[ $wp_local_package ];
					}
				}
				if ( isset( $language ) && in_array( current( $language['iso'] ), $supported_locales ) ) {
					$rrf_ratings_settings['language'] = current( $language['iso'] ); // ISO code for the language selected. A list of supported locales is mentioned above in `$supported_locales` variable. In addition to this you can take the LANG.js file in star-rating-lib/js/locales as a template and create a translation for your own language.
				}

				$rrf_ratings_settings = apply_filters( 'rrf_default_rating_settings', $rrf_ratings_settings );
			}
		}
	}
	Ratings_Star_Display::get_instance();
}
