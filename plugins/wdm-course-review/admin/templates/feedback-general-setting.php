<?php
/**
 * Loading general setting of course feedback.
 *
 * @package RatingsReviewsFeedback\Admin\Feedback
 */

$default = array(
	0 => __( 'No', 'wdm_ld_course_review' ),
	1 => __( 'Yes', 'wdm_ld_course_review' ),
);
$setting = get_option( 'wdm_course_feedback_setting', 1 );
$email_setting = get_option( 'wdm_send_email_after_feedback', 1 );
$btn_text = get_option( 'wdm_course_feedback_btn_txt', __( 'Provide your feedback', 'wdm_ld_course_review' ) );
?>

<div>


<form method="post">

<table class="form-table">
	<tbody>
	<tr>
	<th scope="row"><label for="wdm_course_feedback_setting"><?php esc_html_e( 'Feedback on all courses', 'wdm_ld_course_review' ); ?></label></th>
	<td>
	<input type="checkbox" name="wdm_course_feedback_setting" id="wdm_course_feedback_setting" aria-describedby="wdm-feedback-setting" value="1" 
	<?php
	checked( $setting );
	?>
			 />
	<p class="description" id="wdm-feedback-setting"><?php esc_html_e( 'Allow users to give feedback after course completion.', 'wdm_ld_course_review' ); ?></p>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="wdm_send_email_after_feedback"><?php esc_html_e( 'Feedback notification', 'wdm_ld_course_review' ); ?></label></th>
	<td>
	<input type="checkbox" name="wdm_send_email_after_feedback" id="wdm_send_email_after_feedback" aria-describedby="wdm-feedback-notification" value="1" 
	<?php
	checked( $email_setting );
	?>
			 />
	<p class="description" id="wdm-feedback-notification"><?php esc_html_e( 'To send email notification to the author after feedback submission.', 'wdm_ld_course_review' ); ?></p>
	</td>
	</tr>

	<!-- for feedback button text -->
	<tr>
	<th scope="row"><label for="wdm_course_feedback_btn_txt"><?php esc_html_e( 'Feedback button text', 'wdm_ld_course_review' ); ?></label></th>
	<td>
	<input type="text" name="wdm_course_feedback_btn_txt" value="<?php echo esc_attr( $btn_text ); ?>" aria-describedby="wdm-feedback-btn-text" />
	<p class="description" id="wdm-feedback-btn-text"><?php esc_html_e( 'This text will be used on feedback button.', 'wdm_ld_course_review' ); ?></p>
	</td>
	</tr>

	<?php
		do_action( 'wdm_feedback_general_setting' );
	?>
</tbody>
</table>
<?php wp_nonce_field( 'wdm_feedback_general_setting_action', 'wdm_feedback_general_setting_nonce' ); ?>
<p class="submit">
<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
</p>
</form>
<style type="text/css">
	.form-table th{
		width: 35%;
	}
</style>
</div>

