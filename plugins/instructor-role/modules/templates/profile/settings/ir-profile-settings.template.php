<?php
/**
 * Profile Settings Template
 *
 * @since 3.5.0
 *
 * @var array  $introduction_settings_data       Array of introduction settings section data.
 * @var array  $default_intro_settings_options   Array of default intro setting section options.
 * @var string $course_label                     LearnDash Course Label.
 * @var string $enable_profile_links             Whether the profile links settings are enabled or not.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div class="wrap">
	<h3><?php esc_html_e( 'Profile Settings', 'wdm_instructor_role' ); ?></h3>

	<form method="post" id="ir-profile-settings-form">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="ir_profile_enable">
							<?php esc_html_e( 'Profile Links', 'wdm_instructor_role' ); ?>
						</label>
					</th>
					<td>
						<input type="checkbox" name="ir_profile_enable" id="ir_profile_enable" <?php checked( 'on', $enable_profile_links ); ?>/>
						<p class="ir-tooltip">
							<?php
							echo esc_html(
								sprintf(
									// translators: Course placeholder.
									__( 'Instructor profile links on %s archive and single pages', 'wdm_instructor_role' ),
									$course_label
								)
							);
							?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>
							<?php esc_html_e( 'Introduction Section', 'wdm_instructor_role' ); ?>
						</label>
					</th>
					<td>
						<p class="ir-tooltip">
							<?php esc_html_e( 'Configure the details to be displayed in the introduction section.', 'wdm_instructor_role' ); ?>
						</p>
						<div class="ir-profile-intro-settings">
							<table class="ir-profile-settings-table">
								<thead>
									<th style="width: 10px;"></th>
									<th>
										<?php esc_html_e( 'Section', 'wdm_instructor_role' ); ?>
									</th>
									<th>
										<?php esc_html_e( 'Actions', 'wdm_instructor_role' ); ?>
									</th>
								</thead>
								<tbody>
									<?php foreach ( $introduction_settings_data as $key => $setting ) : ?>
										<tr class="ir-profile-settings-row">
											<td>
												<span class="dashicons dashicons-sort"></span>
											</td>
											<td>
												<span id="ir-profile-section-title-<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $setting['title'] ); ?></span>
											</td>
											<td id="ir-profile-section-actions-<?php echo esc_attr( $key ); ?>">
												<a title="<?php esc_attr_e( 'Edit', 'wdm_instructor_role' ); ?>">
													<span data-id=<?php echo esc_attr( $key ); ?> class="dashicons dashicons-admin-tools ir-profile-setting-edit"></span>
												</a>
												<input id="ir-profile-section-data-<?php echo esc_attr( $key ); ?>" type="hidden" name="ir_profile_section[<?php echo esc_attr( $key ); ?>]" value='<?php echo json_encode( $setting ); ?>'>
												<a class="ir-profile-delete-section" title="<?php esc_attr_e( 'Delete', 'wdm_instructor_role' ); ?>">
													<span class="dashicons dashicons-trash"></span>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						<p>
							<a id="ir-profile-add-setting-section" class="button button-secondary"><?php esc_html_e( 'Add', 'wdm_instructor_role' ); ?></a>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- Add Profile Setting Container -->
		<div id="ir-profile-add-setting-container" class="ir-overlay">
			<div class="ir-profile-add-setting">
				<h3><?php esc_html_e( 'Add/Edit Section', 'wdm_instructor_role' ); ?></h3>
				<a class="close">&times;</a>
				<div class="ir-profile-add-setting-contents">
					<table>
						<tbody>
							<tr>
								<th>
									<?php esc_html_e( 'Title', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<input id="ir-profile-update-title" type="text" name="ir-profile-update-setting[title]"/>
								</td>
							</tr>
							<tr>
								<th>
									<?php esc_html_e( 'Image', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<select id="ir-profile-update-image" name="ir-profile-update-setting[image]">
										<?php foreach ( $default_intro_settings_options['image'] as $image_key => $image_value ) : ?>
											<option value="<?php echo esc_attr( $image_key ); ?>"><?php echo esc_attr( $image_value ); ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<div class="ir-profile-image-div">
										<span>
											<a id="ir-profile-update-custom-image">
												<?php esc_html_e( 'Select Image', 'wdm_instructor_role' ); ?>
											</a>
										</span>
										<span>
											<a target="blank" id="ir-profile-view-img-url"><?php esc_html_e( 'View Image', 'wdm_instructor_role' ); ?>
										</span>
										<input type="hidden" name="ir-profile-update-setting[custom_image_url]" class="ir-profile-update-custom-settings"/>
									</div>
								</td>
							</tr>
							<tr>
								<th>
									<?php esc_html_e( 'Meta Key', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<input id="ir-profile-update-metakey" type="text" name="ir-profile-update-setting[meta_key]"/>
								</td>
								<td class="ir-docs-help-link">
									<a target="blank" href="https://wisdmlabs.com/docs/article/wisdm-instructor-role/ir-features/profile-introduction-sections/#meta-key">
										<span class="dashicons dashicons-info" title="<?php esc_html_e( 'Click here to learn more', 'wdm_instructor_role' ); ?>"></span>
									</a>
								</td>
							</tr>
							<tr>
								<th>
									<?php esc_html_e( 'Data Type', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<select id="ir-profile-update-datatype" name="ir-profile-update-setting[data_type]">
										<?php foreach ( $default_intro_settings_options['data_type'] as $data_key => $data_value ) : ?>
											<option value="<?php echo esc_attr( $data_key ); ?>"><?php echo esc_attr( $data_value ); ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th>
									<?php esc_html_e( 'Icon', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<select id="ir-profile-update-icon" name="ir-profile-update-setting[icon]" id="">
										<?php foreach ( $default_intro_settings_options['icon'] as $icon_key => $icon_value ) : ?>
											<option value="<?php echo esc_attr( $icon_key ); ?>"><?php echo esc_attr( $icon_value ); ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<input id="ir-profile-update-custom-dashicon" type="text" name="ir-profile-update-setting[custom_dashicon]" placeholder="<?php esc_html_e( 'eg: dashicons-yes' ); ?>"/>
								</td>
							</tr>
						</tbody>
					</table>
					<p>
						<a id="ir-profile-update-save" class="button ir-profile-update-button"><?php esc_html_e( 'Save', 'wdm_instructor_role' ); ?></a>
						<a id="ir-profile-update-add" class="button ir-profile-update-button"><?php esc_html_e( 'Add', 'wdm_instructor_role' ); ?></a>
						<span class="dashicons dashicons-update"></span>
						<input id="ir-profile-update-id" type="hidden" name="ir-profile-update-setting[id]" value="0" />
					</p>
				</div>
			</div>
		</div>
		<p>
			<input id="ir-profile-save-setting-section" type="submit" class="button button-primary" name="ir_profile_settings_save" value="<?php esc_html_e( 'Save', 'wdm_instructor_role' ); ?>">
			<?php wp_nonce_field( 'ir_profile_settings_nonce', 'ir_nonce' ); ?>
		</p>

	</form>
</div>
