<?php
/**
 * This file is the template for adding review details.
 *
 * @package RatingsReviewsFeedback\Public\Reviews
 *
 * $step_number : Step Count
 * $step_type : Type of step i.e., add/edit/delete in this case it will always be add.
 * $steps : Registered steps.
 * $stars : Number of stars.
 */

?>
<div class="rrf-modal-content modal review-details" data-course_id="<?php echo esc_attr( $course_id ); ?>" data-step="<?php echo esc_attr( $step_number ); ?>" data-steptype="<?php echo esc_attr( $step_type ); ?>">
	<?php
	global $rrf_ratings_settings;
	$rrf_ratings_settings['displayOnly'] = false;
	$rrf_ratings_settings['size'] = 'lg';
	$rrf_ratings_settings['showClear'] = false;
	$rrf_ratings_settings['starCaptions'] = array(
		1 => __( 'Awful, not what I expected at all', 'wdm_ld_course_review' ),
		2 => __( 'Poor, pretty disappointed', 'wdm_ld_course_review' ),
		3 => __( 'Average, could be better', 'wdm_ld_course_review' ),
		4 => __( 'Good, what I expected', 'wdm_ld_course_review' ),
		5 => __( 'Amazing, above expectations!', 'wdm_ld_course_review' ),
	);
	$input_id = 'input-' . $course_id . '-rrf';
	$user_id = get_current_user_id();
	$review = rrf_get_user_course_review_id( $user_id, $course_id );
	if ( empty( $review ) ) {
		$review_title = '';
		$review_description = '';
	} else {
		$review_title = $review->post_title;
		$review_description = $review->post_content;
		if ( empty( $stars ) ) {
			$stars = intval( get_post_meta( $review->ID, 'wdm_course_review_review_rating', true ) );
		}
	}
	?>
	<div class="modal-container">
		<div class="prompt-text"><?php echo esc_html__( 'Why did you leave this rating?', 'wdm_ld_course_review' ); ?></div>
		<input type="hidden" class="rating-settings" value='<?php echo json_encode( $rrf_ratings_settings ); ?>'>
		<input data-id="<?php echo esc_attr( $input_id ); ?>" class="rating rating-loading" value="<?php echo esc_attr( $stars ); ?>">
		<div class="review-title review-headline">
			<input type="text" maxlength='<?php echo esc_attr( RRF_REVIEW_HEADLINE_MAX_LENGTH ); ?>' placeholder="<?php esc_attr_e( 'Headline for your review*', 'wdm_ld_course_review' ); ?>" value="<?php echo esc_attr( $review_title ); ?>"/>
		</div>
		<div class="review-description review-details">
			<textarea maxlength="<?php echo esc_attr( RRF_REVIEW_DETAILS_MAX_LENGTH ); ?>" cols="30" rows="5" placeholder="<?php esc_attr_e( 'Review description*', 'wdm_ld_course_review' ); ?>"><?php echo esc_html( $review_description ); ?></textarea>
			<div class="wdm_rrf_remaining_characters">
				<span class="wdm_cff_remaining_count"><?php echo esc_html( RRF_REVIEW_DETAILS_MAX_LENGTH ); ?></span>
				<span><?php esc_html_e( 'remaining character(s)', 'wdm_ld_course_review' ); ?></span>
			</div>
		</div>
	</div>
	<div class="modal-navigation">
		<button class="previous"><?php esc_html_e( 'Back', 'wdm_ld_course_review' ); ?></button>
		<button class="next mid-submit-step" data-steptype="add"><?php esc_html_e( 'Save & Continue', 'wdm_ld_course_review' ); ?></button>
	</div>
</div>
