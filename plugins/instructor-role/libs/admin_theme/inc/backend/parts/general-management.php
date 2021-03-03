<div class='eat-tab-content eat-tab-general-management' style='display: none;'>
	<div class="eat-tab-content-header">
		<div class='eat-tab-content-header-title'><?php _e('General Management' , 'everest-admin-theme'); ?></div>
	</div>
	<div class='eat-tab-content-body'>
		<div class="eat-general-settings-options-wrap eat-options-wrap-outer">
				<div class="eat-select-options-wrap eat-admin-menu-header-wrap">
					<div class="eat-options-wrap">
						<label for="eat-background-option"><?php _e('Admin menu header settings', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap eat-background-select-wrap">
							<select id='eat-background-options' name='everest_admin_theme[general-settings][admin-menu-header][type]' class="eat-selectbox-wrap eat-select-options ">
								<option value='' ><?php _e( 'None', 'everest-admin-theme' ); ?></option>
								<option value='image' <?php selected( $plugin_settings['general-settings']['admin-menu-header']['type'], 'image' ); ?> > <?php _e( 'Image', 'everest-admin-theme'); ?></option>
								<option value='texts' <?php selected( $plugin_settings['general-settings']['admin-menu-header']['type'], 'texts' ); ?>><?php _e( 'Texts', 'everest-admin-theme'); ?></option>
							</select>
						</div>
					</div>

					<div class="eat-select-content-wrap">
						<div class="eat-background-image-content-wrap eat-image eat-common-content-wrap" style="display: <?php if(isset($plugin_settings['general-settings']['admin-menu-header']['type']) && $plugin_settings['general-settings']['admin-menu-header']['type'] =='image' ){ ?> block; <?php }else{ ?> none; <?php } ?>">
							<div class="eat-image-selection-wrap">
								<div class="eat-options-wrap">
									<label for="eat-background-image-url"><?php _e( 'Image Upload: ', 'everest-admin-theme' ); ?></label>
									<div class="eat-input-field-wrap">
										<input type="text" id='eat-background-image-url' name='everest_admin_theme[general-settings][admin-menu-header][image][url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['general-settings']['admin-menu-header']['image']['url']) && $plugin_settings['general-settings']['admin-menu-header']['image']['url'] != '' ){ echo $plugin_settings['general-settings']['admin-menu-header']['image']['url']; } ?>' />
										<input type="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Image', 'everest-admin-theme'); ?>' />
									</div>
								</div>
								<div class='eat-image-preview eat-image-placeholder'>
									<img src='<?php if(isset($plugin_settings['general-settings']['admin-menu-header']['image']['url']) && $plugin_settings['general-settings']['admin-menu-header']['image']['url'] != '' ){ echo $plugin_settings['general-settings']['admin-menu-header']['image']['url']; } ?>' />
								</div>
							</div>
						</div>

						<div class="eat-background-color-content eat-texts eat-common-content-wrap" style="display: <?php if(isset($plugin_settings['general-settings']['admin-menu-header']['type']) && $plugin_settings['general-settings']['admin-menu-header']['type'] =='texts' ){ ?> block; <?php }else{ ?> none; <?php } ?>">
							<div class="eat-background-color-content-wrap">
								<div class="eat-options-wrap">
									<label for="eat-background-background-color"><?php _e('Title', 'everest-admin-theme' ); ?></label>
									<input id='eat-background-background-color' type="text" name='everest_admin_theme[general-settings][admin-menu-header][text][title][text]' class='' value='<?php if(isset($plugin_settings['general-settings']['admin-menu-header']['text']['title']['text']) && $plugin_settings['general-settings']['admin-menu-header']['text']['title']['text'] != '' ){ echo $plugin_settings['general-settings']['admin-menu-header']['text']['title']['text']; } ?>' />
								</div>
								<div class="eat-options-wrap">
									<label for="eat-background-background-color"><?php _e('Sub Title', 'everest-admin-theme' ); ?></label>
									<input id='eat-background-background-color' type="text" name='everest_admin_theme[general-settings][admin-menu-header][text][subtitle][text]' class='' value='<?php if(isset($plugin_settings['general-settings']['admin-menu-header']['text']['subtitle']['text']) && $plugin_settings['general-settings']['admin-menu-header']['text']['subtitle']['text'] != '' ){ echo $plugin_settings['general-settings']['admin-menu-header']['text']['subtitle']['text']; } ?>' />
								</div>

								<div class='eat-font-settings'>
									<div class="eat-style-label">Title Font Settings</div>
									<div class="eat-options-wrap">
										<label for='eat-general-settings'><?php _e('Font Family', 'everest-admin-theme'); ?></label>
										<div class="eat-item-input-field-wrap eat-input-field-wrap">
											<select id='eat-google-fonts' name='everest_admin_theme[general-settings][admin-menu-header][text][title][font-settings][google-fonts]' class="eat-selectbox-wrap">
												<option value=''><?php esc_html_e('Default', 'everest-admin-theme' ); ?></option>
												<?php
												foreach($google_fonts as $key=>$value){
													?>
													<option value='<?php echo $value; ?>' <?php if(isset($plugin_settings['general-settings']['admin-menu-header']['text']['title']['font-settings']['google-fonts'])){selected( $plugin_settings['general-settings']['admin-menu-header']['text']['title']['font-settings']['google-fonts'], $value ); } ?>><?php echo $value; ?></option>
													<?php
												}
												?>
											</select>
										</div>
									</div>
									<div class="eat-options-wrap">
										<label for='eat-general-settings'><?php _e('Font color', 'everest-admin-theme'); ?></label>
										<input id='ec-background-background-color' type="text" name='everest_admin_theme[general-settings][admin-menu-header][text][title][font-settings][font-color]' class='eat-color-picker' data-alpha="true" value="<?php if(isset($plugin_settings['general-settings']['admin-menu-header']['text']['title']['font-settings']['font-color']) && $plugin_settings['general-settings']['admin-menu-header']['text']['title']['font-settings']['font-color'] != '' ){ echo $plugin_settings['general-settings']['admin-menu-header']['text']['title']['font-settings']['font-color']; } ?>" />
									</div>
								</div>

								<div class='eat-font-settings'>
									<div class="eat-style-label"><?php _e( 'Sub Title Font Settings', 'everest-admin-theme' ); ?></div>
									<div class="eat-options-wrap">
										<label for='eat-general-settings'><?php _e( 'Font Family', 'everest-admin-theme' ); ?></label>
										<div class="eat-item-input-field-wrap eat-input-field-wrap">
											<select id='eat-google-fonts' name='everest_admin_theme[general-settings][admin-menu-header][text][subtitle][font-settings][google-fonts]' class="eat-selectbox-wrap">
												<option value=''><?php esc_html_e('Default', 'everest-admin-theme' ); ?></option>
												<?php
												foreach($google_fonts as $key=>$value){
													?>
													<option value='<?php echo $value; ?>' <?php if(isset($plugin_settings['general-settings']['admin-menu-header']['text']['subtitle']['font-settings']['google-fonts'])){selected( $plugin_settings['general-settings']['admin-menu-header']['text']['subtitle']['font-settings']['google-fonts'], $value ); } ?>><?php echo $value; ?></option>
													<?php
												}
												?>
											</select>
										</div>
									</div>
									<div class="eat-options-wrap">
										<label for='eat-general-settings'><?php _e('Font color', 'everest-admin-theme'); ?></label>
										<input id='ec-background-background-color' type="text" name='everest_admin_theme[general-settings][admin-menu-header][text][subtitle][font-settings][font-color]' class='eat-color-picker' data-alpha="true" value="<?php if(isset($plugin_settings['general-settings']['admin-menu-header']['text']['subtitle']['font-settings']['font-color']) && $plugin_settings['general-settings']['admin-menu-header']['text']['subtitle']['font-settings']['font-color'] != '' ){ echo $plugin_settings['general-settings']['admin-menu-header']['text']['subtitle']['font-settings']['font-color']; } ?>" />
									</div>
								</div>

							</div>
						</div>

						<div class="eat-admin-menu-header-background-settings eat-common-content-wrap eat-background" style="display:<?php if(isset($plugin_settings['general-settings']['admin-menu-header']['type']) && ($plugin_settings['general-settings']['admin-menu-header']['type'] =='image' || $plugin_settings['general-settings']['admin-menu-header']['type'] =='texts' )){ ?> block; <?php }else{ ?> none; <?php } ?>">
							<div class="eat-substyle-label"><?php _e("Background Settings", 'everest-admin-theme'); ?></div>
							<div class="eat-background-color-content-wrap">
								<div class="eat-options-wrap">
									<label for="eat-background-background-color"><?php _e('Background Color', 'everest-admin-theme' ); ?></label>
									<input id='eat-background-background-color' type="text" name='everest_admin_theme[general-settings][admin-menu-header][background-color][color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['general-settings']['admin-menu-header']['background-color']['color']) && $plugin_settings['general-settings']['admin-menu-header']['background-color']['color'] != '' ){ echo $plugin_settings['general-settings']['admin-menu-header']['background-color']['color']; } ?>' />
								</div>
							</div>
						</div>
					</div>
				</div>
		</div>

		<div class="eat-style-label" ><?php _e('Favicon Settings', 'everest-admin-theme'); ?></div>
		<div class="eat-image-selection-wrap">
			<div class="eat-general-settings-options-wrap eat-options-wrap">
				<label for='eat-general-settings'><?php _e('Custom Favicon', 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
					<input type="url" id='favicon-upload' name='everest_admin_theme[general-settings][favicon][url]' class='eat-image-upload-url' value="<?php if(isset($plugin_settings['general-settings']['favicon']['url']) && $plugin_settings['general-settings']['favicon']['url'] != '' ){ echo esc_url($plugin_settings['general-settings']['favicon']['url']); } ?>" />
					<input type="button" class='eat-button eat-image-upload-button' value="<?php _e('Upload Image', 'everest-admin-theme'); ?>" />
				</div>
			</div>
			<div class="eat-image-preview eat-image-placeholder">
				<img src="<?php if(isset($plugin_settings['general-settings']['favicon']['url']) && $plugin_settings['general-settings']['favicon']['url'] != '' ){ echo esc_url($plugin_settings['general-settings']['favicon']['url']); } ?>" alt='site favicon'/>
			</div>
		</div>
	</div>
</div>