<?php
/**
 * Instructor Course Reports Page Template
 *
 * @var $course_id          int     ID of the course.
 * @var $selected_course_id int     ID of the selected course.
 * @var $course_list        array   List of courses.
 *
 * @since 3.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div id="learndash-instructor-reports"  class="wrap">
	<h2>
		<?php _e( sprintf( '%s Reports', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?>
	</h2>
	<br>
	<div class="sfwd_settings_left">
		<div class=" " id="div-instructor-courses">
			<div class="inside">
				<?php if ( ! empty( $course_list ) ) : ?>
					<label class="wdm-filter-title">
						<?php _e( sprintf( 'Select %s :', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?>
					</label>
					<select name="sel-instructor-courses" id="instructor-courses" onchange='wdm_change_report(this)'>
						<?php foreach ( $course_list as $course_id ) : ?>
							<option value="<?php echo esc_html( $course_id ); ?>"
							<?php echo ( $selected_course_id == $course_id ) ? 'selected' : ''; ?>>
								<?php esc_html_e( get_the_title( $course_id ) ); ?>
							</option>
						<?php endforeach; ?>
					</select>

					<div id="wdm_main_report_div" >
						<?php $this->display_course_reports( $selected_course_id, 1 ); ?>
					</div>

				<?php else : ?>
					<?php echo __( 'No reports to display', 'wdm_instructor_role' ); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
