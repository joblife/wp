<?php
/**
 * This file is the template for showing first delete screen.
 *
 * @package RatingsReviewsFeedback\Public\Reviews
 *
 * $step_number : Step Count
 * $step_type : Type of step i.e., add/edit/delete in this case it will always be delete.
 * $steps : Registered steps.
 */

?>
<div class="rrf-modal-content modal delete-review" data-course_id="<?php echo esc_attr( $course_id ); ?>" data-step="<?php echo esc_attr( $step_number ); ?>" data-steptype="<?php echo esc_attr( $step_type ); ?>">
	<?php
		$user_id = get_current_user_id();
		$review = rrf_get_user_course_review_id( $user_id, $course_id );
	?>
	<div class="modal-container">
		<h4><?php echo esc_html__( 'Delete Your Review?', 'wdm_ld_course_review' ); ?></h4>
	</div>
	<div class="review-text">
		<?php echo esc_html__( 'Are you sure you want to delete your review?', 'wdm_ld_course_review' ); ?>
	</div>
	<div class="modal-navigation">
		<button class="delete-close"><?php echo esc_html__( 'Cancel', 'wdm_ld_course_review' ); ?></button>
		<button class="delete-close delete-confirm"><?php echo esc_html__( 'Yes, Delete My Review', 'wdm_ld_course_review' ); ?></button>
	</div>
</div>
