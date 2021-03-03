<?php
/**
 * Default Instructor Menu Template
 *
 * @since 3.1.0
 */
?>
<div id="ir-primary-navigation" class="menu-test-container ir-default-menu">
	<ul id="ir-primary-menu" class="menu">
		<li class="wdm-mob-menu wdm-admin-menu-show wdm-hidden"><span class="dashicons dashicons-menu-alt"></span></li>
		<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="<?php echo get_bloginfo( 'url' ); ?>"><?php echo get_bloginfo( 'name' ); ?></a></li>
		<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="<?php echo wp_logout_url(); ?>"><?php _e( 'Logout', 'instructor-role' ); ?></a></li>
	</ul>
</div>
