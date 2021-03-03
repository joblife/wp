<?php
/**
 * Loading email template of course feedback
 *
 * @package RatingsReviewsFeedback\Admin\Feedback
 */

$email_subject = get_option( 'wdm_feedback_email_subject', WDM_LD_DEFAULT_FEEDBACK_SUBJECT );
$email_body = get_option( 'wdm_feedback_email_body', WDM_LD_DEFAULT_FEEDBACK_BODY );
$email_subject = stripslashes( $email_subject );
?>

<div>


<form method="post">

<table class="form-table">
	<tbody>
	<tr>
	<th scope="row"><label for="wdm_feedback_email_subject"><?php esc_html_e( 'Subject', 'wdm_ld_course_review' ); ?></label></th>
   <td>
   <input name="wdm_feedback_email_subject" type="text" id="wdm_feedback_email_subject" aria-describedby="feedback-email-subject" value="<?php echo esc_attr( $email_subject ); ?>" class="regular-text ltr">
	<p class="description" id="feedback-email-subject"><?php esc_html_e( 'This subject will be used while sending email to author.', 'wdm_ld_course_review' ); ?></p>
	</td>

	</tr>

	<tr>
	<th scope="row"><label for="wdm_feedback_email_body"><?php esc_html_e( 'Body', 'wdm_ld_course_review' ); ?></label></th>
	<td>
	<?php

	$editor_settings = array(
		'textarea_rows' => 100,
		'editor_height' => 200,
	);
	wp_editor(
		( wp_unslash( $email_body ) ),
		'wdm_feedback_email_body',
		$editor_settings
	);

	?>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="wdm_shortcode"><?php esc_html_e( 'Available shortcodes', 'wdm_ld_course_review' ); ?></label></th>
	<td>
		<table>
			<tr>
				<td>
		<b><?php esc_html_e( 'User shortcodes', 'wdm_ld_course_review' ); ?></b>
		<div>
		<span>
		<ol>
		  <li>[user_first_name]   : <?php esc_html_e( 'User first name', 'wdm_ld_course_review' ); ?></li>
		  <li>[user_last_name]   : <?php esc_html_e( 'User last name', 'wdm_ld_course_review' ); ?></li>
		  <li>[user_display_name] : <?php esc_html_e( 'User display name', 'wdm_ld_course_review' ); ?></li>
		  <li>[user_email_id]     : <?php esc_html_e( 'User email ID', 'wdm_ld_course_review' ); ?></li>
		  <li>[user_id]           : <?php esc_html_e( 'User ID', 'wdm_ld_course_review' ); ?></li>
		</ol>
		</span>
	</div>
				</td>
				<td>
<b><?php esc_html_e( 'Author shortcodes', 'wdm_ld_course_review' ); ?></b>
  <div>
		<span>
		<ol>
		  <li>[author_first_name]   : <?php esc_html_e( 'Author first name', 'wdm_ld_course_review' ); ?></li>
		  <li>[author_last_name]   : <?php esc_html_e( 'Author last name', 'wdm_ld_course_review' ); ?></li>
		  <li>[author_display_name] : <?php esc_html_e( 'Author display name', 'wdm_ld_course_review' ); ?></li>
		  <li>[author_email_id]     : <?php esc_html_e( 'Author email ID', 'wdm_ld_course_review' ); ?></li>
		  <li>[author_id]           : <?php esc_html_e( 'Author ID', 'wdm_ld_course_review' ); ?></li>
		</ol>
		</span>
	</div>
				</td>
			</tr>

			<tr>
				<td>
 <b><?php esc_html_e( 'Course shortcodes', 'wdm_ld_course_review' ); ?></b>
		<div>
		<span>
		<ol>
		  <li>[course_title]   : <?php esc_html_e( 'Course title', 'wdm_ld_course_review' ); ?></li>
		  <li>[course_link]   : <?php esc_html_e( 'Course link', 'wdm_ld_course_review' ); ?></li>
		  <li>[course_id] : <?php esc_html_e( 'Course ID', 'wdm_ld_course_review' ); ?></li>
		</ol>
		</span>
	</div>
				</td>
				<td>
 <b><?php esc_html_e( 'Feedback shortcodes', 'wdm_ld_course_review' ); ?></b>
		<div>
		<span>
		<ol>
		  <li>[feedback_content]   : <?php esc_html_e( 'Feedback of the user', 'wdm_ld_course_review' ); ?></li>
		  <li>[feedback_link]   : <?php esc_html_e( 'Feedback link', 'wdm_ld_course_review' ); ?></li>
		  <li>[feedback_id] : <?php esc_html_e( 'Feedback ID', 'wdm_ld_course_review' ); ?></li>
		</ol>
		</span>
	</div>
				</td>
			</tr>
		</table>


	</td>
	</tr>

	<?php
		do_action( 'wdm_feedback_email_template' );
	?>
</tbody>
</table>
<?php wp_nonce_field( 'wdm_feedback_email_template_action', 'wdm_feedback_email_template_nonce' ); ?>
<p class="submit">
<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
</p>
</form>

</div>

