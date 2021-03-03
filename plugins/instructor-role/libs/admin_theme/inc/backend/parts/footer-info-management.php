<div class="eat-tab-content eat-tab-footer-info-management" style="display: none;">
	<div class="eat-tab-content-header">
		<div class="eat-tab-content-header-title"><?php esc_html_e('Footer Info Management', 'everest-admin-theme'); ?></div>
	</div>
	<div class="eat-tab-content-body">
		<div class="eat-options-wrap">
			<label for="eat-footer-info-hide-footer-completely"><?php _e( 'Hide footer Completely?', 'everest-admin-theme' ); ?></label>
			<input type="checkbox" id='eat-footer-info-hide-footer-completely' name='everest_admin_theme[footer_info][hide-all]' class='eat-hide-footer-completely' <?php if(isset($plugin_settings['footer_info']['hide-all'])){ ?> checked <?php } ?> />
			<label for='eat-footer-info-hide-footer-completely'></label>
		</div>

		<div class="eat-default-footer-options-wrap">
			<div class="eat-deafult-footer-options-left">
				<div class="eat-style-label"><?php _e('Left footer settings?', 'everest-admin-theme'); ?></div>
				<div class="eat-options-wrap">
					<label for="eat-footer-info-left-hide"><?php _e( 'Hide left footer completely?', 'everest-admin-theme' ); ?></label>
					<input type="checkbox" id='eat-footer-info-left-hide' name='everest_admin_theme[footer_info][left][hide]' class='eat-footer-info-left-hide' <?php if(isset($plugin_settings['footer_info']['left']['hide'])){ ?> checked <?php } ?> />
					<label for='eat-footer-info-left-hide'></label>
				</div>

				<div class="eat-options-wrap">
					<label for="eat-footer-info-left-hide-default"><?php _e( 'Hide left footer default texts?', 'everest-admin-theme' ); ?></label>
					<input type="checkbox" id='eat-footer-info-left-hide-default' name='everest_admin_theme[footer_info][left][hide-default]' class='eat-footer-info-left-hide-default' <?php if(isset($plugin_settings['footer_info']['left']['hide-default'])){ ?> checked <?php } ?> />
					<label for='eat-footer-info-left-hide-default'></label>
				</div>

				<div class="eat-custom-texts-options-wrap">
					<div class="eat-options-wrap">
						<label for="eat-footer-info-left-custom-texts-enable"><?php _e( 'Use custom texts?', 'everest-admin-theme' ); ?></label>
						<input type="checkbox" id='eat-footer-info-left-custom-texts-enable' name='everest_admin_theme[footer_info][left][custom_texts][enable]' class='eat-footer-info-left-custom-texts eat-footer-info-custom-texts' <?php if(isset($plugin_settings['footer_info']['left']['custom_texts']['enable'])){ ?> checked <?php } ?> />
						<label for='eat-footer-info-left-custom-texts-enable'></label>
					</div>

					<div class="eat-options-wrap eat-custom-texts-content-wrap" <?php if(isset($plugin_settings['footer_info']['left']['custom_texts']['enable'])){ echo "style='display:block;'"; }else{ echo "style='display:none;'"; } ?>>
						<label for="eat-footer-info-left-custom-texts-content"><?php _e( 'Custom texts', 'everest-admin-theme' ); ?></label>
						<?php
						$settings = array(
							'media_buttons' => false,
							'textarea_name' => 'everest_admin_theme[footer_info][left][custom_texts][content]' );
						$editor_id = "eat-footer-info-left-custom-texts-content";
						if(isset($plugin_settings['footer_info']['left']['custom_texts']['content']) && $plugin_settings['footer_info']['left']['custom_texts']['content'] !=''){
							$content = $plugin_settings['footer_info']['left']['custom_texts']['content'];
						}else{
							$content = "";
						}
						wp_editor( $content, $editor_id, $settings );
						?>
					</div>
				</div>
			</div>

			<div class="eat-deafult-footer-options-right">
				<div class="eat-style-label"><?php _e( 'Right Footer settings?', 'everest-admin-theme' ); ?></div>
				<div class="eat-options-wrap">
					<label for="eat-footer-info-right-hide"><?php _e( 'Hide right footer completely?', 'everest-admin-theme' ); ?></label>
					<input type="checkbox" id='eat-footer-info-right-hide' name='everest_admin_theme[footer_info][right][hide]' class='eat-footer-info-right-hide' <?php if(isset($plugin_settings['footer_info']['right']['hide'])){ ?> checked <?php } ?> />
					<label for='eat-footer-info-right-hide'></label>
				</div>

				<div class="eat-options-wrap">
					<label for="eat-footer-info-right-hide-default"><?php _e( 'Hide right footer default texts?', 'everest-admin-theme' ); ?></label>
					<input type="checkbox" id='eat-footer-info-right-hide-default' name='everest_admin_theme[footer_info][right][hide-default]' class='eat-footer-info-right-hide-default' <?php if(isset($plugin_settings['footer_info']['right']['hide-default'])){ ?> checked <?php } ?> />
					<label for='eat-footer-info-right-hide-default'></label>
				</div>

				<div class="eat-custom-texts-options-wrap">
					<div class="eat-options-wrap">
						<label for="eat-footer-info-right-custom-texts-enable"><?php _e( 'Use custom texts?', 'everest-admin-theme' ); ?></label>
						<input type="checkbox" id='eat-footer-info-right-custom-texts-enable' name='everest_admin_theme[footer_info][right][custom_texts][enable]' class='eat-footer-info-right-custom-texts eat-footer-info-custom-texts' <?php if(isset($plugin_settings['footer_info']['right']['custom_texts']['enable'])){ ?> checked <?php } ?> />
						<label for='eat-footer-info-right-custom-texts-enable'></label>
					</div>

					<div class="eat-options-wrap eat-custom-texts-content-wrap" <?php if(isset($plugin_settings['footer_info']['right']['custom_texts']['enable'])){ echo "style='display:block;'"; }else{ echo "style='display:none;'"; } ?>>
						<label for="eat-footer-info-right-custom-texts-content"><?php _e( 'Custom texts', 'everest-admin-theme' ); ?></label>
						<?php
						$settings = array(
							'media_buttons' => false,
							'textarea_name' => 'everest_admin_theme[footer_info][right][custom_texts][content]' );
						$editor_id = "eat-footer-info-right-custom-texts-content";
						if(isset($plugin_settings['footer_info']['right']['custom_texts']['content']) && $plugin_settings['footer_info']['right']['custom_texts']['content'] !=''){
							$content = $plugin_settings['footer_info']['right']['custom_texts']['content'];
						}else{
							$content = "";
						}
						wp_editor( $content, $editor_id, $settings );
						?>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>
