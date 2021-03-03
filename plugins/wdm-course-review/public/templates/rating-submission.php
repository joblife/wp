<?php
/**
 * This file is the template for selecting the stars to rate.
 *
 * @package RatingsReviewsFeedback\Public\Reviews
 *
 * $step_number : Step Count
 * $step_type : Type of step i.e., add/edit/delete in this case it will always be add.
 * $steps : Registered steps.
 * $course_id : Course ID.
 */

?>
<div class="rrf-modal-content modal star-submission" data-course_id="<?php echo esc_attr( $course_id ); ?>" data-step="<?php echo esc_attr( $step_number ); ?>" data-steptype="<?php echo esc_attr( $step_type ); ?>">
	<?php
	global $rrf_ratings_settings;
	$rrf_ratings_settings['displayOnly'] = false;
	$rrf_ratings_settings['size'] = 'xl';
	$rrf_ratings_settings['showClear'] = false;
	$rrf_ratings_settings['starCaptions'] = array(
		1 => __( 'Awful, not what I expected at all', 'wdm_ld_course_review' ),
		2 => __( 'Poor, pretty disappointed', 'wdm_ld_course_review' ),
		3 => __( 'Average, could be better', 'wdm_ld_course_review' ),
		4 => __( 'Good, what I expected', 'wdm_ld_course_review' ),
		5 => __( 'Amazing, above expectations!', 'wdm_ld_course_review' ),
	);
	$input_id = 'input-' . $course_id . '-rrf';
	?>
	<div class="modal-container">
		<div class="prompt-text"><?php echo esc_html__( 'How would you rate this course?', 'wdm_ld_course_review' ); ?></div>
		<input type="hidden" class="rating-settings" value='<?php echo json_encode( $rrf_ratings_settings ); ?>'>
		<input data-id="<?php echo esc_attr( $input_id ); ?>" class="rating rating-loading">
	</div>
	<div class="modal-navigation hide">
		<button class="next" data-steptype="add" disabled="disabled"><?php esc_html_e( 'Save & Continue', 'wdm_ld_course_review' ); ?></button>
	</div>
	<!-- <button class="previous">Back</button> -->
</div>
