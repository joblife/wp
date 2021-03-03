<div class="eat-tab-content eat-tab-custom-login-management" style="display: none;">
	<div class="eat-tab-content-header">
		<div class="eat-tab-content-header-title"><?php _e('custom Login page Management', 'everest-admin-theme'); ?></div>
	</div>
	<div class="eat-tab-content-body">
		<div class="eat-options-wrap-outer">
			<div class="eat-options-wrap">
				<label for="eat-background-option"><?php _e('Background selection', 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap eat-background-select-wrap">
					<select id='eat-background-options' name='everest_admin_theme[custom_login][background][type]' class="eat-selectbox-wrap eat-select-options eat-background-selector">
						<option value='' ><?php _e( 'Default', 'everest-admin-theme' ); ?></option>
						<option value='image' <?php selected( $plugin_settings['custom_login']['background']['type'], 'image' ); ?> > <?php _e( 'Image', 'everest-admin-theme'); ?></option>
						<option value='background-color' <?php selected( $plugin_settings['custom_login']['background']['type'], 'background-color' ); ?>><?php _e( 'Background Color', 'everest-admin-theme'); ?></option>
						<option value='video' <?php selected( $plugin_settings['custom_login']['background']['type'], 'video' ); ?>><?php _e( 'Video', 'everest-admin-theme' ); ?></option>
					</select>
				</div>
			</div>

			<div class="eat-background-select-content">
				<div class="eat-background-image-content-wrap eat-image eat-common-content-wrap" style="display: <?php if(isset($plugin_settings['custom_login']['background']['type']) && $plugin_settings['custom_login']['background']['type'] =='image' ){ ?> block; <?php }else{ ?> none; <?php } ?>">
					<div class="eat-image-selection-wrap">
						<div class="eat-options-wrap">
							<label for="eat-custom-login-background-image-url"><?php _e( 'Image Upload: ', 'everest-admin-theme' ); ?></label>
							<div class="eat-input-field-wrap">
								<input type="text" id='eat-custom-login-background-image-url' name='everest_admin_theme[custom_login][background][image][url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['custom_login']['background']['image']['url']) && $plugin_settings['custom_login']['background']['image']['url'] != '' ){ echo $plugin_settings['custom_login']['background']['image']['url']; } ?>' />
								<input type="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Image', 'everest-admin-theme'); ?>' />
							</div>
						</div>
						<div class='eat-image-preview eat-image-placeholder'>
							<img src='<?php if(isset($plugin_settings['custom_login']['background']['image']['url']) && $plugin_settings['custom_login']['background']['image']['url'] != '' ){ echo $plugin_settings['custom_login']['background']['image']['url']; } ?>' />
						</div>
					</div>
				</div>

				<div class="eat-background-color-content eat-background-color eat-common-content-wrap" style="display: <?php if(isset($plugin_settings['custom_login']['background']['type']) && $plugin_settings['custom_login']['background']['type'] =='background-color' ){ ?> block; <?php }else{ ?> none; <?php } ?>">
					<div class="eat-background-color-content-wrap">
						<div class="eat-options-wrap">
							<label for="eat-custom-login-background-color"><?php _e('Background Color', 'everest-admin-theme' ); ?></label>
							<input id='eat-custom-login-background-color' type="text" name='everest_admin_theme[custom_login][background][background-color][color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['background']['background-color']['color']) && $plugin_settings['custom_login']['background']['background-color']['color'] != '' ){ echo $plugin_settings['custom_login']['background']['background-color']['color']; } ?>' />
						</div>
					</div>
				</div>

				<div class="eat-video-content eat-video eat-common-content-wrap" style="display: <?php if(isset($plugin_settings['custom_login']['background']['type']) && $plugin_settings['custom_login']['background']['type'] =='video' ){ ?> block; <?php }else{ ?> none; <?php } ?>">
					<div class="eat-background-color-content-wrap eat-video-options-wrap">
						<div class="eat-options-wrap">
							<label for="eat-background-video-type"> <?php _e('Background Video type', 'everest-admin-theme' ); ?></label>
							<div class="eat-input-field-wrap">
								<select id='eat-background-video-type' name='everest_admin_theme[custom_login][background][video][type]' class='eat-selectbox-wrap eat-video-select-option'>
									<option value='youtube' <?php if(isset($plugin_settings['custom_login']['background']['video']['type']) && $plugin_settings['custom_login']['background']['video']['type'] == 'youtube' ){ ?> selected  <?php } ?> ><?php _e('Youtube', 'everest-admin-theme'); ?></option>
									<option value='viemo' 	<?php if(isset($plugin_settings['custom_login']['background']['video']['type']) && $plugin_settings['custom_login']['background']['video']['type'] == 'viemo' ){ ?> selected  <?php } ?> ><?php _e('Viemo', 'everest-admin-theme'); ?></option>
									<option value='html5' 	<?php if(isset($plugin_settings['custom_login']['background']['video']['type']) && $plugin_settings['custom_login']['background']['video']['type'] == 'html5' ){ ?> selected  <?php } ?>><?php _e('HTML5', 'everest-admin-theme'); ?></option>
								</select>
							</div>
						</div>
						<div class="eat-input-field-wrap eat-common-content-wrap-inner eat-youtube-details-input eat-youtube" style='display: <?php if(isset($plugin_settings['custom_login']['background']['video']['type']) && $plugin_settings['custom_login']['background']['video']['type'] =='youtube' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class='eat-options-wrap'>
								<label for="eat-custom-login-background-video-youtube"><?php _e('Youtube Video URL', 'everest-admin-theme'); ?></label>
								<input id='eat-custom-login-background-video-youtube' type="url" name='everest_admin_theme[custom_login][background][video][youtube][video-url]' value='<?php if(isset($plugin_settings['custom_login']['background']['video']['youtube']['video-url']) && $plugin_settings['custom_login']['background']['video']['youtube']['video-url'] != '' ){ echo $plugin_settings['custom_login']['background']['video']['youtube']['video-url']; } ?>'/>
							</div>
						</div>

						<div class="eat-input-field-wrap eat-common-content-wrap-inner eat-viemo-details-input eat-viemo" style='display: <?php if(isset($plugin_settings['custom_login']['background']['video']['type']) && $plugin_settings['custom_login']['background']['video']['type'] =='viemo' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class="eat-options-wrap">
								<label for="eat-custom-login-background-video-viemo"><?php _e( 'Viemo Video URL', 'everest-admin-theme' ); ?></label>
								<input id='eat-custom-login-background-video-viemo' type="url" name='everest_admin_theme[custom_login][background][video][viemo][video-url]' value='<?php if(isset($plugin_settings['custom_login']['background']['video']['viemo']['video-url']) && $plugin_settings['custom_login']['background']['video']['viemo']['video-url'] != '' ){ echo $plugin_settings['custom_login']['background']['video']['viemo']['video-url']; } ?>' />
							</div>
						</div>

						<div class="eat-common-content-wrap-inner eat-html5-video-details-input eat-html5" style='display: <?php if(isset($plugin_settings['custom_login']['background']['video']['type']) && $plugin_settings['custom_login']['background']['video']['type'] =='html5' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class="eat-image-selection-wrap">
								<div class="eat-options-wrap">
									<label for="eat-custom-login-background-video-html5-video-url"><?php _e( 'MP4 video URL: ', 'everest-admin-theme' ); ?></label>
									<div class="eat-item-input-field-wrap">
										<input type="url" id='eat-custom-login-background-video-html5-video-url' name='everest_admin_theme[custom_login][background][video][html5][mp4-video-url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['custom_login']['background']['video']['html5']['mp4-video-url']) && $plugin_settings['custom_login']['background']['video']['html5']['mp4-video-url'] != '' ){ echo $plugin_settings['custom_login']['background']['video']['html5']['mp4-video-url']; } ?>' />
										<input type="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Video', 'everest-admin-theme'); ?>' />
									</div>
								</div>
							</div>
							<div class="eat-image-selection-wrap">
								<div class="eat-options-wrap">
									<label for="eat-custom-login-background-video-html5-video-url"><?php _e( 'WEBM video URL: ', 'everest-admin-theme' ); ?></label>
									<div class="eat-item-input-field-wrap">
										<input type="url" id='eat-custom-login-background-video-html5-video-url' name='everest_admin_theme[custom_login][background][video][html5][webm-video-url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['custom_login']['background']['video']['html5']['webm-video-url']) && $plugin_settings['custom_login']['background']['video']['html5']['webm-video-url'] != '' ){ echo $plugin_settings['custom_login']['background']['video']['html5']['webm-video-url']; } ?>' />
										<input type="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Video', 'everest-admin-theme'); ?>' />
									</div>
								</div>
							</div>
							<div class="eat-image-selection-wrap">
								<div class="eat-options-wrap">
									<label for="eat-custom-login-background-video-html5-video-url"><?php _e( 'OGV video URL: ', 'everest-admin-theme' ); ?></label>
									<div class="eat-item-input-field-wrap">
										<input type="url" id='eat-custom-login-background-video-html5-video-url' name='everest_admin_theme[custom_login][background][video][html5][ogv-video-url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['custom_login']['background']['video']['html5']['ogv-video-url']) && $plugin_settings['custom_login']['background']['video']['html5']['ogv-video-url'] != '' ){ echo $plugin_settings['custom_login']['background']['video']['html5']['ogv-video-url']; } ?>' />
										<input type="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Video', 'everest-admin-theme'); ?>' />
									</div>
								</div>
							</div>
						</div>

						<div class='eat-options-wrap'>
							<label for='eat-custom-login-background-video-start-time'><?php _e( 'Video start/end time (sec)', 'everest-admin-theme' ); ?></label>
							<div class="eat-item-input-field-wrap">
								<input type="number" placeholder="Start Time" step="0.01" id='eat-custom-login-background-video-start-time' name='everest_admin_theme[custom_login][background][video][start-time]' class="eat-image-upload-url" value='<?php if(isset($plugin_settings['custom_login']['background']['video']['start-time']) && $plugin_settings['custom_login']['background']['video']['start-time'] != '' ){ echo $plugin_settings['custom_login']['background']['video']['start-time']; } ?>'>
								<input type="number" step="0.01" placeholder="End Time" id='eat-custom-login-background-video-end-time' name='everest_admin_theme[custom_login][background][video][end-time]' class="eat-image-upload-url" value='<?php if(isset($plugin_settings['custom_login']['background']['video']['end-time']) && $plugin_settings['custom_login']['background']['video']['end-time'] != '' ){ echo $plugin_settings['custom_login']['background']['video']['end-time']; } ?>'>
								<div class="input-info"><?php _e("Please enter the start time and end time in seconds for video(these values will be applied for each loop as well.)", 'everest-admin-theme'); ?></div>
							</div>
						</div>
					</div>
				</div>
				<div class="eat-parallax-options-content-wrap eat-options-wrap eat-common-content-wrap eat-common-content-wrap-all" style="<?php if( isset($plugin_settings['custom_login']['background']['type']) && ($plugin_settings['custom_login']['background']['type'] =='image' || $plugin_settings['custom_login']['background']['type'] =='video' ) ){ ?> display:block; <?php }else{ ?> display: none; <?php } ?>">
						<div class="eat-checkbox-outer-wrap">
							<div class="eat-options-wrap">
								<label for="eat-custom-login-background-video-parallax"><?php _e( 'Enable Parallax Effect?', 'everest-admin-theme' ); ?></label>
								<input type="checkbox" id='eat-custom-login-background-video-parallax' name='everest_admin_theme[custom_login][background][parallax][enable]' class='eat-image-overlay-enable-option eat-parallax-enable-option' <?php if(isset($plugin_settings['custom_login']['background']['parallax']['enable'])){ ?> checked <?php } ?> />
								<label for='eat-custom-login-background-video-parallax'></label>
							</div>

							<div class="eat-checkbox-checked-options" style='display: <?php if(isset($plugin_settings['custom_login']['background']['parallax']['enable'])){ ?> block; <?php }else{ ?> none; <?php } ?>'>
								<div class="eat-options-wrap">
									<label for="eat-custom-login-parallax-type-select-option"><?php _e( 'Parallax Type', 'everest-admin-theme' ); ?></label>
									<div class="eat-input-field-wrap">
										<select id='eat-custom-login-parallax-type-select-option' name='everest_admin_theme[custom_login][background][parallax][type]' class='eat-selectbox-wrap eat-parallax-type-select-option'>
											<option value='scroll' <?php if(isset($plugin_settings['custom_login']['background']['parallax']['type']) && $plugin_settings['custom_login']['background']['parallax']['type'] == 'scroll' ){ ?> selected  <?php } ?> ><?php _e('Scroll', 'everest-admin-theme'); ?></option>
											<option value='scale' <?php if(isset($plugin_settings['custom_login']['background']['parallax']['type']) && $plugin_settings['custom_login']['background']['parallax']['type'] == 'scale' ){ ?> selected  <?php } ?> ><?php _e('Scale', 'everest-admin-theme'); ?></option>
											<option value='opacity' <?php if(isset($plugin_settings['custom_login']['background']['parallax']['type']) && $plugin_settings['custom_login']['background']['parallax']['type'] == 'opacity' ){ ?> selected  <?php } ?>><?php _e('Opacity', 'everest-admin-theme'); ?></option>
											<option value='scroll-opacity' <?php if(isset($plugin_settings['custom_login']['background']['parallax']['type']) && $plugin_settings['custom_login']['background']['parallax']['type'] == 'scroll-opacity' ){ ?> selected  <?php } ?> ><?php _e('Scroll Opacity', 'everest-admin-theme'); ?></option>
											<option value='scale-opacity' <?php if(isset($plugin_settings['custom_login']['background']['parallax']['type']) && $plugin_settings['custom_login']['background']['parallax']['type'] == 'scale-opacity' ){ ?> selected  <?php } ?> ><?php _e('Scale Opacity', 'everest-admin-theme'); ?></option>
										</select>
									</div>
								</div>
								<div class="eat-options-wrap eat-image-overlay-color">
									<label for="eat-custom-login-background-video-speed"><?php _e( 'Speed', 'everest-admin-theme' ); ?></label>
									<div class="eat-item-input-field-wrap">
										<input type="number" step='0.01' min='0' max='2' id='eat-custom-login-background-video-speed' class='min-max-value' name='everest_admin_theme[custom_login][background][parallax][speed]' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['background']['parallax']['speed']) && $plugin_settings['custom_login']['background']['parallax']['speed'] != '' ){ echo $plugin_settings['custom_login']['background']['parallax']['speed']; }else { echo "0.5"; } ?>' />
										<div class="input-info"><?php _e('Please enter the number between 0 and 2', 'everest-admin-theme'); ?></div>
									</div>
								</div>

								<div class="eat-options-wrap eat-image-overlay-color">
									<label for="eat-custom-login-background-video-enable-parallax-mobile"><?php _e( 'Enable on Mobile devices?', 'everest-admin-theme' ); ?></label>
									<input type="checkbox" id='eat-custom-login-background-video-enable-parallax-mobile' name='everest_admin_theme[custom_login][background][parallax][enable-on-mobile-devices]' class='eat-enable-parallax-on-mobile-option' <?php if(isset($plugin_settings['custom_login']['background']['parallax']['enable-on-mobile-devices'])){ ?> checked <?php } ?> />
									<label for='eat-custom-login-background-video-enable-parallax-mobile'></label>
								</div>
							</div>
						</div>

						<div class="eat-checkbox-outer-wrap">
							<div class="eat-options-wrap">
								<label for="eat-custom-login-background-video-overlay-enable"><?php _e( 'Enable Overlay?', 'everest-admin-theme' ); ?></label>
								<input type="checkbox" id='eat-custom-login-background-video-overlay-enable' name='everest_admin_theme[custom_login][background][overlay][enable]' class='eat-image-overlay-enable-option' <?php if(isset($plugin_settings['custom_login']['background']['overlay']['enable'])){ ?> checked <?php } ?> />
								<label for='eat-custom-login-background-video-overlay-enable'></label>
							</div>

							<div class="eat-checkbox-checked-options" style="display: <?php if(isset($plugin_settings['custom_login']['background']['overlay']['enable'])){ ?> block; <?php }else{ ?> none; <?php } ?>">
								<div class="eat-options-wrap">
									<label for="eat-custom-login-background-video-overlay-color"><?php _e( 'Overlay Color', 'everest-admin-theme' ); ?></label>
									<div class="eat-input-field-wrap">
										<input type="text" id='eat-custom-login-background-video-overlay-color' class='eat-color-picker' name='everest_admin_theme[custom_login][background][overlay][color]' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['background']['overlay']['color']) && $plugin_settings['custom_login']['background']['overlay']['color'] != '' ){ echo $plugin_settings['custom_login']['background']['overlay']['color']; } ?>' />
									</div>
								</div>
							</div>
						</div>
				</div>
			</div>
		</div>
		<div class="eat-image-selection-wrap">
			<div class="eat-options-wrap">
				<div class="eat-style-label"><?php _e('Logo settings', 'everest-admin-theme'); ?></div>
				<label><?php _e('Custom Header Logo', 'everest-admin-theme' ); ?></label>
				<div class="eat-input-field-wrap">
					<input type="url" id='favicon-upload' name='everest_admin_theme[custom_login][login_form][logo][image-url]' class='eat-image-upload-url' value="<?php if(isset($plugin_settings['custom_login']['login_form']['logo']['image-url']) && $plugin_settings['custom_login']['login_form']['logo']['image-url'] != '' ){ echo esc_url($plugin_settings['custom_login']['login_form']['logo']['image-url']); } ?>" />
					<input type="button" class='eat-button eat-image-upload-button' value="<?php _e('Upload Image', 'everest-admin-theme'); ?>" />
				<div class="input-info">Please note if you keep this field blank the default wordpress logo will appear in login page.</div>
				</div>
			</div>
			<div class="eat-image-preview eat-image-placeholder">
				<?php if(isset($plugin_settings['custom_login']['login_form']['logo']['image-url']) && $plugin_settings['custom_login']['login_form']['logo']['image-url'] != '' ){ 
					 ?>
					<img src="<?php echo esc_url($plugin_settings['custom_login']['login_form']['logo']['image-url']); ?>" alt='custom header' />
					<?php }else{ ?>
					<img />
					<?php } ?>
			</div>
		</div>

		<div class="eat-options-wrap">
			<label><?php _e('Header logo title', 'everest-admin-theme' ); ?></label>
			<div class="eat-input-field-wrap">
				<input type="text" name='everest_admin_theme[custom_login][login_form][logo][title]' value="<?php if(isset($plugin_settings['custom_login']['login_form']['logo']['title']) && $plugin_settings['custom_login']['login_form']['logo']['title'] != '' ){ echo ($plugin_settings['custom_login']['login_form']['logo']['title']); } ?>" />
			</div>
		</div>

		<div class="eat-options-wrap">
			<label><?php _e('Header logo URL', 'everest-admin-theme' ); ?></label>
			<div class="eat-input-field-wrap">
				<input type="url" name='everest_admin_theme[custom_login][login_form][logo][url]' value="<?php if(isset($plugin_settings['custom_login']['login_form']['logo']['url']) && $plugin_settings['custom_login']['login_form']['logo']['url'] != '' ){ echo esc_url($plugin_settings['custom_login']['login_form']['logo']['url']); } ?>" />
			</div>
		</div>

		<div class="eat-options-wrap">
			<div class="eat-style-label"><?php _e('Login error messages settings', 'everest-admin-theme'); ?></div>
			<label><?php _e('Invalid Username', 'everest-admin-theme' ); ?></label>
			<div class="eat-input-field-wrap">
				<input type="text" name='everest_admin_theme[custom_login][login_form][error-message][invalid-username]' value="<?php if(isset($plugin_settings['custom_login']['login_form']['error-message']['invalid-username']) && $plugin_settings['custom_login']['login_form']['error-message']['invalid-username'] != '' ){ echo $plugin_settings['custom_login']['login_form']['error-message']['invalid-username']; } ?>"/>
			</div>
		</div>

		<div class="eat-options-wrap">
			<label><?php _e('Invalid Password', 'everest-admin-theme' ); ?></label>
			<div class="eat-input-field-wrap">
				<input type="text" name='everest_admin_theme[custom_login][login_form][error-message][invalid-password]' value="<?php if(isset($plugin_settings['custom_login']['login_form']['error-message']['invalid-password']) && $plugin_settings['custom_login']['login_form']['error-message']['invalid-password'] != '' ){ echo $plugin_settings['custom_login']['login_form']['error-message']['invalid-password']; } ?>" />
			</div>
		</div>

		<div class="eat-options-wrap">
		<div class='eat-style-label'><?php _e('Login Form settings', 'everest-admin-theme'); ?></div>
			<label for="eat-hide-footer-completely"><?php _e( 'Theme selection?', 'everest-admin-theme' ); ?></label>
			<div class="eat-input-field-wrap">
				<?php
				$login_form_templates = $eat_variables['login_form_templates'];
				if(isset($plugin_settings['custom_login']['login_form']['template']) && $plugin_settings['custom_login']['login_form']['template'] =='default'){
					$img_url = '';
				}
				?>
				<select name='everest_admin_theme[custom_login][login_form][template]' class="eat-selectbox-wrap eat-general-template-selection">
					<option value='default' data-img=''><?php _e('Default', 'everest-admin-theme'); ?></option>
					<?php foreach($login_form_templates as $key=>$value){ ?>
						<option value="<?php echo $value['value']; ?>" data-img="<?php echo $value['img']; ?>" <?php if(isset( $plugin_settings['custom_login']['login_form']['template'] ) && $plugin_settings['custom_login']['login_form']['template'] == $value['value']){ echo "selected"; $img_url = $value['img']; } ?>><?php echo $value['name']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="eat-img-selector-media eat-image-placeholder">
				<img src="<?php echo esc_url($img_url); ?>" />
			</div>
		</div>

		<div class="eat-options-wrap-outer">
			<div class="eat-options-wrap">
				<label for='eat-custom-login-login-form-background'><?php _e('Background selection','everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap eat-background-select-wrap">
					<select id='eat-background-options' name='everest_admin_theme[custom_login][login_form][background][type]' class="eat-selectbox-wrap eat-select-options eat-background-selector">
						<option value='' ><?php _e( 'Default', 'everest-admin-theme' ); ?></option>
						<option value='image' <?php selected( $plugin_settings['custom_login']['login_form']['background']['type'], 'image' ); ?> > <?php _e( 'Image', 'everest-admin-theme'); ?></option>
						<option value='background-color' <?php selected( $plugin_settings['custom_login']['login_form']['background']['type'], 'background-color' ); ?>><?php _e( 'Background Color', 'everest-admin-theme'); ?></option>
					</select>
				</div>
			</div>
			<div class="eat-background-select-content">
				<div class="eat-background-image-content-wrap eat-image eat-common-content-wrap" style="display: <?php if(isset($plugin_settings['custom_login']['login_form']['background']['type']) && $plugin_settings['custom_login']['login_form']['background']['type'] =='image' ){ ?> block; <?php }else{ ?> none; <?php } ?>">
					<div class="eat-image-selection-wrap">
						<div class="eat-options-wrap">
							<label for="eat-custom-login-login-form-background-image-url"><?php _e( 'Image Upload: ', 'everest-admin-theme' ); ?></label>
							<div class="eat-input-field-wrap">
								<input type="text" id='eat-custom-login-login-form-background-image-url' name='everest_admin_theme[custom_login][login_form][background][image][url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['custom_login']['login_form']['background']['image']['url']) && $plugin_settings['custom_login']['login_form']['background']['image']['url'] != '' ){ echo $plugin_settings['custom_login']['login_form']['background']['image']['url']; } ?>' />
								<input type="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Image', 'everest-admin-theme'); ?>' />
							</div>
						</div>
						<div class='eat-image-preview eat-image-placeholder'>
							<img src='<?php if(isset($plugin_settings['custom_login']['login_form']['background']['image']['url']) && $plugin_settings['custom_login']['login_form']['background']['image']['url'] != '' ){ echo $plugin_settings['custom_login']['login_form']['background']['image']['url']; } ?>' />
						</div>
					</div>

					<div class="eat-checkbox-outer-wrap">
						<div class="eat-options-wrap">
							<label for="eat-custom-login-overlay-enable"><?php _e( 'Enable Overlay?', 'everest-admin-theme' ); ?></label>
							<input type="checkbox" id='eat-custom-login-overlay-enable' name='everest_admin_theme[custom_login][login_form][background][overlay][enable]' class='eat-image-overlay-enable-option eat-custom-login-overlay-enable' <?php if(isset($plugin_settings['custom_login']['login_form']['background']['overlay']['enable'])){ ?> checked <?php } ?> />
							<label for='eat-custom-login-overlay-enable'></label>
						</div>

						<div class="eat-options-wrap eat-checkbox-checked-options eat-image-overlay-color" style='display: <?php if(isset($plugin_settings['custom_login']['login_form']['background']['overlay']['enable'])){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<label for="eat-custom-login-overlay-color"><?php _e( 'Overlay Color', 'everest-admin-theme' ); ?></label>
							<input type="text" id='eat-custom-login-overlay-color' class='eat-color-picker' name='everest_admin_theme[custom_login][login_form][background][overlay][color]' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['login_form']['background']['overlay']['color']) && $plugin_settings['custom_login']['login_form']['background']['overlay']['color'] != '' ){ echo $plugin_settings['custom_login']['login_form']['background']['overlay']['color']; } ?>' />
						</div>
					</div>
				</div>

				<div class="eat-background-color-content eat-background-color eat-common-content-wrap" style="display: <?php if(isset($plugin_settings['custom_login']['login_form']['background']['type']) && $plugin_settings['custom_login']['login_form']['background']['type'] =='background-color' ){ ?> block; <?php }else{ ?> none; <?php } ?>">
					<div class="eat-background-color-content-wrap">
						<div class="eat-options-wrap">
							<label for="eat-custom-login-login-form-background-color"><?php _e('Background Color', 'everest-admin-theme' ); ?></label>
							<input id='eat-custom-login-login-form-background-color' type="text" name='everest_admin_theme[custom_login][login_form][background][background-color][color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['login_form']['background']['background-color']['color']) && $plugin_settings['custom_login']['login_form']['background']['background-color']['color'] != '' ){ echo $plugin_settings['custom_login']['login_form']['background']['background-color']['color']; } ?>' />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class='eat-font-options'>
			<div class="eat-font-settings">
				<div class="eat-style-label"><?php _e('Font settings', 'everest-admin-theme'); ?></div>
				<div class='eat-username-font-settings'>
					<div class="eat-substyle-label"><?php _e('Username/password/remember me settings', 'everest-admin-theme'); ?></div>
					<div class="eat-options-wrap">
						<label for="eat-custom-login-text-color"><?php _e('Text Color', 'everest-admin-theme' ); ?></label>
						<input id='eat-custom-login-text-color' type="text" name='everest_admin_theme[custom_login][login_form][font_settings][login_password][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['login_form']['font_settings']['login_password']['font-color']) && $plugin_settings['custom_login']['login_form']['font_settings']['login_password']['font-color'] != '' ){ echo $plugin_settings['custom_login']['login_form']['font_settings']['login_password']['font-color']; } ?>' />
					</div>

					<div class="eat-options-wrap">
						<label for="eat-custom-login-text-color"><?php _e('Font Family', 'everest-admin-theme' ); ?></label>
						<div class="eat-input-field-wrap">
							<select id='eat-admin-menu-font-family' name='everest_admin_theme[custom_login][login_form][font_settings][login_password][google-fonts]' class="eat-selectbox-wrap">
							<option value=''><?php esc_html_e('Default', 'everest-admin-theme'); ?></option>
							<?php
							foreach($google_fonts as $key=>$value){
								// $key_value = str_replace(' ', '+', $value);
								?>
								<option value='<?php echo $value; ?>' <?php selected( $plugin_settings['custom_login']['login_form']['font_settings']['login_password']['google-fonts'], $value); ?>><?php echo $value; ?></option>
								<?php
							}
							?>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class='eat-font-settings'>
				<div class="eat-substyle-label"><?php _e( 'Login button settings', 'everest-admin-theme' ); ?></div>
				<div class="eat-options-wrap">
					<label for="eat-custom-login-text-color"><?php _e('Text color', 'everest-admin-theme' ); ?></label>
					<input id='eat-custom-login-text-color' type="text" name='everest_admin_theme[custom_login][login_form][font_settings][login_button][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['login_form']['font_settings']['login_button']['font-color']) && $plugin_settings['custom_login']['login_form']['font_settings']['login_button']['font-color'] != '' ){ echo $plugin_settings['custom_login']['login_form']['font_settings']['login_button']['font-color']; } ?>' />
				</div>
				<div class="eat-options-wrap">
					<label for="eat-custom-login-text-color"><?php _e('Background color', 'everest-admin-theme' ); ?></label>
					<input id='eat-custom-login-text-color' type="text" name='everest_admin_theme[custom_login][login_form][font_settings][login_button][background-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['login_form']['font_settings']['login_button']['background-color']) && $plugin_settings['custom_login']['login_form']['font_settings']['login_button']['background-color'] != '' ){ echo $plugin_settings['custom_login']['login_form']['font_settings']['login_button']['background-color']; } ?>' />
				</div>

				<div class="eat-options-wrap">
					<label for="eat-custom-login-text-color"><?php _e('Font Family', 'everest-admin-theme' ); ?></label>
					<div class="eat-input-field-wrap">
						<select id='eat-admin-menu-font-family' name='everest_admin_theme[custom_login][login_form][font_settings][login_button][google-fonts]' class="eat-selectbox-wrap">
						<option value=''><?php esc_html_e('Default', 'everest-admin-theme'); ?></option>
						<?php
						foreach($google_fonts as $key=>$value){
							// $key_value = str_replace(' ', '+', $value);
							?>
							<option value='<?php echo $value; ?>' <?php selected( $plugin_settings['custom_login']['login_form']['font_settings']['login_button']['google-fonts'], $value); ?>><?php echo $value; ?></option>
							<?php
						}
						?>
						</select>
					</div>
				</div>
				<div class="eat-options-wrap">
					<label for="eat-custom-login-text-color"><?php _e('Hover text Color', 'everest-admin-theme' ); ?></label>
					<input id='eat-custom-login-text-color' type="text" name='everest_admin_theme[custom_login][login_form][font_settings][login_button][hover][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['login_form']['font_settings']['login_button']['hover']['font-color']) && $plugin_settings['custom_login']['login_form']['font_settings']['login_button']['hover']['font-color'] != '' ){ echo $plugin_settings['custom_login']['login_form']['font_settings']['login_button']['hover']['font-color']; } ?>' />
				</div>
				<div class="eat-options-wrap">
					<label for="eat-custom-login-text-color"><?php _e('Hover background color', 'everest-admin-theme' ); ?></label>
					<input id='eat-custom-login-text-color' type="text" name='everest_admin_theme[custom_login][login_form][font_settings][login_button][hover][background-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['login_form']['font_settings']['login_button']['hover']['background-color']) && $plugin_settings['custom_login']['login_form']['font_settings']['login_button']['hover']['background-color'] != '' ){ echo $plugin_settings['custom_login']['login_form']['font_settings']['login_button']['hover']['background-color']; } ?>' />
				</div>
			</div>
			<div class="eat-font-settings">
				<div class='eat-username-font-settings'>
					<div class="eat-substyle-label"><?php _e( 'Register/lost your password link settings', 'everest-admin-theme' ); ?></div>
					<div class="eat-options-wrap">
						<label for="eat-custom-login-text-color"><?php _e('Text Color', 'everest-admin-theme' ); ?></label>
						<input id='eat-custom-login-text-color' type="text" name='everest_admin_theme[custom_login][login_form][font_settings][register_lost_password][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['login_form']['font_settings']['register_lost_password']['font-color']) && $plugin_settings['custom_login']['login_form']['font_settings']['register_lost_password']['font-color'] != '' ){ echo $plugin_settings['custom_login']['login_form']['font_settings']['register_lost_password']['font-color']; } ?>' />
					</div>
					
					<div class="eat-options-wrap">
						<label for="eat-custom-login-text-color"><?php _e('Font Family', 'everest-admin-theme' ); ?></label>
						<div class="eat-input-field-wrap">
							<select id='eat-admin-menu-font-family' name='everest_admin_theme[custom_login][login_form][font_settings][register_lost_password][google-fonts]' class="eat-selectbox-wrap">
								<option value=''><?php esc_html_e('Default', 'everest-admin-theme'); ?></option>
								<?php
								foreach($google_fonts as $key=>$value){
									?>
								<option value='<?php echo $value; ?>' <?php selected( $plugin_settings['custom_login']['login_form']['font_settings']['register_lost_password']['google-fonts'], $value); ?>><?php echo $value; ?></option>
								<?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="eat-options-wrap">
						<label for="eat-custom-login-text-color"><?php _e('Hover Text Color', 'everest-admin-theme' ); ?></label>
						<input id='eat-custom-login-text-color' type="text" name='everest_admin_theme[custom_login][login_form][font_settings][register_lost_password][hover][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['custom_login']['login_form']['font_settings']['register_lost_password']['hover']['font-color']) && $plugin_settings['custom_login']['login_form']['font_settings']['register_lost_password']['hover']['font-color'] != '' ){ echo $plugin_settings['custom_login']['login_form']['font_settings']['register_lost_password']['hover']['font-color']; } ?>' />
					</div>
				</div>
			</div>
		</div>

		<div class="eat-style-label"><?php _e( 'Hide/Show settings', 'everest-admin-theme' ); ?></div>
		<div class="eat-options-wrap">
			<label for="eat-custom-login-hide-wordpress-logo"><?php _e( 'Hide wordpress logo?', 'everest-admin-theme' ); ?></label>
			<input type="checkbox" id='eat-custom-login-hide-wordpress-logo' name='everest_admin_theme[custom_login][login_form][wordpress-logo][hide]' class='eat-hide-wordpress-logo' <?php if(isset($plugin_settings['custom_login']['login_form']['wordpress-logo']['hide'])){ ?> checked <?php } ?> />
			<label for='eat-custom-login-hide-wordpress-logo'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-custom-login-hide-remember-me-checkbox"><?php _e( 'Hide remember me checkbox?', 'everest-admin-theme' ); ?></label>
			<input type="checkbox" id='eat-custom-login-hide-remember-me-checkbox' name='everest_admin_theme[custom_login][login_form][remember-me-checkbox][hide]' class='eat-custom-login-hide-remember-me-checkbox' <?php if(isset($plugin_settings['custom_login']['login_form']['remember-me-checkbox']['hide'])){ ?> checked <?php } ?> />
			<label for='eat-custom-login-hide-remember-me-checkbox'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-custom-login-register-password-hide"><?php _e( 'Hide register/Lost password page link?', 'everest-admin-theme' ); ?></label>
			<input type="checkbox" id='eat-custom-login-register-password-hide' name='everest_admin_theme[custom_login][login_form][register-password-link][hide]' class='eat-custom-login-register-password-hide' <?php if(isset($plugin_settings['custom_login']['login_form']['register-password-link']['hide'])){ ?> checked <?php } ?> />
			<label for='eat-custom-login-register-password-hide'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-custom-login-hide-back-to-home-link"><?php _e( 'Back to home link?', 'everest-admin-theme' ); ?></label>
			<input type="checkbox" id='eat-custom-login-hide-back-to-home-link' name='everest_admin_theme[custom_login][login_form][back-to-home][hide]' class='eat-custom-login-hide-back-to-home-link' <?php if(isset($plugin_settings['custom_login']['login_form']['back-to-home']['hide'])){ ?> checked <?php } ?> />
			<label for='eat-custom-login-hide-back-to-home-link'></label>
		</div>
	</div>
</div>