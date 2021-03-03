<div class="eat-tab-content eat-tab-dashboard-management" style="display: none;">
	<div class="eat-tab-content-header">
		<div class="eat-tab-content-header-title"><?php _e('Dashboard Management', 'everest-admin-theme'); ?></div>
	</div>
	<div class="eat-tab-content-body">
		<div class="eat-hide-show-wrap">
			<div class="eat-style-label"><?php _e('Hide/Show options', 'everest-admin-theme'); ?></div>
			<div class="eat-options-wrap">
				<label for='eat-dashboard-hide-welcome-panel-widget'><?php _e("Hide 'Welcome Panel' widget", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
				<input type="checkbox" id='eat-dashboard-hide-welcome-panel-widget' name='everest_admin_theme[dashboard][hide_welcome_panel]' class='eat-dashboard-hide-welcome-panel-widget' <?php if(isset($plugin_settings['dashboard']['hide_welcome_panel'])){ ?> checked <?php } ?> />
				<label for='eat-dashboard-hide-welcome-panel-widget'></label>
				</div>
			</div>

			<div class="eat-options-wrap">
				<label for='eat-dashboard-hide-wordpress-news-events-widget'><?php _e("Hide 'Wordpress events and news' widget", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
					<input type="checkbox" id='eat-dashboard-hide-wordpress-news-events-widget' name='everest_admin_theme[dashboard][hide_wordpress_events_news]' class='eat-dashboard-hide-wordpress-news-events-widget' <?php if(isset($plugin_settings['dashboard']['hide_wordpress_events_news'])){ ?> checked <?php } ?> />
					<label for='eat-dashboard-hide-wordpress-news-events-widget'></label>
				</div>
			</div>

			<div class="eat-options-wrap">
				<label for='eat-dashboard-hide-quick-draft'><?php _e("Hide 'Quick draft' widget", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
					<input type="checkbox" id='eat-dashboard-hide-quick-draft' name='everest_admin_theme[dashboard][hide_quick_draft]' class='eat-dashboard-hide-quick-draft' <?php if(isset($plugin_settings['dashboard']['hide_quick_draft'])){ ?> checked <?php } ?> />
					<label for='eat-dashboard-hide-quick-draft'></label>
				</div>
			</div>

			<div class="eat-options-wrap">
				<label for='eat-dashboard-hide-at-a-glance-widget'><?php _e("Hide 'At a glance' widgets", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
				<input type="checkbox" id='eat-dashboard-hide-at-a-glance-widget' name='everest_admin_theme[dashboard][hide_at_a_glance]' class='eat-dashboard-hide-at-a-glance-widget' <?php if(isset($plugin_settings['dashboard']['hide_at_a_glance'])){ ?> checked <?php } ?> />
				<label for='eat-dashboard-hide-at-a-glance-widget'></label>
				</div>
			</div>

			<div class="eat-options-wrap">
				<label for='eat-dashboard-hide-activity-widget'><?php _e("Hide 'Activity' Widget", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
					<input type="checkbox" id='eat-dashboard-hide-activity-widget' name='everest_admin_theme[dashboard][hide_activity]' class='eat-dashboard-hide-activity-widget' <?php if(isset($plugin_settings['dashboard']['hide_activity'])){ ?> checked <?php } ?> />
					<label for='eat-dashboard-hide-activity-widget'></label>
				</div>
			</div>

			<div class="eat-options-wrap">
				<label for='eat-dashboard-hide-recent-draft-widget'><?php _e("Hide 'Recent Drafts' Widget", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
					<input type="checkbox" id='eat-dashboard-hide-recent-draft-widget' name='everest_admin_theme[dashboard][hide_recent_draft]' class='eat-dashboard-hide-recent-draft-widget' <?php if(isset($plugin_settings['dashboard']['hide_recent_draft'])){ ?> checked <?php } ?> />
					<label for='eat-dashboard-hide-recent-draft-widget'></label>
				</div>
			</div>

			<div class="eat-options-wrap">
				<label for='eat-dashboard-hide-recent-comments-widget'><?php _e("Hide 'Recent Comments' Widget", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
					<input type="checkbox" id='eat-dashboard-hide-recent-comments-widget' name='everest_admin_theme[dashboard][hide_recent_comments]' class='eat-dashboard-hide-recent-comments-widget' <?php if(isset($plugin_settings['dashboard']['hide_recent_comments'])){ ?> checked <?php } ?> />
					<label for='eat-dashboard-hide-recent-comments-widget'></label>
				</div>
			</div>

			<div class="eat-options-wrap">
				<label for='eat-dashboard-hide-incoming-links-widget'><?php _e("Hide 'Incoming Links' Widget", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
					<input type="checkbox" id='eat-dashboard-hide-incoming-links-widget' name='everest_admin_theme[dashboard][hide_incoming_links]' class='eat-dashboard-hide-incoming-links-widget' <?php if(isset($plugin_settings['dashboard']['hide_incoming_links'])){ ?> checked <?php } ?> />
					<label for='eat-dashboard-hide-incoming-links-widget'></label>
				</div>
			</div>

			<div class="eat-options-wrap">
				<label for='eat-dashboard-hide-plugins-widget'><?php _e("Hide 'Plugins' Widget", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
					<input type="checkbox" id='eat-dashboard-hide-plugins-widget' name='everest_admin_theme[dashboard][hide_plugins]' class='eat-dashboard-hide-plugins-widget' <?php if(isset($plugin_settings['dashboard']['hide_plugins'])){ ?> checked <?php } ?> />
					<label for='eat-dashboard-hide-plugins-widget'></label>
				</div>
			</div>

			<div class="eat-options-wrap">
				<label for='eat-dashboard-hide-wordpress-blog-widget'><?php _e("Hide 'WordPress blog' Widget", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
					<input type="checkbox" id='eat-dashboard-hide-wordpress-blog-widget' name='everest_admin_theme[dashboard][hide_wordpress_blog]' class='eat-dashboard-hide-wordpress-blog-widget' <?php if(isset($plugin_settings['dashboard']['hide_wordpress_blog'])){ ?> checked <?php } ?> />
					<label for='eat-dashboard-hide-wordpress-blog-widget'></label>
				</div>
			</div>


		</div>
		<div class="eat-dashboard-custom-widget-wrap eat-custom-texts-options-wrap">

			<div class="eat-style-label"><?php esc_html_e( 'Add Custom Admin widget', 'everest-admin-theme' ); ?></div>
			<div class="eat-options-wrap">
				<label for='eat-dashboard-enable-custom-widget'><?php _e("Enable widget?", 'everest-admin-theme'); ?></label>
				<div class="eat-input-field-wrap">
					<input type="checkbox" id='eat-dashboard-enable-custom-widget' name='everest_admin_theme[dashboard][custom-widget][enable]' class='eat-dashboard-enable-custom-widget eat-footer-info-custom-texts' <?php if(isset($plugin_settings['dashboard']['custom-widget']['enable'])){ ?> checked <?php } ?> />
					<label for='eat-dashboard-enable-custom-widget'></label>
				</div>
			</div>
			<div class="eat-custom-texts-content-wrap" <?php if(isset($plugin_settings['dashboard']['custom-widget']['enable'])){ echo "style='display:block;'"; }else{ echo "style='display:none;'"; } ?>>
				<div class="eat-dashboard-widgets-wrapper">
					<?php
					for($i=1; $i<=5 ;$i++){ ?>
					<div class='eat-widget-item-<?php echo $i; ?>'>
						<div class="eat-substyle-label"><?php echo _e("Widget", 'everest-admin-theme' ); ?><?php echo ' '.$i; ?></div>
						<div class="eat-options-wrap">
							<label for='eat-dashboard-custom-widget-title'><?php esc_html_e('Widget Title', 'everest-admin-theme'); ?></label>
							<div class="eat-input-field-wrap">
								<input id='eat-dashboard-custom-widget-title' name='everest_admin_theme[dashboard][custom-widget][<?php echo $i; ?>][widget_title]' value="<?php if(isset($plugin_settings['dashboard']['custom-widget'][$i]['widget_title']) && $plugin_settings['dashboard']['custom-widget'][$i]['widget_title'] !=''){ echo esc_html_e($plugin_settings['dashboard']['custom-widget'][$i]['widget_title']); } ?>"/>
							</div>
						</div>
						<div class="eat-options-wrap">
							<label for='eat-dashboard-custom-widget-content'><?php esc_html_e('Widget content', 'everest-admin-theme'); ?></label>
							<div class="eat-input-field-wrap eat-wordpress-default-text-editor">
								<?php
								$settings = array(
												'media_buttons' => false,
												'textarea_name' => "everest_admin_theme[dashboard][custom-widget][$i][widget_content]");
								$editor_id = "eat-dashboard-custom-widget-content-$i";
								if(isset($plugin_settings['dashboard']['custom-widget'][$i]['widget_content']) && $plugin_settings['dashboard']['custom-widget'][$i]['widget_content'] !=''){
									$content = $plugin_settings['dashboard']['custom-widget'][$i]['widget_content'];
								}else{
									$content = "";
								}
								wp_editor( $content, $editor_id, $settings );
								?>
							</div>
						</div>
					</div>
					<?php
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>