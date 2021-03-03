<div class="eat-tab-content eat-tab-admin-bar-management" style="display: none;">
	<div class="eat-tab-content-header">
		<div class="eat-tab-content-header-title"><?php _e('Admin bar Management', 'everest-admin-theme'); ?></div>
	</div>
	<div class="eat-tab-content-body">
		<div class="eat-options-wrap">
			<label for='eat-admin-bar-template-selection'><?php _e('Admin bar position', 'everest-admin-theme'); ?></label>
			<div class="eat-input-field-wrap">
				<select id='eat-admin-bar-template-selection' name='everest_admin_theme[admin_bar][layout]' class="eat-selectbox-wrap eat-selectbox-wrap eat-admin-bar-template-selection eat-select-options eat-dropdown-selector" >
					<option value='fixed' <?php selected($plugin_settings['admin_bar']['layout'], 'fixed'); ?> ><?php _e( 'Fixed', 'everest-admin-theme' ); ?></option>
					<option value='absolute' <?php selected($plugin_settings['admin_bar']['layout'], 'absolute'); ?>> <?php _e( 'Absolute', 'everest-admin-theme'); ?></option>
				</select>
			</div>
		</div>

		<div class="eat-options-wrap">
			<label for='eat-admin-bar-hide-in-frontend'><?php _e('Hide Admin bar in frontend', 'everest-admin-theme'); ?></label>
			<div class="eat-input-field-wrap">
				<div class="eat-input-field-wrap">
					<input type="checkbox" name="everest_admin_theme[admin_bar][hide_in_frontend]" <?php if(isset($plugin_settings['admin_bar']['hide_in_frontend'])){ ?> checked <?php } ?> class="eat-admin-bar-enable-option ec-checkbox-enable-option" id="eat-admin-bar-hide-in-frontend" value="1">
				<label for="eat-admin-bar-hide-in-frontend"></label>
				</div>
			</div>
		</div>

		<div class="eat-options-wrap">
			<div class="eat-style-label"><?php _e('Hide/Show Options', 'everest-admin-theme'); ?></div>
			<div class="eat-admin-bar-nodes-wrap">
				<?php
					$admin_bar_items = get_option('eat_admin_bar_nodes');
					$hide_show_option = isset($plugin_settings['admin_bar']['hide_show_opt']) ? $plugin_settings['admin_bar']['hide_show_opt']: array();
					if ( ! empty( $admin_bar_items ) && is_array( $admin_bar_items ) ) {
						foreach ( $admin_bar_items as $key => $value ) {
							$is_parent = ! empty( $value->parent );
							// No title on the item.
							if ( ! $value->title ) {
								$value->title = '<b><i>' . esc_attr__( 'No Title!', 'everest-admin-theme' ) . '</i></b>';
							}

							$item_string  = '&bull; ';
							$before_title = '<b>';
							$after_title  = '</b> <small>' . esc_attr__( 'Group', 'everest-admin-theme' ) . '</small>';
							if ( $is_parent ) {
								$item_string = '&mdash; ';
								$before_title = '';
								$after_title  = '';
							}
							?>
							<div class="eat-options-wrap <?php if(!$is_parent){ echo "parent-wrap"; }else{ echo "child-wrap"; } ?>">
								<label for='eat-admin-bar-wordpress-org-submenu-<?php echo $key; ?>' ><?php echo strip_tags($value->title); ?></label>
								<div class="eat-input-field-wrap">
									<input type="checkbox" name='everest_admin_theme[admin_bar][hide_show_opt][<?php echo $key; ?>]' class='ec-checkbox-image-border ec-checkbox-enable-option' id='eat-admin-bar-wordpress-org-submenu-<?php echo $key; ?>' <?php if(isset($hide_show_option[$key])){ echo "checked"; } ?> value="<?php echo $key; ?>" />
									<label for='eat-admin-bar-wordpress-org-submenu-<?php echo $key; ?>' ></label>
								</div>
							</div>
							<?php
						}
					}
				?>
			</div>
		</div>

		<div class="eat-admin-bar-display-settings">
			<div class="eat-admin-bar-outer-backend-settings">
				<div class="eat-style-label"><?php _e('Outer wrapper Menu background Settings', 'everest-admin-theme'); ?></div>
				<div class="eat-select-wrap">
					<div class="eat-options-wrap">
						<label for='eat-admin-bar-backend-settings'><?php _e('Menu Background selection', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap eat-options-select-outer-wrap">
							<select name='everest_admin_theme[admin_bar][outer_background_settings][menu][background_selection][type]' class="eat-selectbox-wrap eat-options-select-wrap">
								<option value ='default' <?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['type'])){ selected( $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['type'], 'default' ); } ?>><?php _e('Default', 'everest-admin-theme'); ?></option>
								<option value ='background-color' <?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['type'])){ selected( $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['type'], 'background-color' ); } ?>><?php _e('Background color', 'everest-admin-theme'); ?></option>
								<option value ='image' <?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['type'])){ selected( $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['type'], 'image' ); } ?>><?php _e('Image', 'everest-admin-theme'); ?></option>
							</select>
						</div>
					</div>

					<div class="eat-options-select-content-wrap">
						<div class="eat-common-content-wrap eat-background-color-content-wrap" style='display: <?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['type']) && $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['type'] =='background-color' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class="eat-options-wrap">
								<label for="eat-background-background-color"><?php _e('Background Color', 'everest-admin-theme' ); ?></label>
								<input id='eat-background-background-color' type="text" name='everest_admin_theme[admin_bar][outer_background_settings][menu][background_selection][background-color][color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['background-color']['color']) && $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['background-color']['color'] != '' ){ echo $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['background-color']['color']; } ?>' />
							</div>
						</div>

						<div class="eat-common-content-wrap eat-image-content-wrap" style='display: <?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['type']) && $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['type'] =='image' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class="eat-image-selection-wrap">
								<div class="eat-options-wrap">
									<label for="eat-background-image-url"><?php _e( 'Image Upload: ', 'everest-admin-theme' ); ?></label>
									<div class="eat-input-field-wrap">
										<input type="text" id='eat-background-image-url' name='everest_admin_theme[admin_bar][outer_background_settings][menu][background_selection][image][url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['image']['url']) && $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['image']['url'] != '' ){ echo $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['image']['url']; } ?>' />
										<input type="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Image', 'everest-admin-theme'); ?>' />
									</div>
								</div>
								<div class='eat-image-preview eat-image-placeholder'>
									<img src='<?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['image']['url']) && $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['image']['url'] != '' ){ echo $plugin_settings['admin_bar']['outer_background_settings']['menu']['background_selection']['image']['url']; } ?>' />
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="eat-style-label"><?php _e('Outer wrapper Submenu background Settings', 'everest-admin-theme'); ?></div>
				<div class="eat-select-wrap">
					<div class="eat-options-wrap">
						<label for='eat-admin-bar-backend-settings'><?php _e('Submenu Background selection', 'everest-admin-theme'); ?></label>
						<div class="eat-input-field-wrap eat-options-select-outer-wrap">
							<select name='everest_admin_theme[admin_bar][outer_background_settings][sub_menu][background_selection][type]' class="eat-selectbox-wrap eat-options-select-wrap">
								<option value ='default' <?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['type'])){ selected( $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['type'], 'default' ); } ?>><?php _e('Default', 'everest-admin-theme'); ?></option>
								<option value ='background-color' <?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['type'])){ selected( $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['type'], 'background-color' ); } ?>><?php _e('Background color', 'everest-admin-theme'); ?></option>
								<option value ='image' <?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['type'])){ selected( $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['type'], 'image' ); } ?>><?php _e('Image', 'everest-admin-theme'); ?></option>
							</select>
						</div>
					</div>

					<div class="eat-options-select-content-wrap">
						<div class="eat-common-content-wrap eat-background-color-content-wrap" style='display: <?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['type']) && $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['type'] =='background-color' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class="eat-options-wrap">
								<label for="eat-background-background-color"><?php _e('Background Color', 'everest-admin-theme' ); ?></label>
								<input id='eat-background-background-color' type="text" name='everest_admin_theme[admin_bar][outer_background_settings][sub_menu][background_selection][background-color][color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['background-color']['color']) && $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['background-color']['color'] != '' ){ echo $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['background-color']['color']; } ?>' />
							</div>
						</div>

						<div class="eat-common-content-wrap eat-image-content-wrap" style='display: <?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['type']) && $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['type'] =='image' ){ ?> block; <?php }else{ ?> none; <?php } ?>'>
							<div class="eat-image-selection-wrap">
								<div class="eat-options-wrap">
									<label for="eat-background-image-url"><?php _e( 'Image Upload: ', 'everest-admin-theme' ); ?></label>
									<div class="eat-input-field-wrap">
										<input type="text" id='eat-background-image-url' name='everest_admin_theme[admin_bar][outer_background_settings][sub_menu][background_selection][image][url]' class='eat-image-upload-url' value='<?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['image']['url']) && $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['image']['url'] != '' ){ echo $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['image']['url']; } ?>' />
										<input type="button" class='eat-button eat-image-upload-button' value='<?php _e('Upload Image', 'everest-admin-theme'); ?>' />
									</div>
								</div>
								<div class='eat-image-preview eat-image-placeholder'>
									<img src='<?php if(isset($plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['image']['url']) && $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['image']['url'] != '' ){ echo $plugin_settings['admin_bar']['outer_background_settings']['sub_menu']['background_selection']['image']['url']; } ?>' />
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="eat-admin-bar-menu-settings">
				<div class="eat-style-label"><?php _e('Menu Settings', 'everest-admin-theme'); ?></div>
				<div class="eat-options-wrap">
					<label for='eat-general-settings'><?php _e('Font color', 'everest-admin-theme'); ?></label>
					<div class="eat-input-field-wrap">
						<input id='ec-background-background-color' type="text" name='everest_admin_theme[admin_bar][menu][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_bar']['menu']['font-color']) && $plugin_settings['admin_bar']['menu']['font-color'] != '' ){ echo $plugin_settings['admin_bar']['menu']['font-color']; } ?>' />
					</div>
				</div>

				<div class="eat-options-wrap">
					<label for='eat-general-settings'><?php _e('Font Family', 'everest-admin-theme'); ?></label>
					<div class="eat-input-field-wrap">
						<select id='eat-google-fonts' name='everest_admin_theme[admin_bar][menu][google-fonts]' class="eat-selectbox-wrap">
							<option value=''><?php esc_html_e('Default', 'everest-admin-theme'); ?></option>
							<?php
							foreach($google_fonts as $key=>$value){
								// $key_value = str_replace(' ', '+', $value);
								?>
								<option value='<?php echo $value; ?>' <?php selected( $plugin_settings['admin_bar']['menu']['google-fonts'], $value); ?>><?php echo $value; ?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
			</div>

			<div class="eat-admin-bar-menu-hover-settings">
				<div class="eat-style-label"><?php esc_html_e('Menu hover settings', 'everest-admin-theme'); ?></div>
				<div class="eat-options-wrap">
					<label for='eat-admin-bar-submenu-hover-background-color'><?php _e('Hover background color', 'everest-admin-theme'); ?></label>
					<div class="eat-input-field-wrap">
						<input id='eat-admin-bar-submenu-hover-background-color' type="text" name='everest_admin_theme[admin_bar][menu][hover][background-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_bar']['menu']['hover']['background-color']) && $plugin_settings['admin_bar']['menu']['hover']['background-color'] != '' ){ echo $plugin_settings['admin_bar']['menu']['hover']['background-color']; } ?>' />
					</div>
				</div>

				<div class="eat-options-wrap">
					<label for='eat-admin-bar-submenu-hover-font-color'><?php _e('Hover Font color', 'everest-admin-theme'); ?></label>
					<input id='eat-admin-bar-submenu-hover-font-color' type="text" name='everest_admin_theme[admin_bar][menu][hover][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_bar']['menu']['hover']['font-color']) && $plugin_settings['admin_bar']['menu']['hover']['font-color'] != '' ){ echo $plugin_settings['admin_bar']['menu']['hover']['font-color']; } ?>' />
				</div>
			</div>
			
			<div class="eat-admin-bar-submenu-settings">
				<div class="eat-style-label"><?php esc_html_e('Submenu Settings', 'everest-admin-theme'); ?></div>
				<div class="eat-options-wrap">
					<label for='eat-general-settings'><?php _e('Font color', 'everest-admin-theme'); ?></label>
					<input id='ec-background-background-color' type="text" name='everest_admin_theme[admin_bar][sub_menu][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_bar']['sub_menu']['font-color']) && $plugin_settings['admin_bar']['sub_menu']['font-color'] != '' ){ echo $plugin_settings['admin_bar']['sub_menu']['font-color']; } ?>' />
				</div>

				<div class="eat-options-wrap">
					<label for='eat-general-settings'><?php _e('Font Family', 'everest-admin-theme'); ?></label>
					<div class="eat-item-input-field-wrap eat-input-field-wrap">
					<select id='eat-google-fonts' name='everest_admin_theme[admin_bar][sub_menu][google-fonts]' class="eat-selectbox-wrap">
						<option value=''><?php esc_html_e('Default', 'everest-admin-theme'); ?></option>
						<?php
						foreach($google_fonts as $key=>$value){
							?>
							<option value='<?php echo $value; ?>' <?php selected($plugin_settings['admin_bar']['sub_menu']['google-fonts'], $value); ?>><?php echo $value; ?></option>
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
					<label for='eat-general-settings'><?php _e('Hover background color', 'everest-admin-theme'); ?></label>
					<input id='ec-background-background-color' type="text" name='everest_admin_theme[admin_bar][sub_menu][hover][background-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_bar']['sub_menu']['hover']['background-color']) && $plugin_settings['admin_bar']['sub_menu']['hover']['background-color'] != '' ){ echo $plugin_settings['admin_bar']['sub_menu']['hover']['background-color']; } ?>' />
				</div>

				<div class="eat-options-wrap">
					<label for='eat-general-settings'><?php _e('Hover Font color', 'everest-admin-theme'); ?></label>
					<input id='ec-background-background-color' type="text" name='everest_admin_theme[admin_bar][sub_menu][hover][font-color]' class='eat-color-picker' data-alpha="true" value='<?php if(isset($plugin_settings['admin_bar']['sub_menu']['hover']['font-color']) && $plugin_settings['admin_bar']['sub_menu']['hover']['font-color'] != '' ){ echo $plugin_settings['admin_bar']['sub_menu']['hover']['font-color']; } ?>' />
				</div>
			</div>
		</div>

	</div>
</div>