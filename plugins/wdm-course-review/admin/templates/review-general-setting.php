<?php
/**
 * Loading general setting of course reviews.
 *
 * @package RatingsReviewsFeedback\Admin\Reviews
 */

$default = array(
	0 => __( 'No', 'wdm_ld_course_review' ),
	1 => __( 'Yes', 'wdm_ld_course_review' ),
);
$setting = get_option( 'wdm_course_review_setting', 1 );
$email_setting = get_option( 'wdm_send_email_after_review', 1 );
$default_subject = get_option( 'wdm_review_default_reject_subject', WDM_LD_DEFAULT_REVIEW_REJECTION_SUBJECT );
$default_message = get_option( 'wdm_review_default_message', '' );
?>

<div>


<form method="post">

<table class="form-table">
	<tbody>
	<tr>
	<th scope="row"><label for="wdm_course_review_setting"><?php esc_html_e( 'Allow comment on reviews', 'wdm_ld_course_review' ); ?></label></th>
	<td>
	<input type="checkbox" name="wdm_course_review_setting" id="wdm_course_review_setting" aria-describedby="wdm-review-setting" value="1" 
	<?php
	checked( $setting );
	?>
			 />
	<p class="description" id="wdm-review-setting"><?php esc_html_e( 'Allow users to give comments on the reviews.', 'wdm_ld_course_review' ); ?></p>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="wdm_send_email_after_review"><?php esc_html_e( 'Review notification', 'wdm_ld_course_review' ); ?></label></th>
	<td>
	<input type="checkbox" name="wdm_send_email_after_review" id="wdm_send_email_after_review" aria-describedby="wdm-review-notification" value="1" 
	<?php
	checked( $email_setting );
	?>
			 />
	<p class="description" id="wdm-review-notification"><?php esc_html_e( 'To send email notification to the author after review submission.', 'wdm_ld_course_review' ); ?></p>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="wdm_review_default_reject_subject"><?php esc_html_e( 'Review default reject subject', 'wdm_ld_course_review' ); ?></label></th>
	<td>
	<input type="text" name="wdm_review_default_reject_subject" id="wdm_review_default_reject_subject" aria-describedby="wdm-review-rejection-subject" value="<?php echo esc_attr( $default_subject ); ?>" />
	<p class="description" id="wdm-review-rejection-subject"><?php esc_html_e( 'The default subject set here will be sent to the author when a course review is rejected.', 'wdm_ld_course_review' ); ?></p>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="wdm_review_default_message"><?php esc_html_e( 'Review default reject message', 'wdm_ld_course_review' ); ?></label></th>
	<td>
	<textarea name="wdm_review_default_message" id="wdm_review_default_message" aria-describedby="wdm-review-rejection" rows="5"><?php echo esc_html( $default_message ); ?></textarea>
	<p class="description" id="wdm-review-rejection"><?php esc_html_e( 'The default message set here will be displayed when a course review is rejected.', 'wdm_ld_course_review' ); ?></p>
	</td>
	</tr>
	<?php
		do_action( 'wdm_review_general_setting' );
	?>
</tbody>
</table>
<?php wp_nonce_field( 'wdm_review_general_setting_action', 'wdm_review_general_setting_nonce' ); ?>
<p class="submit">
<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
</p>
</form>
<style type="text/css">
	.form-table th{
		width: 35%;
	}
	textarea#wdm_review_default_message,
	input#wdm_review_default_reject_subject {
		width: 50%;
	}
</style>
</div>

