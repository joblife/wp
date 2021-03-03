<?php
/**
 * This file contains all the pluggable functions available from the plugin.
 *
 * @package RatingsReviewFeedback
 */

if ( ! function_exists( 'wdm_add_hook' ) ) {
	/**
	 * Adds all the actions/filters.
	 *
	 * @param [string]                     $hook     [action-hook].
	 * @param [string]                     $callback [class method].
	 * @param [CourseReviewNRating object] $scope    [class name or object instance].
	 * @param [array]                      $args     [type of hook, it's priority and number of arguments to the callback].
	 */
	function wdm_add_hook( $hook, $callback, $scope, $args = array() ) {
		$defaults = array(
			'type'      => 'action',
			'priority'  => 10,
			'num_args'  => 1,
		);

		$args = $args + $defaults;
		call_user_func_array( 'add_' . $args['type'], array( $hook, array( $scope, $callback ), $args['priority'], $args['num_args'] ) );
	}
}

if ( ! function_exists( 'rrf_get_all_courses' ) ) {
	/**
	 * This function will return all courses page.
	 *
	 * @since 2.0.0 This function replaces the wdmGetAllCourses function.
	 * @return array of posts.
	 */
	function rrf_get_all_courses() {
		$args = array(
			'orderby'           => 'date',
			'order'             => 'ASC',
			'post_type'         => 'sfwd-courses',
			'post_status'       => 'publish',
			'posts_per_page'    => -1,
		);

		return apply_filters( 'rrf_get_all_courses', get_posts( $args ) );
	}
}

if ( ! function_exists( 'rrf_get_all_course_reviews' ) ) {
	/**
	 * This function will return all the reviews of the provided course id.
	 *
	 * @version 2.0.0 [replaces the old wdmGetAllCourseReviews function].
	 * @param int   $course_id [course id].
	 * @param array $args [].
	 *
	 * @return object $posts     [array of post objects]
	 */
	function rrf_get_all_course_reviews( $course_id = 0, $args = array() ) {
		global $reviews_query;
		if ( empty( $course_id ) ) {
			$course_id = get_the_ID();
		}
		if ( empty( $course_id ) ) {
			return array();
		}
		$defaults = array(
			'posts_per_page'    => 100,
			'paged'             => 1,
			'post_type'         => 'wdm_course_review',
			'post_status'       => 'publish',
			'meta_key'          => 'wdm_course_review_review_rating', // for loading top rated reviews.
			'orderby'           => 'meta_value_num',
			'order'             => 'DESC',
			'meta_query'        => array(
				array(
					'key'       => 'wdm_course_review_review_on_course',
					'value'     => $course_id,
					'compare'   => '=',
				),
			),
		);
		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'rrf_get_all_course_reviews_args', $args );
		$reviews_query = new WP_Query();
		return apply_filters( 'rrf_get_all_course_reviews', $reviews_query->query( $args ) );
	}
}

if ( ! function_exists( 'rrf_get_course_rating_details' ) ) {

	/**
	 * This function will return course rating details i.e avg,total,etc.
	 *
	 * @version 2.0.0 [Replaces the wdmGetCourseRatingDetails function]
	 * @param int $course_id [course id].
	 *
	 * @return array $reviews_details [contains review details]
	 */
	function rrf_get_course_rating_details( $course_id = 0 ) {
		$reviews_details = array();

		$reviews_details['average_rating'] = 0.0;
		$reviews_details['total_count'] = 0;
		$reviews_details['max_stars'] = apply_filters( 'rrf_max_star_rating', 5 );
		$reviews_details['total_rating'] = 0;

		for ( $index = 1; $index <= $reviews_details['max_stars']; $index++ ) {
			$reviews_details['rating'][ $index ] = 0;
		}
		if ( empty( $course_id ) ) {
			$course_id = get_the_ID();
		}
		if ( empty( $course_id ) ) {
			return $reviews_details;
		}
		$args = array(
			'numberposts'   => -1,
			'post_type'     => 'wdm_course_review',
			'post_status'   => 'publish',
			'meta_query'    => array(
				array(
					'key'   => 'wdm_course_review_review_on_course', // wdm_course_review_review_rating.
					'value' => $course_id,
					// 'compare'   => '>',
				),
			),
		);

		$args = apply_filters( 'rrf_get_single_course_reviews_args', $args );

		$reviews = get_posts( $args );
		if ( $reviews ) {
			$reviews_details['total_count'] = count( $reviews );
			foreach ( $reviews as $review ) {
				$rating_value = intval( get_post_meta( $review->ID, 'wdm_course_review_review_rating', true ) );
				$reviews_details['total_rating'] += $rating_value;
				$reviews_details['rating'][ $rating_value ] = ( ( $reviews_details['rating'][ $rating_value ] ) + 1 );
			}
			$reviews_details['average_rating'] = floatval( $reviews_details['total_rating'] ) / floatval( $reviews_details['total_count'] );
			$reviews_details['average_rating'] = number_format( $reviews_details['average_rating'], 2, '.', '' );
		}

		return apply_filters( 'rrf_get_single_course_reviews_details', $reviews_details );
	}
}

if ( ! function_exists( 'rrf_load_star_rating_lib' ) ) {
	/**
	 * Loads the star rating library.
	 *
	 * @version 2.0.0 [replaces wdmLoadStarRatingLib function]
	 */
	function rrf_load_star_rating_lib() {
		global $rrf_ratings_settings;
		// wp_enqueue_style( 'bootstrap-css', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), WDM_LD_COURSE_VERSION );
		// wp_enqueue_style( 'bootstrap-css', plugins_url( 'includes/bootstrap/css/bootstrap.min.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'includes/bootstrap/css/bootstrap.min.css' ) ); // Modified Boostrap file with only required modules.
		wp_enqueue_style( 'fontawesome-css', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), WDM_LD_COURSE_VERSION );
		wp_enqueue_style( 'star-rating-css', plugins_url( 'includes/star-rating-lib/css/star-rating.min.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'includes/star-rating-lib/css/star-rating.min.css' ) );
		wp_enqueue_style( 'star-rating-theme-css', plugins_url( 'includes/star-rating-lib/themes/krajee-fa/theme.min.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'includes/star-rating-lib/themes/krajee-fa/theme.min.css' ) );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'star-rating-js', plugins_url( 'includes/star-rating-lib/js/star-rating.min.js', RRF_PLUGIN_FILE ), array( 'jquery' ), filemtime( RRF_PLUGIN_PATH . 'includes/star-rating-lib/js/star-rating.min.js' ) );
		wp_enqueue_script( 'star-rating-theme-js', plugins_url( 'includes/star-rating-lib/themes/krajee-fa/theme.min.js', RRF_PLUGIN_FILE ), array( 'star-rating-js' ), filemtime( RRF_PLUGIN_PATH . 'includes/star-rating-lib/themes/krajee-fa/theme.min.js' ) );
		if ( isset( $rrf_ratings_settings['language'] ) && ! empty( $rrf_ratings_settings['language'] ) ) {
			wp_enqueue_script( 'star-rating-locale', plugins_url( 'includes/star-rating-lib/js/locales/' . $rrf_ratings_settings['language'] . '.js', RRF_PLUGIN_FILE ), array( 'star-rating-js' ), filemtime( RRF_PLUGIN_PATH . 'includes/star-rating-lib/js/locales/' . $rrf_ratings_settings['language'] . '.js' ) );
		}
	}
}

if ( ! function_exists( 'rrf_load_jquery_modal_lib' ) ) {
	/**
	 * Loads the jquery modal library.
	 *
	 * @param boolean $footer [Whether to load the modal library in the footer].
	 * @version 2.0.0
	 */
	function rrf_load_jquery_modal_lib( $footer = false ) {
		if ( is_rtl() ) {
			wp_enqueue_style( 'jquery-modal-css', plugins_url( 'includes/jquery-modal/rtl/jquery.modal.min.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'includes/jquery-modal/rtl/jquery.modal.min.css' ) );
		} else {
			wp_enqueue_style( 'jquery-modal-css', plugins_url( 'includes/jquery-modal/jquery.modal.min.css', RRF_PLUGIN_FILE ), array(), filemtime( RRF_PLUGIN_PATH . 'includes/jquery-modal/jquery.modal.min.css' ) );
		}
		wp_enqueue_script( 'jquery-modal-js', plugins_url( 'includes/jquery-modal/jquery.modal.min.js', RRF_PLUGIN_FILE ), array( 'jquery' ), filemtime( RRF_PLUGIN_PATH . 'includes/jquery-modal/jquery.modal.min.js' ), $footer );
	}
}

if ( ! function_exists( 'IND_money_format' ) ) {
	/**
	 * Convert number to IND money format e.g 11000 to 11,000.
	 *
	 * @param int $money [integer amount].
	 */
    // phpcs:disable
    function IND_money_format($money = 0)
    {
        $len = strlen($money);
        $temp_m = '';
        $money = strrev($money);
        for ($loop_i = 0; $loop_i < $len; ++$loop_i) {
            if ((3 == $loop_i || ($loop_i > 3 && ($loop_i - 1) % 2 == 0)) && $loop_i != $len) {
                $temp_m .= ',';
            }
            $temp_m .= $money[ $loop_i ];
        }
        return strrev($temp_m);
    }
    // phpcs:enable
}

if ( ! function_exists( 'rrf_get_helpful_message' ) ) {
	/**
	 * Gets the message shown for helpful review.
	 *
	 * @version 2.0.0 [replaces wdmGetHelpfulMessage function]
	 * @param int $count [Count i.e., Number of likes].
	 * @return string $message [returns Helpful message text].
	 */
	function rrf_get_helpful_message( $count = 0 ) {
		$message = '';
		if ( 1 == $count ) {
			$message = __( 'One person found this helpful. ', 'wdm_ld_course_review' );
		} elseif ( $count > 1 ) {
			/* translators: %d: Number of People. */
			$message = sprintf( __( '%d people found this helpful.', 'wdm_ld_course_review' ), $count );
		}
		return $message;
	}
}

if ( ! function_exists( 'rrf_get_all_user_reviews' ) ) {
	/**
	 * This will return all the publish reviews of the user with course_id as a key.
	 *
	 * @param  integer $user_id [user ID].
	 * @version 2.0.0 [This function replaces wdmGetAllReviewsOfUser function]
	 * @return array $review_n_course [this will return reivew details with course id as a key]
	 */
	function rrf_get_all_user_reviews( $user_id = 0 ) {
		$review_n_course = array();
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( empty( $user_id ) ) {
			return $review_n_course;
		}
		$args = array(
			'numberposts' => -1,
			'post_type' => 'wdm_course_review',
			'post_status' => array( 'publish', 'pending', 'draft', 'trash', 'private', 'rejected' ), // || 'any'
			'author' => $user_id,
		);

		$reviews = get_posts( $args );
		if ( $reviews ) {
			foreach ( $reviews as $review ) {
				$rating_value = get_post_meta( $review->ID, 'wdm_course_review_review_rating', true );
				$course_id = get_post_meta( $review->ID, 'wdm_course_review_review_on_course', true );
				$review_n_course[ $course_id ]['review_title'] = $review->post_title;
				$review_n_course[ $course_id ]['review_desc'] = $review->post_content;
				$review_n_course[ $course_id ]['review_value'] = $rating_value;
				$review_n_course[ $course_id ]['allow_edit'] = false;
				if ( 'publish' == $review->post_status || 'rejected' == $review->post_status ) {
					$review_n_course[ $course_id ]['allow_edit'] = true;
				}
			}
		}

		return $review_n_course;
	}
}

if ( ! function_exists( 'rrf_get_user_course_review_id' ) ) {
	/**
	 * Return review id of user.
	 *
	 * @version 2.0.0 [This function replaces wdmGetReviewIdOfCourse function]
	 * @param int $user_id   [user id].
	 * @param int $course_id [course id].
	 *
	 * @return object [return review post object if found else 0]
	 */
	function rrf_get_user_course_review_id( $user_id = 0, $course_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( empty( $user_id ) ) {
			return array();
		}
		$args = array(
			'numberposts'   => 1,
			'post_type'     => 'wdm_course_review',
			'post_status'   => array( 'publish', 'pending', 'private' ), // || 'any' //'draft', 'trash',//, 'rejected'
			'author'        => $user_id,
			'orderby'       => 'date',
			'order'         => 'DESC',
			'meta_query'    => array(
				array(
					'key'       => 'wdm_course_review_review_on_course',
					'value'     => $course_id,
					'compare'   => '=',
				),
			),
		);
		$reviews = get_posts( $args );
		if ( $reviews ) {
			return current( $reviews );
		}
		return array();
	}
}

if ( ! function_exists( 'rrf_add_capabilities' ) ) {
	/**
	 * Assign given capabilities to given role.
	 *
	 * @version 2.0.0 [This function replaces wdmAddCapabilities]
	 * @param array $role_ids     [role ids].
	 * @param array $capabilities [array of capabilities].
	 */
	function rrf_add_capabilities( $role_ids = array(), $capabilities = array() ) {
		foreach ( $role_ids as $role_id ) {
			$role_obj = get_role( $role_id );
			if ( ! is_null( $role_obj ) ) {
				foreach ( $capabilities as $cap ) {
					if ( $role_obj->has_cap( $cap ) ) {
						continue;
					}
					$role_obj->add_cap( $cap );
				}
			}
		}
	}
}

if ( ! function_exists( 'rrf_clean_data' ) ) {
	/**
	 * This function is used to clean the string data passed to it.
	 *
	 * @version 2.0.0 [This function replaces wdmCleanData].
	 * @param  string $data [Data to process].
	 * @return string $data  Processed Data
	 */
	function rrf_clean_data( $data ) {
		$data = trim( $data );
		$data = stripslashes( $data );
		$data = htmlspecialchars( $data );

		return $data;
	}
}

if ( ! function_exists( 'rrf_check_if_post_set' ) ) {
	/**
	 * Function to check post is set or not.
	 *
	 * @param object $object Object to check.
	 * @param string $key Search Key.
	 * @version 2.0.0 [replaces wdmRRCheckIsSet]
	 */
	function rrf_check_if_post_set( $object, $key ) {
		if ( isset( $object[ $key ] ) ) {
			return $object[ $key ];
		}

		return '';
	}
}

if ( ! function_exists( 'rrf_display_pagination' ) ) {
	/**
	 * Displays pagination.
	 *
	 * @version 2.0.0 replaces wdm_pagination function.
	 * @param string $numpages  [number of pages].
	 * @param string $pagerange [page range].
	 * @param string $paged     [current paged number].
	 */
	function rrf_display_pagination( $numpages = '', $pagerange = '', $paged = '' ) {
		if ( empty( $pagerange ) ) {
			$pagerange = 2;
		}

		/**
		 * This first part of our function is a fallback
		 * for custom pagination inside a regular loop that
		 * uses the global $paged and global $wp_query variables.
		 *
		 * It's good because we can now override default pagination
		 * in our theme, and use this function in default quries
		 * and custom queries.
		 */
		if ( empty( $paged ) ) {
			$paged = 1;
		}
		if ( '' == $numpages ) {
			global $wp_query;
			$numpages = $wp_query->max_num_pages;
			if ( ! $numpages ) {
				$numpages = 1;
			}
		}

		  /*
		* We construct the pagination arguments to enter into our paginate_links
		* function.
		*/
		$pagination_args = array(
			'base'          => get_pagenum_link( 1 ) . '%_%',
			'format'        => 'page/%#%/', // '?paged=%#%' | 'page/%#%'
			'total'         => $numpages,
			'current'       => $paged,
			'show_all'      => false,
			'end_size'      => 1,
			'mid_size'      => $pagerange,
			'prev_next'     => true,
			'prev_text'     => __( '&laquo;' ),
			'next_text'     => __( '&raquo;' ),
			'type'          => 'plain',
			'add_args'      => false,
			'add_fragment'  => '',
		);

		$paginate_links = paginate_links( $pagination_args );

		if ( $paginate_links ) {
			echo "<nav class='wdm-reviews-pagination'>";
			echo $paginate_links;// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</nav>';
		}
	}
}

if ( ! function_exists( 'rrf_get_course_label' ) ) {
	/**
	 * Return course label.
	 *
	 * @version 2.0.0 [Replaces wdmGetCourseLabel].
	 * @param string $label [course or courses].
	 *
	 * @return string $label [label]
	 */
	function rrf_get_course_label( $label = 'course' ) {
		$ld_label = __( 'Course', 'wdm_ld_course_review' );
		if ( class_exists( 'LearnDash_Custom_Label' ) ) {
			return LearnDash_Custom_Label::get_label( $label );
		}
		if ( 'courses' == $label ) {
			$ld_label = __( 'Courses', 'wdm_ld_course_review' );
		}

		return $ld_label;
	}
}

if ( ! function_exists( 'rrf_save_meta_field_val' ) ) {
	/**
	 * Updating meta fields.
	 *
	 * @version 2.0.0 [replaces wdmRRFSaveMetaFieldVal]
	 * @param int   $post_id [post id].
	 * @param array $my_type [meta field types].
	 */
	function rrf_save_meta_field_val( $post_id, $my_type ) {
		foreach ( $my_type as $field ) {
			if ( isset( $field['disabled'] ) && $field['disabled'] ) {
				continue;
			}
			$old = get_post_meta( $post_id, $field['id'], true );
			if ( isset( $_REQUEST[ $field['id'] ] ) ) {
				$new = sanitize_key( $_REQUEST[ $field['id'] ] );
				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $field['id'], $new );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $field['id'], $old );
				}
			} else {
				delete_post_meta( $post_id, $field['id'], $old );
			}
		}
	}
}

if ( ! function_exists( 'rrf_can_user_post_reviews' ) ) {
	/**
	 * Function to check if the user is allowed to rate the course or not.
	 *
	 * @version 2.0.0 [replaces wdmIsUserAllowedToRate]
	 * @param int $user_id   user id.
	 * @param int $course_id course id.
	 *
	 * @return bool $is_completed     true if allowed
	 */
	function rrf_can_user_post_reviews( $user_id = 0, $course_id = 0 ) {
		if ( empty( $course_id ) || ( empty( $user_id ) && ! is_user_logged_in() ) ) {
			return false;
		}
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$setting = get_post_meta( $course_id, 'wdm_rating_review_setting', true );
		$setting = rrf_check_review_setting( $setting, $course_id );
		if ( current_user_can( 'publish_wdm_course_reviews' ) ) {
			return true;
		}

		if ( ! sfwd_lms_has_access_fn( $course_id, $user_id ) ) {
			return false;
		}
		if ( 0 === $setting ) {
			return apply_filters( 'wdm_course_review_allowed', true );
		}
		$post_type = get_post_type( $setting );
		$is_completed = false;
		switch ( $post_type ) {
			case 'sfwd-courses':
					$is_completed = learndash_course_completed( $user_id, $setting );

				break;
			case 'sfwd-lessons':
					$is_completed = learndash_is_lesson_complete( $user_id, $setting );

				break;
			case 'sfwd-topic':
					$is_completed = learndash_is_topic_complete( $user_id, $setting );

				break;
			case 'sfwd-quiz':
					$is_completed = learndash_is_quiz_complete( $user_id, $setting );

				break;
		}

		return apply_filters( 'wdm_course_review_allowed', $is_completed );
	}
}

if ( ! function_exists( 'rrf_check_review_setting' ) ) {
	/**
	 * Checking if the post is still published or not.
	 *
	 * @version 2.0.0 [replaces wdmCheckReviewSetting]
	 * @param  integer $setting   [post id].
	 * @param  integer $course_id [course id].
	 */
	function rrf_check_review_setting( $setting = 0, $course_id = 0 ) {
		if ( empty( $setting ) ) {
			return 0;
		}

		$post_obj = get_post( $setting );
		if ( ! $post_obj || 'publish' != $post_obj->post_status ) {
			update_post_meta( $course_id, 'wdm_rating_review_setting', 0 );
			return 0;
		}
		return $setting;
	}
}
if ( ! function_exists( 'rrf_get_star_html_struct' ) ) {
	/**
	 * This function will return HTML structure of star lib.
	 *
	 * @version 2.0.0 [replaces wdmGetStarHTMLStruct]
	 * @param int   $course_id [course id].
	 * @param float $value     [current value of star which will be used to display intial value].
	 * @param array $args      = array().
	 * @return [type] [description]
	 */
	function rrf_get_star_html_struct( $course_id = 0, $value = 0.0, $args = array() ) {
		global $rrf_ratings_settings;
		$defaults = $rrf_ratings_settings;
		$args = wp_parse_args( $args, $defaults );
		$attr = '';
		foreach ( $args as $attr_name => $attr_value ) {
			if ( is_array( $attr_value ) ) {
				$attr_value = json_encode( $attr_value );
			}
			$attr .= ' data-' . $attr_name . '=\'';
			if ( true === $attr_value ) {
				$attr .= 'true\'';
			} elseif ( false === $attr_value ) {
				$attr .= 'false\'';
			} else {
				$attr .= $attr_value . '\'';
			}
		}

		$html_struct = '<input data-id=\'input-' . ( intval( $course_id ) ) . '-xs\' class=\'rating rating-loading wdm-crr-star-input\' value=\'' . floatval( $value ) . '\'' . $attr . '/>';

		return $html_struct;
	}
}

if ( ! function_exists( 'rrf_get_bar_values' ) ) {
	/**
	 * The function fetches the values used for bar chart display.
	 *
	 * @version 2.0.0
	 * @param  array $rating_details [description].
	 * @return array $result                 [description]
	 */
	function rrf_get_bar_values( $rating_details = array() ) {
		for ( $i = 1; $i <= 5; $i++ ) {
			$result[ $i ] = array(
				'value' => 0,
				'percentage' => 0,
			);
		}
		if ( empty( $rating_details ) || empty( $rating_details['rating'] ) ) {
			return $result;
		}
		krsort( $rating_details['rating'] );
		// $total_count = count( $rating_details['rating'] );
		$ratings_total_count = $rating_details['total_count'];
		if ( 0 === $ratings_total_count ) {
			return $result;
		}
		foreach ( $rating_details['rating'] as $rating => $value ) {
			$result[ $rating ] = array(
				'value' => $value,
				'percentage' => ( $value / $ratings_total_count ) * 100,
			);
		}
		return $result;
	}
}

if ( ! function_exists( 'update_review_helpful_meta' ) ) {
	/**
	 * This function is used to update the was helpful feature information.
	 *
	 * @version 2.0.0 [replaces updateReviewHelpfulMeta]
	 * @param  integer $user_id   [User ID].
	 * @param  integer $review_id [Review Post ID].
	 * @param  string  $answer    [yes/no].
	 */
	function update_review_helpful_meta( $user_id, $review_id, $answer ) {
		$user_meta_key = 'wdm_helpful_answers';
		$wdm_helpful_answers = get_user_meta( $user_id, $user_meta_key, true );
		$postmeta_key = 'wdm_helpful_yes';
		if ( empty( $wdm_helpful_answers ) ) {
			$wdm_helpful_answers = array();
		}
		// check if answer is same.
		if ( ! isset( $wdm_helpful_answers[ $review_id ] ) || $wdm_helpful_answers[ $review_id ] != $answer ) {
			$count = get_post_meta( $review_id, $postmeta_key, true );
			if ( empty( $count ) ) {
				$count = 0;
			}
			if ( 'yes' === $answer ) {
				$count++;
			} else {
				$count--;
			}
			update_post_meta( $review_id, $postmeta_key, $count );
			$wdm_helpful_answers[ $review_id ] = $answer;
			update_user_meta( $user_id, $user_meta_key, $wdm_helpful_answers );
		}
	}
}

if ( ! function_exists( 'rrf_load_custom_comment_template' ) ) {
	/**
	 * This function loads custom template for displaying comments.
	 *
	 * @version 2.0.0
	 * @param  [WP_Post object] $comment [single comment].
	 * @param  [Array]          $args    [Array of args].
	 */
	function rrf_load_custom_comment_template( $comment, $args ) {
		?>
		<div <?php comment_class( empty( $args['has_children'] ) ? 'review-comment-list' : 'review-comment-list parent' ); ?> id="comment-<?php comment_ID(); ?>">
			<div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
				<div class="review-head">
					<div class="review-author-info">
						<span class="review-author-img-wrap">
							<?php if ( 0 != $args['avatar_size'] ) : ?>
								<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
							<?php endif; ?>
						</span>
						<strong class="review-author-name" title=""><?php echo get_comment_author_link(); ?></strong>
					</div> <!-- .review-author-info closing -->
					<span class="wdm-review-age">
						<?php
						/* translators: %s : human-readable time difference */
						echo esc_html( sprintf( _x( 'Posted %s ago', '%s = human-readable time difference', 'wdm_ld_course_review' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ) );
						?>
					</span>
				</div>
				<div class="review-body">
					<div class="review-desc"><?php comment_text(); ?></div>
				</div>
				<div class="review-footer">
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'rrf_is_feedback_form_enabled' ) ) {
	/**
	 * Check if the feedback form is enabled on course or not.
	 *
	 * @version 2.0.0 [replaces wdmIsFeedbackFormEnabled]
	 * @param int $course_id [course id].
	 *
	 * @return bool $is_enabled [return true if enabled]
	 */
	function rrf_is_feedback_form_enabled( $course_id = 0 ) {
		if ( empty( $course_id ) ) {
			$course_id = get_the_ID();
		}
		if ( empty( $course_id ) ) {
			return false;
		}
		$is_enabled = false;
		$course_setting = get_post_meta( $course_id, 'wdm_course_feedback_setting', true );
		// checking global setting.
		if ( empty( $course_setting ) ) {
			$global_setting = get_option( 'wdm_course_feedback_setting', 1 );
			if ( ! empty( $global_setting ) ) {
				$is_enabled = true;
			}
		} elseif ( 1 == $course_setting ) { // checking course setting.
			$is_enabled = true;
		}

		return apply_filters( 'wdm_is_feedback_form_enabled', $is_enabled, $course_id );
	}
}

if ( ! function_exists( 'rrf_is_user_submitted_feedback_form' ) ) {
	/**
	 * Check if the user has already submitted the form for the course or not.
	 *
	 * @version 2.0.0 [replaces wdmIsUserSubmittedFeedbackForm]
	 * @param int $user_id   [user id].
	 * @param int $course_id [course id].
	 *
	 * @return bool $is_submitted [return true if already submitted]
	 */
	function rrf_is_user_submitted_feedback_form( $user_id = 0, $course_id = 0 ) {
		$is_submitted = true;
		if ( empty( $course_id ) ) {
			$course_id = get_the_ID();
		}
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( empty( $course_id ) || empty( $user_id ) ) {
			$is_submitted = false;
		}
		$feedback = rrf_get_course_feedback_id( $user_id, $course_id );
		if ( empty( $feedback ) ) {
			$is_submitted = false;
		}
		$is_submitted = apply_filters( 'wdm_is_user_submitted_feedback_form', $is_submitted, $user_id, $course_id );

		return $is_submitted;
	}
}

if ( ! function_exists( 'rrf_get_course_feedback_id' ) ) {
	/**
	 * Return feedback of the user of provided course id.
	 *
	 * @version 2.0.0 [replaces wdmGetFeedbackIdOfCourse]
	 * @param int $user_id   [user id].
	 * @param int $course_id [course id].
	 *
	 * @return object [return feedback post object if found else 0]
	 */
	function rrf_get_course_feedback_id( $user_id = 0, $course_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( empty( $user_id ) ) {
			return 0;
		}
		$args = array(
			'numberposts' => 1,
			'post_type' => 'wdm_course_feedback',
			'post_status' => array( 'publish', 'pending', 'draft', 'trash', 'private' ), // || 'any'
			'author' => $user_id,
			'orderby' => 'date',
			'order' => 'DESC',
			'meta_query' => array(
				array(
					'key' => 'wdm_course_feedback_feedback_on_course',
					'value' => $course_id,
					'compare' => '=',
				),
			),
		);

		$feedback = get_posts( $args );
		if ( $feedback ) {
			return $feedback[0];
		}

		return 0;
	}
}

if ( ! function_exists( 'rrf_get_instructor_course_ids' ) ) {
	/**
	 * Return course post object of instructor role.
	 *
	 * @version 2.0.0 [replaces wdmGetIRCourseIDs]
	 * @param int $user_id [user id].
	 *
	 * @return object $posts   [return posts of provided author ]
	 */
	function rrf_get_instructor_course_ids( $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		if ( empty( $user_id ) ) {
			return array();
		}

		$args = array(
			'orderby' => 'date',
			'order' => 'ASC',
			'post_type' => 'sfwd-courses',
			'post_status' => 'any',
			'posts_per_page' => -1,
			'author' => $user_id,
			'fields' => 'ids',
		);

		$posts = get_posts( $args );
		if ( empty( $posts ) ) {
			return array();
		}

		return $posts;
	}
}
