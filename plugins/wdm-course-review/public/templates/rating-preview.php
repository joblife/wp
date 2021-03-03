<?php
/**
 * This file is the template for showing preview (for edit case).
 *
 * @package RatingsReviewsFeedback\Public\Reviews
 *
 * $step_number : Step Count
 * $step_type : Type of step i.e., add/edit/delete in this case it will always be add.
 * $steps : Registered steps.
 * $course_id : Course ID.
 * $stars
 * $review_title
 * $review_description
 */

?>
<div class="rrf-modal-content modal rating-preview" data-course_id="<?php echo esc_attr( $course_id ); ?>" data-step="<?php echo esc_attr( $step_number ); ?>" data-steptype="<?php echo esc_attr( $step_type ); ?>">
	<?php
	global $rrf_ratings_settings;
	$rrf_ratings_settings['displayOnly'] = true;
	$rrf_ratings_settings['size'] = 'md';
	$rrf_ratings_settings['showClear'] = false;
	$rrf_ratings_settings['showCaption'] = false;
	$input_id = 'input-' . $course_id . '-rrf';
	$user_id = get_current_user_id();
	$review = rrf_get_user_course_review_id( $user_id, $course_id );
	if ( ! empty( $review ) ) {
		$review_title = $review->post_title;
		$review_description = $review->post_content;
		$stars = intval( get_post_meta( $review->ID, 'wdm_course_review_review_rating', true ) );
	}
	?>
	<div class="modal-container">
		<h4><?php echo esc_html__( 'Your Review', 'wdm_ld_course_review' ); ?></h4>
		<input type="hidden" class="rating-settings" value='<?php echo json_encode( $rrf_ratings_settings ); ?>'>
		<input data-id="<?php echo esc_attr( $input_id ); ?>" class="rating rating-loading" value="<?php echo esc_attr( $stars ); ?>">
		<div class="review-text">
			<span><strong><?php echo esc_html( $review_title ); ?></strong></span><br>
			<span><?php echo esc_html( $review_description ); ?></span>
		</div>
	</div>
	<div class="modal-navigation">
		<button class="next" data-steptype="delete"><?php esc_html_e( 'Delete', 'wdm_ld_course_review' ); ?></button>
		<button class="next" data-steptype="add"><?php esc_html_e( 'Edit Review', 'wdm_ld_course_review' ); ?></button>
	</div>
</div>
