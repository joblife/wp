<?php
/**
 * Course Reports User Single Record Template.
 *
 * @var $user_meta              object  Object containing all user details.
 * @var $completed_percentage   int     Percentage of course completed by the user.
 * @var $completed_steps        int     No of steps completed by the user.
 * @var $total_steps            int     Total no of steps in the course.
 * @var $course_completed_on    string  Date on which the course was completed.
 *
 * @since 3.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<tr>
	<td><?php echo $user_meta->data->user_login; ?></td>
	<td><?php echo $user_meta->data->user_email; ?></td>
	<td><?php echo $completed_percentage; ?></td>
	<td><?php echo $total_steps; ?></td>
	<td><?php echo $completed_steps; ?></td>
	<td><?php echo $course_completed_on; ?></td>
	<td>
		<a
			href="javascript:wdm_show_email_form('<?php echo $user_meta->data->user_email; ?>');"
			title="<?php
			// translators: Student email.
			echo sprintf( __( 'E-Mail to %s', 'wdm_instructor_role' ), $user_meta->data->user_email );
			?>">
			<?php esc_html_e( 'E-Mail', 'wdm_instructor_role' ); ?>
		</a>
	</td>
</tr>
