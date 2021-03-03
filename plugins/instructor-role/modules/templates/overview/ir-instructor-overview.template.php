<?php
/**
 * Template : Instructor Overview Template
 *
 * @param array $course_list        List of instructor course ids
 * @param string $ajax_loader       Ajax Loader path
 * @param object $instance          Instance of Instructor_Role_Overview class.
 * @since 3.1.0
 */

?>
<div class="wrap learndash-settings-page-wrap learndash-overview-page-wrap">
	<div class="ir-instructor-overview-container">
		<h1><?php esc_html_e( 'Instructor Overview', 'wdm_instructor_role' ); ?></h1>

		<div class="ir-instructor-data-container">
			<div class="ir-instructor-info-tiles">
				<div class="ir-course-info-tile info-tile">
					<a href="<?php echo esc_attr( $instance->page_links['courses'] ); ?>" class="ir-page-link"></a>
					<div class="info-tile-title ir-theme-color"><?php echo esc_attr( $instance->courses_label ); ?></div>
					<div>
						<span class="dashicons dashicons-desktop ir-theme-color"></span>
					</div>
					<div class="info-tile-stats ir-theme-color"><?php echo esc_attr( $instance->course_count ); ?></div>
				</div>
				<div class="ir-student-info-tile info-tile">
					<div class='info-tile-title ir-theme-color'><?php esc_html_e( 'Students', 'wdm_instructor_role' ); ?></div>
					<div>
						<span class="dashicons dashicons-groups ir-theme-color"></span>
					</div>
					<div class="info-tile-stats ir-theme-color"><?php echo esc_attr( $instance->student_count ); ?></div>
				</div>
				<?php if ( ! empty( $instance->addon_info ) ) : ?>
					<?php if ( array_key_exists( 'products', $instance->addon_info ) && array_key_exists( 'downloads', $instance->addon_info ) ) : ?>
					<div class="ir-addon-info-tile info-tile split-tile">
						<div class='ir-half-tile'>
							<a href="<?php echo esc_attr( $instance->page_links['woo'] ); ?>" class="ir-page-link"></a>
							<div class='ir-woo-icon'>
								<span class="wcicon-woo ir-theme-color"></span>
							</div>
							<div class='ir-stats-count ir-theme-color'><?php echo esc_attr( $instance->addon_info['products'] ); ?></div>
						</div>
						<div class='ir-half-tile'>
							<a href="<?php echo esc_attr( $instance->page_links['edd'] ); ?>" class="ir-page-link"></a>
							<div class='ir-edd-icon'>
								<span class="dashicons dashicons-download ir-theme-color"></span>
							</div>
							<div class='ir-stats-count ir-theme-color'><?php echo esc_attr( $instance->addon_info['downloads'] ); ?></div>
						</div>
					</div>
					<?php else : ?>
					<div class="ir-addon-info-tile info-tile full-tile">
						<?php if ( array_key_exists( 'products', $instance->addon_info ) ) : ?>
							<a href="<?php echo esc_attr( $instance->page_links['woo'] ); ?>" class="ir-page-link"></a>
							<div class='ir-woo-icon'>
								<span class="wcicon-woo ir-theme-color"></span>
							</div>
							<div class='ir-stats-count ir-theme-color'><?php echo esc_attr( $instance->addon_info['products'] ); ?></div>
						<?php endif; ?>
						<?php if ( array_key_exists( 'downloads', $instance->addon_info ) ) : ?>
							<a href="<?php echo esc_attr( $instance->page_links['edd'] ); ?>" class="ir-page-link"></a>
							<div class='ir-edd-icon'>
								<span class="dashicons dashicons-download ir-theme-color"></span>
							</div>
							<div class='ir-stats-count ir-theme-color'><?php echo esc_attr( $instance->addon_info['downloads'] ); ?></div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>

			<div class="ir-overview-charts">
				<?php if ( ! ir_admin_settings_check( 'instructor_commission' ) ) : ?>
					<div class="ir-earnings ir-chart ir-theme-color">
						<div id="ir-earnings-pie-chart-div"></div>
					</div>
				<?php endif; ?>
				<div class="ir-course-reports ir-chart">
					<div class="ir-ajax-overlay" style="display: none;">
						<img src="<?php echo esc_attr( $ajax_icon ); ?>" alt="Loading...">
					</div>
					<div class="ir-instructor-course-select-wrap">
						<select name="sel-instructor-courses" id="instructor-courses-select">
						<?php if ( ! empty( $course_list ) ) : ?>
							<?php foreach ( $course_list as $key => $course_id ) : ?>
								<option value="<?php echo esc_attr( $course_id ); ?>" <?php echo ! ( $key ) ? 'selected' : ''; ?>>
									<?php echo esc_html( get_the_title( $course_id ) ); ?>
								</option>
							<?php endforeach; ?>
						<?php else : ?>
							<?php esc_html_e( sprintf( 'No %s created', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?>
						<?php endif; ?>
						</select>
					</div>
					<div id="ir-course-pie-chart-div"></div>
				</div>
			</div>

			<div class="ir-submissions ir-theme-color">
				<?php $instance->generateSubmissionReports(); ?>
			</div>
		</div>
	</div>
</div>
