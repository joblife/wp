<div class="eat-tab-content eat-tab-posts-and-pages-management" style="display: none;">
	<div class="eat-tab-content-header">
		<div class="eat-tab-content-header-title"><?php _e('Posts and Pages Management', 'everest-admin-theme'); ?></div>
	</div>
	<div class="eat-tab-content-body">
		<div class="eat-options-wrap">
			<label for="eat-posts-pages-excerpt-box"><?php _e("Remove 'Excerpt' box?", "everest-admin-theme"); ?></label>
			<input type="checkbox" id='eat-posts-pages-excerpt-box' name='everest_admin_theme[posts_pages][excerpt-box]' class='eat-posts-pages-excerpt-box' <?php if(isset($plugin_settings['posts_pages']['excerpt-box'])){ ?> checked <?php } ?> />
			<label for='eat-posts-pages-excerpt-box'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-posts-pages-category-box"><?php _e("Remove 'Category' box?", "everest-admin-theme"); ?></label>
			<input type="checkbox" id='eat-posts-pages-category-box' name='everest_admin_theme[posts_pages][category-box]' class='eat-posts-pages-category-box' <?php if(isset($plugin_settings['posts_pages']['category-box'])){ ?> checked <?php } ?> />
			<label for='eat-posts-pages-category-box'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-posts-pages-format-box"><?php _e("Remove 'Format' box?", "everest-admin-theme"); ?></label>
			<input type="checkbox" id='eat-posts-pages-format-box' name='everest_admin_theme[posts_pages][format-box]' class='eat-posts-pages-format-box' <?php if(isset($plugin_settings['posts_pages']['format-box']['hide'])){ ?> checked <?php } ?> />
			<label for='eat-posts-pages-format-box'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-posts-pages-trackback-box"><?php _e("Remove 'Trackbacks' box?", "everest-admin-theme"); ?></label>
			<input type="checkbox" id='eat-posts-pages-trackback-box' name='everest_admin_theme[posts_pages][trackback-box]' class='eat-posts-pages-trackback-box' <?php if(isset($plugin_settings['posts_pages']['trackback-box'])){ ?> checked <?php } ?> />
			<label for='eat-posts-pages-trackback-box'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-posts-pages-remove-comments-status-box"><?php _e("Remove 'Comments status' box?", "everest-admin-theme"); ?></label>
			<input type="checkbox" id='eat-posts-pages-remove-comments-status-box' name='everest_admin_theme[posts_pages][comment-status-box]' class='eat-hide-footer-left-texts' <?php if(isset($plugin_settings['posts_pages']['comment-status-box'])){ ?> checked <?php } ?> />
			<label for='eat-posts-pages-remove-comments-status-box'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-posts-pages-comments-list"><?php _e("Remove 'Comments list' box?", "everest-admin-theme"); ?></label>
			<input type="checkbox" id='eat-posts-pages-comments-list' name='everest_admin_theme[posts_pages][comments-list-box]' class='eat-posts-pages-comments-list' <?php if(isset($plugin_settings['posts_pages']['comments-list-box'])){ ?> checked <?php } ?> />
			<label for='eat-posts-pages-comments-list'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-posts-pages-custom-fields"><?php _e("Remove 'Custom Fields' box?", "everest-admin-theme"); ?></label>
			<input type="checkbox" id='eat-posts-pages-custom-fields' name='everest_admin_theme[posts_pages][custom-fields-box]' class='eat-posts-pages-custom-fields' <?php if(isset($plugin_settings['posts_pages']['custom-fields-box'])){ ?> checked <?php } ?> />
			<label for='eat-posts-pages-custom-fields'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-posts-pages-revisions-box"><?php _e("Remove 'Revisions' box?", "everest-admin-theme"); ?></label>
			<input type="checkbox" id='eat-posts-pages-revisions-box' name='everest_admin_theme[posts_pages][revisions-box]' class='eat-posts-pages-revisions-box' <?php if(isset($plugin_settings['posts_pages']['revisions-box'])){ ?> checked <?php } ?> />
			<label for='eat-posts-pages-revisions-box'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-posts-pages-author-box"><?php _e("Remove 'Author' box?", "everest-admin-theme"); ?></label>
			<input type="checkbox" id='eat-posts-pages-author-box' name='everest_admin_theme[posts_pages][author-box]' class='eat-posts-pages-author-box' <?php if(isset($plugin_settings['posts_pages']['author-box'])){ ?> checked <?php } ?> />
			<label for='eat-posts-pages-author-box'></label>
		</div>

		<div class="eat-options-wrap">
			<label for="eat-posts-pages-slug-box"><?php _e("Remove 'Slug' box?", "everest-admin-theme"); ?></label>
			<input type="checkbox" id='eat-posts-pages-slug-box' name='everest_admin_theme[posts_pages][slug-box]' class='eat-posts-pages-slug-box' <?php if(isset($plugin_settings['posts_pages']['slug-box'])){ ?> checked <?php } ?> />
			<label for='eat-posts-pages-slug-box'></label>
		</div>
	</div>
</div>