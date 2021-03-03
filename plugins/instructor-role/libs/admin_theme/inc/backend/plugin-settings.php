<?php
global $eat_variables;
if(!get_option( 'everest-google-fonts-array' )){
	$object = $this->google_fonts_array();
	$google_fonts = array();
	foreach ($object as $key => $value) {
		array_push($google_fonts, $value->family);
	}
	update_option('everest-google-fonts-array', $google_fonts);
}else{
	$google_fonts = get_option('everest-google-fonts-array');
}
$plugin_settings = get_option('eat_admin_theme_settings');
// $this->print_array($plugin_settings);
?>
<form class="eat-plugin-settings-form" method="post" action="<?php echo admin_url() . 'themes.php?page=ir-admin-customizer' ?>">
    <input type="hidden" name="action" value="eat_settings_action" />
	<div class="eat-display-settings-wrap clearfix">
		<div class="eat-tabs-header">
			<div class="eat-tabs-header__inner">
				<ul class='eat-tabs-wrap'>
					<li class="eat-tab eat-template-management eat-active" id='eat-tab-template-management'><?php _e( 'Template', 'ir-admin-customizer' ); ?></li>
					<li class="eat-tab eat-general-management" id='eat-tab-general-management'><?php _e( 'General', 'ir-admin-customizer' ); ?></li>
					<li class="eat-tab eat-visibility-management" id='eat-tab-visibility-management'><?php _e('Visibility', 'ir-admin-customizer'); ?></li>
					<!-- <li class="eat-tab eat-dashboard-management" id='eat-tab-dashboard-management'><?php // _e('Dashboard', 'ir-admin-customizer'); ?></li> -->
					<!-- <li class="eat-tab eat-admin-menu-management" id='eat-tab-admin-menu-management'><?php // _e('Admin menu', 'ir-admin-customizer'); ?></li> -->
					<!-- <li class="eat-tab eat-footer-info-management" id='eat-tab-footer-info-management'><?php // _e('Footer info', 'ir-admin-customizer'); ?></li> -->
					<!-- <li class="eat-tab eat-custom-login-management" id='eat-tab-custom-login-management'><?php // _e('Custom login page', 'ir-admin-customizer'); ?></li> -->
					<li class="eat-tab eat-custom-css-management" id='eat-tab-custom-css-management'><?php _e('Custom CSS', 'ir-admin-customizer'); ?></li>
					<!-- <li class="eat-tab eat-posts-and-pages-management" id='eat-tab-posts-and-pages-management'><?php // _e('Import/Export', 'ir-admin-customizer'); ?></li> -->
				</ul>
				<?php
			    /**
			     * Nonce field
			     * */
			    wp_nonce_field('eat_settings_action', 'eat_settings_nonce');
			    ?>
			    <div id="eat-plugin-settings-submit" class="eat-settings-submit">
			        <input type="submit" class="eat-button button-primary" value="<?php _e('Save Settings', 'ir-admin-customizer'); ?>" name="eat_settings_submit"/>
			        <input type="submit" class='eat-button button-primary' value="<?php _e('Reset Settings', 'ir-admin-customizer'); ?>" name='eat_reset_settings' onclick="return confirm('Are you sure you want to restore default settings?')" />
			    </div>
			</div>
		</div>
		<div class="eat-tabs-content-wrap">
			<?php if(isset($_GET['message']) && $_GET['message'] == '1'){ ?>
			<div class="notice notice-success is-dismissible">
				<p><strong><?php _e('Settings saved successfully.', 'ir-admin-customizer'); ?></strong></p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text"><?php _e('Dismiss this notice.', 'ir-admin-customizer'); ?></span>
				</button>
			</div>
			<?php }else if(isset($_GET['message']) && $_GET['message'] == '3'){?>
			<div class="notice notice-success is-dismissible">
				<p><strong><?php _e('Settings restored successfully.', 'ir-admin-customizer'); ?></strong></p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text"><?php _e('Dismiss this notice.', 'ir-admin-customizer'); ?></span>
				</button>
			</div>
			<?php }else if(isset($_GET['message']) && $_GET['message'] != '3' && $_GET['message'] != '1'){ ?>
			<div class="notice notice-warning is-dismissible">
			    <p><?php _e('No new settings saved.', 'ir-admin-customizer'); ?></p>
			</div>
			<?php } ?>
			<?php include('parts/template-management.php'); ?>
			<?php include('parts/general-management.php'); ?>
			<?php include('parts/visibility-management.php'); ?>
			<?php // include('parts/dashboard-management.php'); ?>
			<?php // include('parts/admin-menu-management.php'); ?>
			<?php // include('parts/footer-info-management.php'); ?>
			<?php // include('parts/custom-login-page-management.php'); ?>
			<?php // include('parts/import_export.php'); ?>
			<?php include('parts/custom-css-management.php'); ?>
		</div>
	</div>
</form>