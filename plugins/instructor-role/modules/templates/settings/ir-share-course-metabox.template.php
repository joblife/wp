<?php
/**
 * Share Course Metabox Template
 *
 * @since 3.2.0
 *
 * @var object  $course
 * @var array   $all_instructors
 * @var array   $shared_instructor_ids
 */

defined( 'ABSPATH' ) || exit;

?>
<?php if ( $course->post_author == get_current_user_id() || current_user_can( 'manage_options' ) ) : ?>
	<div class="ir-share-course-metabox-div">
		<p><?php _e( sprintf( 'Select the list of instructors you wish to share this %s with', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' ); ?></p>
		<select name="shared_instructors[]" id="ir-shared-instructors" style="width: 100%;" multiple>
			<?php foreach ( $all_instructors as $instructor ) : ?>
				<option
					value="<?php echo $instructor->ID; ?>"
					data-avatar="<?php echo get_avatar_url( $instructor->ID, array( 'size' => 32 ) ); ?>"
					<?php echo in_array( $instructor->ID, $shared_instructor_ids ) ? 'selected' : ''; ?>>
					<?php echo $instructor->display_name; ?>
				</option>
			<?php endforeach ?>
		</select>
	</div>
<?php else : ?>
	<div class="ir-course-shared-message">
		<?php
		_e(
			apply_filters(
				'ir_filter_share_course_restriction_message',
				sprintf( '<p>Sorry, but you cannot share this %1$s with anyone.</p><p>Contact <b>%2$s</b>, the author of this %1$s.</p>', \LearnDash_Custom_Label::label_to_lower( 'course' ), get_the_author_meta( 'display_name', $course->post_author ) ),
				$course
			)
		);
		?>
	</div>
<?php endif; ?>
