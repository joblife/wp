<div class="eat-tab-content eat-tab-custom-css-management" style="display: none;">
	<div class="eat-tab-content-header">
		<div class="eat-tab-content-header-title"><?php _e('Custom CSS', 'everest-admin-theme'); ?></div>
	</div>
	<div class="eat-tab-custom-css-content-body">
		<div class="eat-textarea-code">
		<textarea class='eat-textarea-code-texts' name="everest_admin_theme[custom_css]"><?php if(isset($plugin_settings['custom_css']) && $plugin_settings['custom_css'] !='' ){ echo $plugin_settings['custom_css']; } ?></textarea>
		</div>
	</div>
</div>