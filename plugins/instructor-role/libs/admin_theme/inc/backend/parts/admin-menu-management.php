<div class="eat-tab-content eat-tab-admin-menu-management" style="display: none;">
	<div class="eat-tab-content-header">
		<div class="eat-tab-content-header-title"><?php _e('Admin Menu Management', 'everest-admin-theme'); ?></div>
	</div>
	<div class='eat-tab-content-body'>
	<?php
	///////////////////////////////////////////////////////////////////////////////////////////////
	$eat_admin_menu_slug_list =array();

//////////////////////////////////////////////////////////////////////////////////////////////
	?>
		<div class="eat-admin-menu-settings-wrap">
			<div class="eat-admin-menu-display-settings">
				<div class="eat-style-label"><?php _e('Display Settings', 'everest-admin-theme'); ?></div>
				<?php /* ?>
				<div class="eat-options-wrap">
					<label for='eat-admin-menu-width'><?php _e('Width of the admin bar(px)', 'everest-admin-theme'); ?></label>
					<input id='eat-admin-menu-width' type="number" name='everest_admin_theme[admin_menu][width]' class='eat-admin-menu-width' value='<?php if(isset($plugin_settings['admin_menu']['width']) && $plugin_settings['admin_menu']['width'] != '' ){ echo $plugin_settings['admin_menu']['width']; } ?>' />
				</div>
				<?php */ ?>

				<div class="eat-select-wrap">

					<div class="eat-options-wrap">
						<label for='eat-admin-menu-enable-admin-menu-manager'><?php _e('Enable admin menu manager', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap">
							<input type="checkbox" name="everest_admin_theme[admin_menu][enable_menu_manager]" <?php if(isset($plugin_settings['admin_menu']['enable_menu_manager'])){ ?> checked <?php } ?> class="eat-admin-bar-enable-option ec-checkbox-enable-option" id="eat-admin-menu-enable-admin-menu-manager" value="1">
						<label for="eat-admin-menu-enable-admin-menu-manager"></label>
						<div class="input-info"><?php _e( 'Please enable this option if you want to do custom menu ordering and renaming options in menu manager section.', 'everest-admin-theme' ); ?></div>
						</div>
					</div>

					<div class="eat-options-wrap">
						<label for='eat-admin-bar-backend-settings'><?php _e('Outer wrap menu background settings', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap eat-options-select-outer-wrap">
							<select name='everest_admin_theme[admin_menu][outer_background_settings][menu][type]' class="eat-selectbox-wrap eat-options-select-wrap">
								<option value ='default' <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['menu']['type']) ) { selected( $plugin_settings['admin_menu']['outer_background_settings']['menu']['type'], 'default' ); } ?>><?php _e('Default', 'everest-admin-theme'); ?></option>
								<option value ='background-color' <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['menu']['type']) ) { selected( $plugin_settings['admin_menu']['outer_background_settings']['menu']['type'], 'background-color' ); } ?>><?php _e('Background color', 'everest-admin-theme'); ?></option>
								<option value ='image' <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['menu']['type']) ) { selected( $plugin_settings['admin_menu']['outer_background_settings']['menu']['type'], 'image' ); } ?>><?php _e('Image', 'everest-admin-theme'); ?></option>
							</select>
						</div>
					</div>

					<div class="eat-options-select-content-wrap">
						<div class="eat-common-content-wrap eat-background-color-content-wrap" style='display: <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['menu']['type']) && $plugin_settings['admin_menu']['outer_background_settings']['menu']['type'] =='background-color' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class="eat-options-wrap">
								<label for ="eat-background-background-color"><?php _e('Background Color', 'everest-admin-theme' ); ?></label>
								<input id  ='eat-background-background-color' type="text" name='everest_admin_theme[admin_menu][outer_background_settings][menu][background-color][color]' class='eat-color-picker' data-alpha="true" value="<?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['menu']['background-color']['color']) && $plugin_settings['admin_menu']['outer_background_settings']['menu']['background-color']['color'] != '' ){ echo $plugin_settings['admin_menu']['outer_background_settings']['menu']['background-color']['color']; } ?>" />
							</div>
						</div>

						<div class="eat-common-content-wrap eat-image-content-wrap" style='display: <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['menu']['type']) && $plugin_settings['admin_menu']['outer_background_settings']['menu']['type'] =='image' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class="eat-image-selection-wrap">
								<div class="eat-options-wrap">
									<label for="eat-background-image-url"><?php _e( 'Image Upload: ', 'everest-admin-theme' ); ?></label>
									<div class="eat-input-field-wrap">
										<input type ="text" id='eat-background-image-url' name='everest_admin_theme[admin_menu][outer_background_settings][menu][image][url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['menu']['image']['url']) && $plugin_settings['admin_menu']['outer_background_settings']['menu']['image']['url'] != '' ){ echo $plugin_settings['admin_menu']['outer_background_settings']['menu']['image']['url']; } ?>' />
										<input type ="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Image', 'everest-admin-theme'); ?>' />
									</div>
								</div>
								<div class='eat-image-preview eat-image-placeholder'>
									<img src ='<?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['menu']['image']['url']) && $plugin_settings['admin_menu']['outer_background_settings']['menu']['image']['url'] != '' ){ echo $plugin_settings['admin_menu']['outer_background_settings']['menu']['image']['url']; } ?>' />
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="eat-select-wrap">
					<div class="eat-options-wrap">
						<label for='eat-admin-bar-backend-settings'><?php _e( 'Outer wrap submenu background settings', 'everest-admin-theme' ); ?></label>
						<div class="eat-input-field-wrap eat-options-select-outer-wrap">
							<select name='everest_admin_theme[admin_menu][outer_background_settings][sub_menu][type]' class="eat-selectbox-wrap eat-options-select-wrap">
								<option value ='default' <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['type'])){ selected( $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['type'], 'default' ); } ?>><?php _e('Default', 'everest-admin-theme'); ?></option>
								<option value ='background-color' <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['type'])){ selected( $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['type'], 'background-color' ); } ?>><?php _e('Background color', 'everest-admin-theme'); ?></option>
								<option value ='image' <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['type'])){ selected( $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['type'], 'image' ); } ?>><?php _e('Image', 'everest-admin-theme'); ?></option>
							</select>
						</div>
					</div>

					<div class="eat-options-select-content-wrap">
						<div class="eat-common-content-wrap eat-background-color-content-wrap" style='display: <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['type']) && $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['type'] =='background-color' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class="eat-options-wrap">
								<label for ="eat-background-background-color"><?php _e('Background Color', 'everest-admin-theme' ); ?></label>
								<input id  ='eat-background-background-color' type="text" name='everest_admin_theme[admin_menu][outer_background_settings][sub_menu][background-color][color]' class='eat-color-picker' data-alpha="true" value="<?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['background-color']['color']) && $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['background-color']['color'] != '' ){ echo $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['background-color']['color']; } ?>" />
							</div>
						</div>

						<div class="eat-common-content-wrap eat-image-content-wrap" style='display: <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['type']) && $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['type'] =='image' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class="eat-image-selection-wrap">
								<div class="eat-options-wrap">
									<label for="eat-background-image-url"><?php _e( 'Image Upload: ', 'everest-admin-theme' ); ?></label>
									<div class="eat-input-field-wrap">
										<input type ="text" id='eat-background-image-url' name='everest_admin_theme[admin_menu][outer_background_settings][sub_menu][image][url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['image']['url']) && $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['image']['url'] != '' ){ echo $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['image']['url']; } ?>' />
										<input type ="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Image', 'everest-admin-theme'); ?>' />
									</div>
								</div>
								<div class='eat-image-preview eat-image-placeholder'>
									<img src ='<?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['image']['url']) && $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['image']['url'] != '' ){ echo $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['image']['url']; } ?>' />
								</div>
							</div>

							<div class="eat-checkbox-outer-wrap">
								<div class="eat-options-wrap">
									<label for="eat-admin-menu-submenu-overlay-enable"><?php _e( 'Enable Overlay?', 'everest-admin-theme' ); ?></label>
									<input type="checkbox" id='eat-admin-menu-submenu-overlay-enable' name='everest_admin_theme[admin_menu][outer_background_settings][sub_menu][overlay][enable]' class='eat-image-overlay-enable-option' <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['overlay']['enable'])){ ?> checked <?php } ?> />
									<label for='eat-admin-menu-submenu-overlay-enable'></label>
								</div>

								<div class="eat-checkbox-checked-options" style='display: <?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['overlay']['enable'])){ ?> block; <?php }else{ ?> none; <?php } ?>'>
									<div class="eat-options-wrap">
										<label for="eat-custom-login-background-video-overlay-color"><?php _e( 'Overlay Color', 'everest-admin-theme' ); ?></label>
										<input type="text" id='eat-custom-login-background-video-overlay-color' class='eat-color-picker' name='everest_admin_theme[admin_menu][outer_background_settings][sub_menu][overlay][color]' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['overlay']['color']) && $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['overlay']['color'] != '' ){ echo $plugin_settings['admin_menu']['outer_background_settings']['sub_menu']['overlay']['color']; } ?>' />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
			
			<div class="eat-admin-menu-menu-submenu-settings">
				<div class="eat-admin-menu-menu-settings">
					<div class="eat-style-label"><?php _e('Menu Settings', 'everest-admin-theme'); ?></div>
					<div class="eat-select-wrap">
						<div class="eat-options-wrap">
							<label for='eat-admin-menu-menu-backend-selection'><?php _e('Menu Background selection', 'everest-admin-theme'); ?></label>
							<div class="eat-input-field-wrap eat-options-select-outer-wrap">
								<select id='eat-admin-menu-menu-backend-selection' name='everest_admin_theme[admin_menu][menu][background_selection][type]' class="eat-selectbox-wrap eat-options-select-wrap">
									<option value='default' <?php selected( $plugin_settings['admin_menu']['menu']['background_selection']['type'], 'default' ); ?>><?php _e('Default', 'everest-admin-theme'); ?></option>
									<option value='background-color' <?php selected( $plugin_settings['admin_menu']['menu']['background_selection']['type'], 'background-color' ); ?>><?php _e('Background color', 'everest-admin-theme'); ?></option>
									<option value='image' <?php selected( $plugin_settings['admin_menu']['menu']['background_selection']['type'], 'image' ); ?>><?php _e('Image', 'everest-admin-theme'); ?></option>
								</select>
							</div>
						</div>

						<div class="eat-options-select-content-wrap">
							<div class="eat-common-content-wrap eat-background-color-content-wrap" style='display: <?php if(isset($plugin_settings['admin_menu']['menu']['background_selection']['type']) && $plugin_settings['admin_menu']['menu']['background_selection']['type'] =='background-color' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
								<div class="eat-options-wrap">
									<label for="eat-admin-menu-menu-background-color"><?php _e('Background Color', 'everest-admin-theme' ); ?></label>
									<input id='eat-admin-menu-menu-background-color' type="text" name='everest_admin_theme[admin_menu][menu][background_selection][background-color][color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['menu']['background_selection']['background-color']['color']) && $plugin_settings['admin_menu']['menu']['background_selection']['background-color']['color'] != '' ){ echo $plugin_settings['admin_menu']['menu']['background_selection']['background-color']['color']; } ?>' />
								</div>
							</div>

							<div class="eat-common-content-wrap eat-image-content-wrap" style='display: <?php if(isset($plugin_settings['admin_menu']['menu']['background_selection']['type']) && $plugin_settings['admin_menu']['menu']['background_selection']['type'] =='image' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
								<div class="eat-image-selection-wrap">
									<div class="eat-options-wrap">
										<label for="eat-admin-menu-background-image-url"><?php _e( 'Image Upload: ', 'everest-admin-theme' ); ?></label>
										<div class="eat-input-field-wrap">
											<input type="text" id='eat-admin-menu-background-image-url' name='everest_admin_theme[admin_menu][menu][background_selection][image][url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['admin_menu']['menu']['background_selection']['image']['url']) && $plugin_settings['admin_menu']['menu']['background_selection']['image']['url'] != '' ){ echo $plugin_settings['admin_menu']['menu']['background_selection']['image']['url']; } ?>' />
											<input type="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Image', 'everest-admin-theme'); ?>' />
										</div>
									</div>
									<div class='eat-image-preview eat-image-placeholder'>
										<img src='<?php if(isset($plugin_settings['admin_menu']['menu']['background_selection']['image']['url']) && $plugin_settings['admin_menu']['menu']['background_selection']['image']['url'] != '' ){ echo $plugin_settings['admin_menu']['menu']['background_selection']['image']['url']; } ?>' />
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- <div class="eat-options-wrap">
						<label for='eat-admin-menu-menu-icon-color'><?php _e('Icon color', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap">
							<input id='eat-admin-menu-menu-icon-color' type="text" name='everest_admin_theme[admin_menu][menu][icon-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['menu']['icon-color']) && $plugin_settings['admin_menu']['menu']['icon-color'] != '' ){ echo $plugin_settings['admin_menu']['menu']['icon-color']; } ?>' />
						</div>
					</div> -->

					<!-- <div class="eat-options-wrap">
						<label for='eat-admin-menu-menu-icon-bg-color'><?php _e('Icon background color', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap">
							<input id='eat-admin-menu-menu-icon-bg-color' type="text" name='everest_admin_theme[admin_menu][menu][icon-bg-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['menu']['icon-bg-color']) && $plugin_settings['admin_menu']['menu']['icon-bg-color'] != '' ){ echo $plugin_settings['admin_menu']['menu']['icon-bg-color']; } ?>' />
						</div>
					</div> -->

					<div class="eat-options-wrap">
						<label for='eat-admin-menu-menu-font-color'><?php _e('Font color', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap">
							<input id='eat-admin-menu-menu-font-color' type="text" name='everest_admin_theme[admin_menu][menu][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['menu']['font-color']) && $plugin_settings['admin_menu']['menu']['font-color'] != '' ){ echo $plugin_settings['admin_menu']['menu']['font-color']; } ?>' />
						</div>
					</div>
					
					<?php /* ?>
					<div class="eat-options-wrap">
						<label for='eat-admin-menu-menu-font-size'><?php _e('Font size (px)', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap">
							<input id='eat-admin-menu-menu-font-size' type="number" name='everest_admin_theme[admin_menu][menu][font-size]' value="<?php if(isset($plugin_settings['admin_menu']['menu']['font-size']) && $plugin_settings['admin_menu']['menu']['font-size'] != '' ){ echo $plugin_settings['admin_menu']['menu']['font-size']; } ?>"/>
						</div>
					</div>
					<?php */ ?>

					<div class="eat-options-wrap">
						<label for='eat-admin-menu-font-family'><?php _e('Font Family', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap">
						<select id='eat-admin-menu-font-family' name='everest_admin_theme[admin_menu][menu][google-fonts]' class="eat-selectbox-wrap">
							<option value=''><?php esc_html_e('Default', 'everest-admin-theme'); ?></option>
							<?php
							foreach($google_fonts as $key=>$value){
								// $key_value = str_replace(' ', '+', $value);
								?>
								<option value='<?php echo $value; ?>' <?php selected( $plugin_settings['admin_menu']['menu']['google-fonts'], $value); ?>><?php echo $value; ?></option>
								<?php
							}
							?>
							</select>
						</div>
					</div>
				</div>

				<div class="eat-admin-menu-menu-hover-settings">
					<div class="eat-style-label"><?php esc_html_e('Menu hover settings', 'everest-admin-theme'); ?></div>
					<div class="eat-options-wrap">
						<label for='eat-admin-menu-menu-hover-background-color'><?php _e('Hover background color', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap">
							<input id='eat-admin-menu-menu-hover-background-color' type="text" name='everest_admin_theme[admin_menu][menu][hover][background-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['menu']['hover']['background-color']) && $plugin_settings['admin_menu']['menu']['hover']['background-color'] != '' ){ echo $plugin_settings['admin_menu']['menu']['hover']['background-color']; } ?>' />
						</div>
					</div>

					<div class="eat-options-wrap">
						<label for='eat-admin-menu-menu-hover-font-color'><?php _e('Hover Font color', 'everest-admin-theme'); ?></label>
						<input id='eat-admin-menu-menu-hover-font-color' type="text" name='everest_admin_theme[admin_menu][menu][hover][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['menu']['hover']['font-color']) && $plugin_settings['admin_menu']['menu']['hover']['font-color'] != '' ){ echo $plugin_settings['admin_menu']['menu']['hover']['font-color']; } ?>' />
					</div>
				</div>
				
				<div class="eat-admin-menu-submenu-settings">
					
				</div>

				<div class="eat-admin-menu-submenu-settings">
					<div class="eat-style-label"><?php esc_html_e('Submenu Settings', 'everest-admin-theme'); ?></div>
					<div class="eat-select-wrap">
						<div class="eat-options-wrap">
							<label for='eat-admin-menu-submenu-backend-settings'><?php _e( 'Menu Background selection', 'everest-admin-theme' ); ?></label>
							<div class="eat-input-field-wrap eat-options-select-outer-wrap">
								<select id='eat-admin-menu-submenu-backend-settings' name='everest_admin_theme[admin_menu][sub_menu][background_selection][type]' class="eat-selectbox-wrap eat-options-select-wrap">
									<option value='default' <?php selected($plugin_settings['admin_menu']['sub_menu']['background_selection']['type'], 'default'); ?>><?php _e('Default', 'everest-admin-theme'); ?></option>
									<option value='background-color' <?php selected($plugin_settings['admin_menu']['sub_menu']['background_selection']['type'], 'background-color'); ?> ><?php _e('Background color', 'everest-admin-theme'); ?></option>
									<option value='image' <?php selected($plugin_settings['admin_menu']['sub_menu']['background_selection']['type'], 'image'); ?> ><?php _e('Image', 'everest-admin-theme'); ?></option>
								</select>
							</div>
						</div>

						<div class="eat-options-select-content-wrap">
							<div class="eat-common-content-wrap eat-background-color-content-wrap" style='display: <?php if(isset($plugin_settings['admin_menu']['sub_menu']['background_selection']['type']) && $plugin_settings['admin_menu']['sub_menu']['background_selection']['type'] =='background-color' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
								<div class="eat-options-wrap">
									<label for="eat-admin-menu-submenu-background-color"><?php _e('Background Color', 'everest-admin-theme' ); ?></label>
									<input id='eat-admin-menu-submenu-background-color' type="text" name='everest_admin_theme[admin_menu][sub_menu][background_selection][background-color][color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['sub_menu']['background_selection']['background-color']['color']) && $plugin_settings['admin_menu']['sub_menu']['background_selection']['background-color']['color'] != '' ){ echo $plugin_settings['admin_menu']['sub_menu']['background_selection']['background-color']['color']; } ?>' />
								</div>
							</div>

							<div class="eat-common-content-wrap eat-image-content-wrap" style='display: <?php if(isset($plugin_settings['admin_menu']['sub_menu']['background_selection']['type']) && $plugin_settings['admin_menu']['sub_menu']['background_selection']['type'] =='image' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
								<div class="eat-image-selection-wrap">
									<div class="eat-options-wrap">
										<label for="eat-admin-menu-submenu-image-url"><?php _e( 'Image Upload: ', 'everest-admin-theme' ); ?></label>
										<div class="eat-input-field-wrap">
											<input type="text" id='eat-admin-menu-submenu-image-url' name='everest_admin_theme[admin_menu][sub_menu][background_selection][image][url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['admin_menu']['sub_menu']['background_selection']['image']['url']) && $plugin_settings['admin_menu']['sub_menu']['background_selection']['image']['url'] != '' ){ echo $plugin_settings['admin_menu']['sub_menu']['background_selection']['image']['url']; } ?>' />
											<input type="button" class='eat-button eat-image-upload-button' value='<?php esc_html_e('Upload Image', 'everest-admin-theme'); ?>' />
										</div>
									</div>
									<div class='eat-image-preview eat-image-placeholder'>
										<img src='<?php if(isset($plugin_settings['admin_menu']['sub_menu']['background_selection']['image']['url']) && $plugin_settings['admin_menu']['sub_menu']['background_selection']['image']['url'] != '' ){ echo $plugin_settings['admin_menu']['sub_menu']['background_selection']['image']['url']; } ?>' />
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="eat-options-wrap">
						<label for='eat-admin-menu-submenu-font-color'><?php _e('Font color', 'everest-admin-theme'); ?></label>
						<input id='eat-admin-menu-submenu-font-color' type="text" name='everest_admin_theme[admin_menu][sub_menu][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['sub_menu']['font-color']) && $plugin_settings['admin_menu']['sub_menu']['font-color'] != '' ){ echo $plugin_settings['admin_menu']['sub_menu']['font-color']; } ?>' />
					</div>

					<?php /* ?>
					<div class="eat-options-wrap">
						<label for='eat-admin-menu-submenu-font-size'><?php _e('Font size (px)', 'everest-admin-theme'); ?></label>
						<div class="eat-item-input-field-wrap">
							<input id='eat-admin-menu-submenu-font-size' type="number" name='everest_admin_theme[admin_menu][sub_menu][font-size]' value="<?php if(isset($plugin_settings['admin_menu']['sub_menu']['font-size']) && $plugin_settings['admin_menu']['sub_menu']['font-size'] !=''){ echo esc_html_e($plugin_settings['admin_menu']['sub_menu']['font-size']); } ?>" />
						</div>
					</div>
					<?php */ ?>

					<div class="eat-options-wrap">
						<label for='eat-admin-menu-submenu-font-family'><?php _e('Font Family', 'everest-admin-theme'); ?></label>
						<div class="eat-item-input-field-wrap eat-input-field-wrap">
						<select id='eat-admin-menu-submenu-font-family' name='everest_admin_theme[admin_menu][sub_menu][google-fonts]' class="eat-selectbox-wrap">
							<option value=''><?php esc_html_e('Default', 'everest-admin-theme'); ?></option>
							<?php
							foreach($google_fonts as $key=>$value){
								// $key_value = str_replace(' ', '+', $value);
								?>
								<option value='<?php echo $value; ?>' <?php selected($plugin_settings['admin_menu']['sub_menu']['google-fonts'], $value); ?>><?php echo $value; ?></option>
								<?php
							}
							?>
							</select>
						</div>
					</div>
				</div>
				<div class="eat-admin-bar-submenu-hover-settings">
					<div class="eat-style-label"><?php esc_html_e('Submenu hover settings', 'everest-admin-theme'); ?></div>
					<div class="eat-options-wrap">
						<label for='eat-admin-menu-submenu-hover-background-color'><?php _e('Hover background color', 'everest-admin-theme'); ?></label>
						<input id='eat-admin-menu-submenu-hover-background-color' type="text" name='everest_admin_theme[admin_menu][sub_menu][hover][background-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['sub_menu']['hover']['background-color']) && $plugin_settings['admin_menu']['sub_menu']['hover']['background-color'] != '' ){ echo $plugin_settings['admin_menu']['sub_menu']['hover']['background-color']; } ?>' />
					</div>

					<div class="eat-options-wrap">
						<label for='eat-admin-menu-submenu-hover-font-color'><?php _e('Hover Font color', 'everest-admin-theme'); ?></label>
						<input id='eat-admin-menu-submenu-hover-font-color' type="text" name='everest_admin_theme[admin_menu][sub_menu][hover][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_menu']['sub_menu']['hover']['font-color']) && $plugin_settings['admin_menu']['sub_menu']['hover']['font-color'] != '' ){ echo $plugin_settings['admin_menu']['sub_menu']['hover']['font-color']; } ?>' />
					</div>
				</div>
			</div>
		</div>

	</div>
</div>