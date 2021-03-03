<?php
/**
 * This file contains all the deprecated functions of the plugin.
 * @SuppressWarnings("unused")
 * @package RatingsReviewFeedback
 */
// phpcs:ignoreFile
if ( ! function_exists( 'wdmGetAllCourses' ) ) {
	/**
	 * This function will return all courses page.
	 *
	 * @deprecated 2.0.0 This function is deprecated from version 2.0.0 and will be removed in an upcoming update.
	 * @see rrf_get_all_courses Use this function to get all courses.
	 *
	 * @return array of posts.
	 */
	function wdmGetAllCourses() { 
		return rrf_get_all_courses();		
	}
}

if ( ! function_exists( 'wdmGetAllCourseReviews' ) ) {
	/**
	 * This function will return all the reviews of the provided course id.
	 *
	 * @deprecated 2.0.0 This function is deprecated from version 2.0.0 and will be removed in an upcoming update.
	 * @see rrf_get_all_course_reviews Use this function to get all reviews of a course.
	 * @param int $course_id [course id].
	 * @param int $posts_per_page [No of Reviews].
	 * @param int $paged [Page Number].
	 *
	 * @return object $posts     [array of post objects]
	 */
	function wdmGetAllCourseReviews( $course_id = 0, $posts_per_page = 100, $paged = 1 ) {
		return rrf_get_all_course_reviews(
			$course_id,
			array(
				'posts_per_page' => $posts_per_page,
				'paged' => $paged
			)
		);
	}
}

if ( ! function_exists( 'wdmGetCourseRatingDetails' ) ) {

	/**
	 * This function will return course rating details i.e avg,total,etc.
	 *
	 * @deprecated 2.0.0 This function is deprecated from version 2.0.0 and will be removed in an upcoming update.
	 * @see rrf_get_course_rating_details Use this function to get review details of a course.
	 * @param int $course_id [course id].
	 *
	 * @return array $reviews_details [contains review details]
	 */
	function wdmGetCourseRatingDetails( $course_id = 0 ) {
		return rrf_get_course_rating_details($course_id);
	}
}


if ( ! function_exists( 'wdmLoadStarRatingLib' ) ) {
	/**
	 * Loading star rating library.
	 * 
	 * @deprecated 2.0.0 This function is deprecated from version 2.0.0 and will be removed in an upcoming update.
	 * @see rrf_load_star_rating_lib Use this function to load the star rating library.
	 */
	function wdmLoadStarRatingLib() {
		// Registering, scripts and styles here
		rrf_load_star_rating_lib();
	}
}

if ( ! function_exists( 'wdmGetStarHTMLStruct' ) ) {
	/**
	 * This function will return HTML structure of star lib.
	 *
	 * @deprecated 2.0.0 This function is deprecated from version 2.0.0 and will be removed in an upcoming update.
	 * @see rrf_get_star_html_struct Use this function to get the star html struct.
	 */
	function wdmGetStarHTMLStruct( $course_id = 0, $value = 0.0, $args = array() ) {
		return rrf_get_star_html_struct( $course_id, $value, $args );
	}
}

if ( ! function_exists( 'wdmGetBarReviewHTML' ) ) {
	/**
	 * Generating HTML structure for showing reviews in bar.
	 *
	 * @param array $rating_details [rating details]
	 * @param int   $course_id      [course id]
	 *
	 * @return string $html           [bar HTML structure]
	 */
	function wdmGetBarReviewHTML( $rating_details = array(), $course_id = 0 ) {
		$rating_details = $rating_details;
		$course_id = $course_id;
		return '';
	}
}

if ( ! function_exists( 'wdmLoadBarChartLib' ) ) {
	/**
	 * Loading bar char library.
	 */
	function wdmLoadBarChartLib() {
		// Registering, scripts and styles here
		return;
	}
}
if ( ! function_exists( 'wdmGetHelpfulMessage' ) ) {

	/**
	 * Will return rating course URL.
	 *
	 * @deprecated 2.0.0 This function is deprecated from version 2.0.0 and will be removed in an upcoming update.
	 * @see rrf_get_helpful_message Use this function to load the star rating library.
	 * @param int $course_id [course id]
	 *
	 * @return string $rating_URL [contains rating URL]
	 */
	function wdmGetHelpfulMessage( $count = 0 ) {
		return rrf_get_helpful_message($count);
	}
}
if ( ! function_exists( 'wdmGetCourseRatingPageURL' ) ) {

	/**
	 * Will return rating course URL.
	 *
	 * @param int $course_id [course id]
	 *
	 * @return string $rating_URL [contains rating URL]
	 */
	function wdmGetCourseRatingPageURL( $course_id = 0 ) {
		$course_id = $course_id;
		return '';
	}
}

if ( ! function_exists( 'wdmGetCourseReviewsPageURL' ) ) {
	/**
	 * Will return course reviews URL.
	 *
	 * @param int $course_id [course id]
	 *
	 * @return string $reviews_URL [contains course reviews URL]
	 */
	function wdmGetCourseReviewsPageURL( $course_id = 0 ) {
		$course_id = $course_id;
		return '';
	}
}

if ( ! function_exists( 'wdmGetCourseRatingReviewHTML' ) ) {
	/**
	 * Generating HTML structure which will get displayed on course rating page.
	 *
	 * @param array $course_ids       [contains course ids]
	 * @param array $all_user_reviews [contains reviews of user if already given]
	 *
	 * @return string $rating_review_html [contains HTML structure of rating course]
	 */
	function wdmGetCourseRatingReviewHTML( $course_ids = array(), $all_user_reviews = array() ) {
		$course_ids = $course_ids;
		$all_user_reviews = $all_user_reviews;
		return '';
	}
}

if ( ! function_exists( 'wdmGetAllReviewsOfUser' ) ) {
	/**
	 * This will return all the publish reviews of the user with course_id as a key.
	 *
	 * @deprecated 2.0.0 [This function is deprecated from version 2.0.0 and will be removed in an upcoming update.]
	 * @see rrf_get_all_user_reviews Use this function to get all the reviews posted by this user.
	 * @param int $user_id [user id]
	 *
	 * @return array $review_n_course [this will return reivew details with course id as a key]
	 */
	function wdmGetAllReviewsOfUser( $user_id = 0 ) {
		return rrf_get_all_user_reviews($user_id);
	}
}

if ( ! function_exists( 'wdmGetReviewIdOfCourse' ) ) {
	/**
	 * Return review id of user.
	 *
	 * @deprecated 2.0.0 [This function is deprecated from version 2.0.0 and will be removed in an upcoming update.]
	 * @see rrf_get_user_course_review_id Use this function to get the review posted by a user for a particular course.
	 * @param int $user_id   [user id]
	 * @param int $course_id [course id]
	 *
	 * @return object [return review post object if found else 0]
	 */
	function wdmGetReviewIdOfCourse( $user_id = 0, $course_id = 0 ) {
		return rrf_get_user_course_review_id($user_id, $course_id);
	}
}

if ( ! function_exists( 'wdmAddCapabilities' ) ) {
	/**
	 * Assign given capabilities to given role.
	 * 
	 * @deprecated 2.0.0 [This function is deprecated from version 2.0.0 and will be removed in an upcoming update.]
	 * @see rrf_add_capabilities [Assign given capabilities to given role.]
	 * @param array $role_ids     [role ids]
	 * @param array $capabilities [array of capabilities]
	 */
	function wdmAddCapabilities( $role_ids = array(), $capabilities = array() ) {
		rrf_add_capabilities($role_ids, $capabilities);
	}
}

if ( ! function_exists( 'wdmCleanData' ) ) {
	/**
	 * This function is used to clean the string data passed to it.
	 *
	 * @deprecated 2.0.0 [This function is deprecated from version 2.0.0 and will be removed in an upcoming update.]
	 * @see  rrf_clean_data [This function is used to clean the string data passed to it]
	 * @param  string $data [Data to process].
	 * @return string $data  Processed Data
	 */
	function wdmCleanData( $data ) {
		return rrf_clean_data($data);
	}
}

if ( ! function_exists( 'wdmRRCheckIsSet' ) ) {
	/**
	 * Function to check post is set or not.
	 * @deprecated 2.0.0
	 * @see  rrf_check_if_post_set
	 */
	function wdmRRCheckIsSet( $object, $key ) {
		return rrf_check_if_post_set($object, $key);
	}
}

if ( ! function_exists( 'wdm_pagination' ) ) {
	/**
	 * Displays pagination.
	 *
	 * @deprecated 2.0.0
	 * @see  rrf_display_pagination [Displays pagination.]
	 * @param string $numpages  [number of pages]
	 * @param string $pagerange [page range]
	 * @param string $paged     [current paged number]
	 */
	function wdm_pagination( $numpages = '', $pagerange = '', $paged = '' ) {
		rrf_display_pagination($numpages, $pagerange, $paged );
	}
}

if ( ! function_exists( 'wdmRRSelected' ) ) {
	function wdmRRSelected( $oldVal, $newVal ) {
		if ( $oldVal == $newVal ) {
			return 'selected';
		}

		return '';
	}
}

if ( ! function_exists( 'wdmIsUserAllowedToRate' ) ) {
	/**
	 * Function to check if the user is allowed to rate the course or not.
	 *
	 * @deprecated 2.0.0
	 * @see  rrf_can_user_post_reviews [can user post ratings.]
	 * @param int $user_id   user id
	 * @param int $course_id course id
	 *
	 * @return bool $isCompleted     true if allowed
	 */
	function wdmIsUserAllowedToRate( $user_id = 0, $course_id = 0 ) {
		return rrf_can_user_post_reviews($user_id, $course_id);
	}
}

if ( ! function_exists( 'wdmIsFeedbackFormEnabled' ) ) {
	/**
	 * Check if the feedback form is enabled on course or not.
	 *  
	 * @deprecated 2.0.0
	 * @see rrf_is_feedback_form_enabled [can user post feedback for this course]
	 * @param int $course_id [course id]
	 *
	 * @return bool $isEnabled [return true if enabled]
	 */
	function wdmIsFeedbackFormEnabled( $course_id = 0 ) {
		return rrf_is_feedback_form_enabled($course_id);
	}
}

if ( ! function_exists( 'wdmIsUserSubmittedFeedbackForm' ) ) {
	/**
	 * Check if the user has already submitted the form for the course or not.
	 *
	 * @deprecated 2.0.0
	 * @see rrf_is_user_submitted_feedback_form [check if user already posted feedback].
	 * @param int $user_id   [user id]
	 * @param int $course_id [course id]
	 *
	 * @return bool $isSubmitted [return true if already submitted]
	 */
	function wdmIsUserSubmittedFeedbackForm( $user_id = 0, $course_id = 0 ) {
		return rrf_is_user_submitted_feedback_form($user_id, $course_id);
	}
}

if ( ! function_exists( 'wdmGetFeedbackIdOfCourse' ) ) {
	/**
	 * Return feedback of the user of provided course id.
	 *
	 * @deprecated 2.0.0
	 * @see rrf_get_course_feedback_id [return user submitted feedback].
	 * @param int $user_id   [user id]
	 * @param int $course_id [course id]
	 *
	 * @return object [return feedback post object if found else 0]
	 */
	function wdmGetFeedbackIdOfCourse( $user_id = 0, $course_id = 0 ) {
		return rrf_get_course_feedback_id($user_id, $course_id);
	}
}

if ( ! function_exists( 'wdmGetIRCourseIDs' ) ) {
	/**
	 * Return course post object of instructor role.
	 *
	 * @deprecated 2.0.0
	 * @see rrf_get_instructor_course_ids [Get Instructor's Course IDs]
	 * @param int $user_id [user id]
	 *
	 * @return object $posts   [return posts of provided author ]
	 */
	function wdmGetIRCourseIDs( $user_id = 0 ) {
		return rrf_get_instructor_course_ids($user_id);
	}
}

if ( ! function_exists( 'wdmGetCourseLabel' ) ) {
	/**
	 * Return course label.
	 *
	 * @deprecated 2.0.0
	 * @see rrf_get_course_label
	 * @param string $label [course or courses]
	 *
	 * @return string $label [label]
	 */
	function wdmGetCourseLabel( $label = 'course' ) {
		return rrf_get_course_label($label);
	}
}

if ( ! function_exists( 'wdmRRFSaveMetaFieldVal' ) ) {
	/**
	 * Updating meta fields.
	 *
	 * @deprecated 2.0.0
	 * @see rrf_save_meta_field_val [updating meta fields]
	 * @param int   $post_id [post id]
	 * @param array $my_type [meta field types]
	 */
	function wdmRRFSaveMetaFieldVal( $post_id, $my_type ) {
		rrf_save_meta_field_val($post_id, $my_type);
	}
}


if ( ! function_exists( 'wdmCheckReviewSetting' ) ) {
	/**
	 * Checking if the post is still published or not.
	 *
	 * @deprecated 2.0.0.
	 * @see rrf_check_review_setting [Checking if the post is still published or not]
	 * @param  integer $setting   [post id]
	 * @param  integer $course_id [course id]
	 * @param  integer $setting   [post id]
	 */
	function wdmCheckReviewSetting( $setting = 0, $course_id = 0 ) {
		return rrf_check_review_setting($setting, $course_id);
	}
}

if ( ! function_exists( 'wdmptAddGetParameter' ) ) {
	function wdmptAddGetParameter( $url, $varName, $value ) {
		$url = $url;
		$varName = $varName;
		$value = $value;
		return '';
	}
}

if ( ! function_exists( 'updateReviewHelpfulMeta' ) ) {
	/**
	 * This function is used to update the was helpful? feature information.
	 *
	 * @deprecated 2.0.0.
	 * @see update_review_helpful_meta.
	 * @param  integer $user_id   [User ID].
	 * @param  integer $review_id [Review Post ID].
	 * @param  string  $answer    [yes/no].
	 */
	function updateReviewHelpfulMeta( $user_id, $review_id, $answer ) {
		update_review_helpful_meta( $user_id, $review_id, $answer );
	}
}
