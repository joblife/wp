<?php
/**
 * No Course Reports Found Template
 *
 * @var $icon_path  string  Path of the no report image displayed on the page.
 *
 * @since 3.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div class="ir-no-enrollments">
	<div class="ir-no-reports-image">
		<?php echo file_get_contents( $icon_path ); ?>    
	</div>
	<p class="no-reports-message">
		<?php esc_html_e( sprintf( 'Sorry, no users enrolled in this %s yet to show reports', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?>
	</p>
</div>
