<div class="eat-tab-content eat-tab-visibility-management" style="display: none;">
	<div class="eat-tab-content-header">
		<div class="eat-tab-content-header-title"><?php _e('Visibility Management', 'everest-admin-theme'); ?></div>
	</div>
	<div class="eat-tab-content-body">
		<div class="eat-options-wrap">
			<label for='eat-visibility-enable-for-admin'><?php _e('Enable theme for Admin', 'everest-admin-theme'); ?></label>
			<div class="eat-input-field-wrap">
				<div class="eat-input-field-wrap">
					<input type="checkbox" name="everest_admin_theme[visibility][enable_for_admin]" <?php if(isset($plugin_settings['visibility']['enable_for_admin'])){ ?> checked <?php } ?> class="eat-visibility-enable-option ec-checkbox-enable-option" id="eat-visibility-enable-for-admin" value="1">
				<label for="eat-visibility-enable-for-admin"></label>
				</div>
            </div>
        </div>
        <div class="eat-options-wrap">
			<label for='eat-visibility-enable-for-instructor'><?php _e('Enable theme for Instructor', 'everest-admin-theme'); ?></label>
			<div class="eat-input-field-wrap">
				<div class="eat-input-field-wrap">
					<input type="checkbox" name="everest_admin_theme[visibility][enable_for_instructor]" <?php if(isset($plugin_settings['visibility']['enable_for_instructor'])){ ?> checked <?php } ?> class="eat-visibility-enable-option ec-checkbox-enable-option" id="eat-visibility-enable-for-instructor" value="1">
				<label for="eat-visibility-enable-for-instructor"></label>
				</div>
            </div>
        </div>
        <div class="eat-options-wrap">
			<label for='eat-visibility-enable-for-all'><?php _e('Enable theme for all Users', 'everest-admin-theme'); ?></label>
			<div class="eat-input-field-wrap">
				<div class="eat-input-field-wrap">
					<input type="checkbox" name="everest_admin_theme[visibility][enable_for_all]" <?php if(isset($plugin_settings['visibility']['enable_for_all'])){ ?> checked <?php } ?> class="eat-visibility-enable-option ec-checkbox-enable-option" id="eat-visibility-enable-for-all" value="1">
				<label for="eat-visibility-enable-for-all"></label>
				</div>
            </div>
        </div>
	</div>
</div>