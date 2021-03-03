<?php
/**
 * This file is the template for showing final conclusion.
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
<div class="rrf-modal-content modal review-completion" data-course_id="<?php echo esc_attr( $course_id ); ?>" data-step="<?php echo esc_attr( $step_number ); ?>" data-steptype="<?php echo esc_attr( $step_type ); ?>">
	<?php
	global $rrf_ratings_settings;
	$rrf_ratings_settings['displayOnly'] = true;
	$rrf_ratings_settings['size'] = 'md';
	$rrf_ratings_settings['showClear'] = false;
	$rrf_ratings_settings['showCaption'] = false;
	$input_id = 'input-' . $course_id . '-rrf';
	?>
	<div class="modal-container">
		<h3><?php echo esc_html__( 'Thanks for submitting the review!', 'wdm_ld_course_review' ); ?></h3>
		<div class="prompt-text"><?php echo esc_html__( 'We will make it public once it has been approved.', 'wdm_ld_course_review' ); ?></div>
		<input type="hidden" class="rating-settings" value='<?php echo json_encode( $rrf_ratings_settings ); ?>'>
		<input data-id="<?php echo esc_attr( $input_id ); ?>" class="rating rating-loading" value="<?php echo esc_attr( $stars ); ?>">
		<div>
			<span><strong><?php echo esc_html( $review_title ); ?></strong></span><br>
			<span><?php echo esc_html( $review_description ); ?></span>
		</div>
	</div>
	<div class="modal-navigation">
		<button class="previous"><?php esc_html_e( 'Back', 'wdm_ld_course_review' ); ?></button>
		<button class="rrf-review-submission"><?php esc_html_e( 'Save & Exit', 'wdm_ld_course_review' ); ?></button>
	</div>
</div>
