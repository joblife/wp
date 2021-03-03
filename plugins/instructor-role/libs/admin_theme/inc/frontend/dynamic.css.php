<?php
	$eat_dynamic_css_at_end = array();
	$google_fonts_used_array = array();

	$plugin_settings = $this->plugin_settings;

	$template = $plugin_settings['general-settings']['template'];

	// $general_settings = $plugin_settings['genral-settings'][];

	$admin_bar_settings = $plugin_settings['admin_bar'];
	$admin_bar_menu_settings = $admin_bar_settings['menu'];
	$admin_bar_menu_background_options = isset($admin_bar_settings['outer_background_settings']['menu']['background_selection']) ? $admin_bar_settings['outer_background_settings']['menu']['background_selection'] : array();
	$admin_bar_sub_menu_background_options = isset($admin_bar_settings['outer_background_settings']['sub_menu']['background_selection']) ? $admin_bar_settings['outer_background_settings']['sub_menu']['background_selection'] : array();
	?>
	<style>
		<?php

		// admin bar background settings

		$dynamic_css =array();

		if($admin_bar_menu_background_options['type'] == 'background-color'){
			$bg_color = $admin_bar_menu_background_options['background-color']['color'];
			$dynamic_css[] = "background:$bg_color;";
		}

		if($admin_bar_menu_background_options['type'] == 'image' && $admin_bar_menu_background_options['image']['url'] != '' ){
			$bg_image = $admin_bar_menu_background_options['image']['url'];
			$dynamic_css[] = "background-image:url('$bg_image'); background-size: cover;";
		}

		if(!empty($dynamic_css)){
			$dynamic_css = implode(' ', $dynamic_css);
		}else{
			$dynamic_css ='';
		}
		?>

		/* admin bar menu settings background color/image */
		<?php if($template == 'temp-19' || $template == 'temp-15'){ ?>
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar.fixedheader { <?php echo $dynamic_css; ?> }	
		<?php }else{ ?>
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar { <?php echo $dynamic_css; ?> }
		<?php } ?>
		
		<?php
		//admin bar submenu background settings
		$dynamic_css1 =array();

		if($admin_bar_sub_menu_background_options['type'] == 'background-color'){
			$bg_color = $admin_bar_sub_menu_background_options['background-color']['color'];
			$dynamic_css1[] = "background:$bg_color;";
		}

		if($admin_bar_sub_menu_background_options['type'] == 'image'){
			$bg_image = $admin_bar_sub_menu_background_options['image']['url'];
			$dynamic_css1[] = "background-image:url('$bg_image'); background-size: cover;";
		}

		if(!empty($dynamic_css1)){
			$dynamic_css1 = implode(' ', $dynamic_css1);
		}else{
			$dynamic_css1 ='';
		}
		?>
		/* admin bar sub menu settings background color/image */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .menupop .ab-sub-wrapper,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .menupop .ab-sub-wrapper,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop ul.ab-sub-secondary { <?php echo $dynamic_css1; ?> }

		<?php
		// admin bar menu settings
		$admin_bar_menu_dynamic_css =array();
		if(isset($admin_bar_menu_settings['font-color']) && $admin_bar_menu_settings['font-color'] !=''){
			$admin_bar_menu_dynamic_css[] = "color:{$admin_bar_menu_settings['font-color']};";
		}

		if(isset($admin_bar_menu_settings['font-size']) && $admin_bar_menu_settings['font-size'] !=''){
			$admin_bar_menu_dynamic_css[] = "font-size:{$admin_bar_menu_settings['font-size']}px;";
		}

		if(isset($admin_bar_menu_settings['google-fonts']) && $admin_bar_menu_settings['google-fonts'] !=''){
			if(!in_array( $admin_bar_menu_settings['google-fonts'], $google_fonts_used_array) ){
				array_push($google_fonts_used_array, preg_replace('/\s/', '+', $admin_bar_menu_settings['google-fonts']) );
			}
			$admin_bar_menu_dynamic_css[] = "font-family: {$admin_bar_menu_settings['google-fonts']}, Dashicons;";
		}

		if(!empty($admin_bar_menu_dynamic_css)){
			$admin_bar_menu_dynamic_css = implode(' ', $admin_bar_menu_dynamic_css);
		}else{
			$admin_bar_menu_dynamic_css ='';
		}

		// admin bar menu hover settings
		$admin_bar_hover_settings = $admin_bar_menu_settings['hover'];
		$admin_bar_hover_settings['background-color'];
		$admin_bar_hover_settings['font-color'];
		$admin_bar_menu_hover_dynamic_css = array();

		if(isset($admin_bar_hover_settings['background-color']) && $admin_bar_hover_settings['background-color'] !=''){
			$admin_bar_menu_hover_dynamic_css[] = "background-color:{$admin_bar_hover_settings['background-color']};";
		}

		if(isset($admin_bar_hover_settings['font-color']) && $admin_bar_hover_settings['font-color'] !=''){
			$admin_bar_menu_hover_dynamic_css[] = "color:{$admin_bar_hover_settings['font-color']};";
		}

		if(!empty($admin_bar_menu_hover_dynamic_css)){
			$admin_bar_menu_hover_dynamic_css = implode(' ', $admin_bar_menu_hover_dynamic_css);
		}else{
			$admin_bar_menu_hover_dynamic_css ='';
		}


		// for submenu
		$admin_bar_submenu_settings = $admin_bar_settings['sub_menu'];

		$admin_bar_menu_background_dynamic_css = array();
		if($admin_bar_submenu_settings['background_selection']['type'] == 'background-color'){
			$bg_color = $admin_bar_submenu_settings['background_selection']['background-color']['color'];
			$admin_bar_menu_background_dynamic_css[] = "background-color:$bg_color;";
		}

		if($admin_bar_submenu_settings['background_selection']['type'] == 'image'){
			$bg_image = $admin_bar_submenu_settings['background_selection']['image']['url'];
			$admin_bar_menu_background_dynamic_css[] = "background-image:url('$bg_image');";
		}

		if(!empty($admin_bar_menu_background_dynamic_css)){
			$admin_bar_menu_background_dynamic_css = implode(' ', $admin_bar_menu_background_dynamic_css);
		}else{
			$admin_bar_menu_background_dynamic_css ='';
		}

		// for submenu text settings
		$admin_bar_submenu_dynamic_css=array();
		if(isset($admin_bar_submenu_settings['font-color']) && $admin_bar_submenu_settings['font-color'] !=''){
			$admin_bar_submenu_dynamic_css[] = "color:{$admin_bar_submenu_settings['font-color']};";
		}

		if(isset($admin_bar_submenu_settings['font-size']) && $admin_bar_submenu_settings['font-size'] !=''){
			$admin_bar_submenu_dynamic_css[] = "font-size:{$admin_bar_submenu_settings['font-size']}px;";
		}

		if(isset($admin_bar_submenu_settings['google-fonts']) && $admin_bar_submenu_settings['google-fonts'] !=''){
			if(!in_array( $admin_bar_submenu_settings['google-fonts'], $google_fonts_used_array) ){
				array_push($google_fonts_used_array, preg_replace('/\s/', '+', $admin_bar_submenu_settings['google-fonts']) );
			}
			$admin_bar_submenu_dynamic_css[] = "font-family: {$admin_bar_submenu_settings['google-fonts']}, Dashicons;";
		}
		
		if(!empty($admin_bar_submenu_dynamic_css)){
			$admin_bar_submenu_dynamic_css = implode(' ', $admin_bar_submenu_dynamic_css);
		}else{
			$admin_bar_submenu_dynamic_css ='';
		}

		// for submenu hover text settings.
		$admin_bar_submenu_hover_dynamic_css=array();
		$admin_bar_submenu_settings['hover']['background-color'];
		$admin_bar_submenu_settings['hover']['font-color'];
		if(isset($admin_bar_submenu_settings['hover']['font-color']) && $admin_bar_submenu_settings['hover']['font-color'] !=''){
			$admin_bar_submenu_hover_dynamic_css[] = "color:{$admin_bar_submenu_settings['hover']['font-color']};";
		}

		if(isset($admin_bar_submenu_settings['hover']['background-color']) && $admin_bar_submenu_settings['hover']['background-color'] !=''){
			$admin_bar_submenu_hover_dynamic_css[] = "background:{$admin_bar_submenu_settings['hover']['background-color']};";
		}

		if(!empty($admin_bar_submenu_hover_dynamic_css)){
			$admin_bar_submenu_hover_dynamic_css = implode(' ', $admin_bar_submenu_hover_dynamic_css);
		}else{
			$admin_bar_submenu_hover_dynamic_css ='';
		}

		// Admin menu outer background settings
		$admin_menu_settings = $plugin_settings['admin_menu'];
		$admin_menu_background_settings = $admin_menu_settings['outer_background_settings']['menu'];
		
		$admin_menu_background_settings_dynamic_css = array();
		if($admin_menu_background_settings['type'] == 'image'){
			$admin_menu_background_settings_dynamic_css [] = "background-image: url({$admin_menu_background_settings['image']['url']});";
		}

		if($admin_menu_background_settings['type'] == 'background-color'){
			$admin_menu_background_settings_dynamic_css [] = "background: {$admin_menu_background_settings['background-color']['color']};";
		}
		
		if(!empty($admin_menu_background_settings_dynamic_css)){
			$admin_menu_background_settings_dynamic_css = implode(' ', $admin_menu_background_settings_dynamic_css);
		}else{
			$admin_menu_background_settings_dynamic_css ='';
		}

		// admin submenu outer background settings 
		$admin_sub_menu_background_settings = $admin_menu_settings['outer_background_settings']['sub_menu'];

		$admin_sub_menu_background_settings_dynamic_css = array();
		if($admin_sub_menu_background_settings['type'] == 'image'){
			$admin_sub_menu_background_settings_dynamic_css [] = "background-image: url({$admin_sub_menu_background_settings['image']['url']}); background-size: cover;";
		}

		if($admin_sub_menu_background_settings['type'] == 'background-color'){
			$admin_sub_menu_background_settings_dynamic_css [] = "background: {$admin_sub_menu_background_settings['background-color']['color']};";
		}
		
		if(!empty($admin_sub_menu_background_settings_dynamic_css)){
			$admin_sub_menu_background_settings_dynamic_css = implode(' ', $admin_sub_menu_background_settings_dynamic_css);
		}else{
			$admin_sub_menu_background_settings_dynamic_css ='';
		}
		?>

		
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenuback { <?php echo $admin_menu_background_settings_dynamic_css; ?> }
		
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu .wp-has-current-submenu .wp-submenu,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu .opensub .wp-submenu,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu .wp-submenu {
			<?php echo $admin_sub_menu_background_settings_dynamic_css; ?>  }
		<?php
		//admin menu settings
		$menu_settings = $admin_menu_settings['menu'];

		$menu_dynamic_css=array();
		$menu_icon_dynamic_css = array();


		if($menu_settings['background_selection']['type'] == 'image' && $menu_settings['background_selection']['type'] != '') {
			$menu_dynamic_css[] = "background-image: url({$menu_settings['background_selection']['image']['url']}); background-size: cover;";
		}

		if($menu_settings['background_selection']['type'] == 'background-color') {
			$menu_dynamic_css[] = "background-color:{$menu_settings['background_selection']['background-color']['color']};";
		}
		if(isset($menu_settings['font-color']) && $menu_settings['font-color'] !=''){
			$menu_dynamic_css[] = "color:{$menu_settings['font-color']};";
			$menu_icon_dynamic_css[] = "color:{$menu_settings['font-color']};";
		}

		if(isset($menu_settings['font-size']) && $menu_settings['font-size'] !=''){
			$menu_dynamic_css[] = "font-size:{$menu_settings['font-size']}px;";
			$menu_icon_dynamic_css[] = "font-size:{$menu_settings['font-size']}px;";
		}

		if(isset($menu_settings['google-fonts']) && $menu_settings['google-fonts'] !=''){
			if(!in_array( $menu_settings['google-fonts'], $google_fonts_used_array) ){
				array_push($google_fonts_used_array, preg_replace('/\s/', '+', $menu_settings['google-fonts']) );
			}
			$menu_dynamic_css[] = "font-family: {$menu_settings['google-fonts']}, Dashicons;";
		}

		if(!empty($menu_dynamic_css)){
			$menu_dynamic_css = implode(' ', $menu_dynamic_css);
		}else{
			$menu_dynamic_css ='';
		}

		if(!empty($menu_icon_dynamic_css)){
			$menu_icon_dynamic_css = implode(' ', $menu_icon_dynamic_css);
		}else{
			$menu_icon_dynamic_css ='';
		}

		$menu_hover_settings = $menu_settings['hover'];
		$menu_hover_dynamic_css = array();
		$menu_hover_icon_dynamic_css = array();

		if(isset($menu_hover_settings['background-color']) && $menu_hover_settings['background-color'] !=''){
			$menu_hover_dynamic_css[] = "background-color:{$menu_hover_settings['background-color']};";
// 			$menu_hover_icon_dynamic_css[] = "background-color:{$menu_hover_settings['background-color']};";
		}

		if(isset($menu_hover_settings['font-color']) && $menu_hover_settings['font-color'] !=''){
			$menu_hover_dynamic_css[] = "color:{$menu_hover_settings['font-color']} !important;";
			$menu_hover_icon_dynamic_css[] = "color:{$menu_hover_settings['font-color']};";
		}

		if(!empty($menu_hover_dynamic_css)){
			$menu_hover_dynamic_css = implode(' ', $menu_hover_dynamic_css);
		}else{
			$menu_hover_dynamic_css ='';
		}

		if(!empty($menu_hover_icon_dynamic_css)){
			$menu_hover_icon_dynamic_css = implode(' ', $menu_hover_icon_dynamic_css);
		}else{
			$menu_hover_icon_dynamic_css ='';
		}



		//admin menu submenu settings
		$sub_menu_settings = $admin_menu_settings['sub_menu'];
// 		echo "<pre>";
// 		print_r($sub_menu_settings);
// 		echo "</pre>";
// 		die();
		$sub_menu_dynamic_css = array();

		if($sub_menu_settings['background_selection']['type'] == 'image' && $sub_menu_settings['background_selection']['type'] !='') {
			$sub_menu_dynamic_css[] = "background-image: url({$sub_menu_settings['background_selection']['image']['url']});";
		}


		if($sub_menu_settings['background_selection']['type'] == 'background-color' && $sub_menu_settings['background_selection']['type'] !='') {
			$sub_menu_dynamic_css[] = "background-color:{$sub_menu_settings['background_selection']['background-color']['color']};";
		}

		if(isset($sub_menu_settings['font-color']) && $sub_menu_settings['font-color'] !=''){
			$sub_menu_dynamic_css[] = "color:{$sub_menu_settings['font-color']};";
		}

		if(isset($sub_menu_settings['font-size']) && $sub_menu_settings['font-size'] !=''){
			$sub_menu_dynamic_css[] = "font-size:{$sub_menu_settings['font-size']}px;";
		}

		if(isset($sub_menu_settings['google-fonts']) && $sub_menu_settings['google-fonts'] !=''){
			if(!in_array( $sub_menu_settings['google-fonts'], $google_fonts_used_array) ){
				array_push($google_fonts_used_array, preg_replace('/\s/', '+', $sub_menu_settings['google-fonts']) );
			}
			$sub_menu_dynamic_css[] = "font-family: {$sub_menu_settings['google-fonts']}, Dashicons;";
		}

		if(!empty($sub_menu_dynamic_css)){
			$sub_menu_dynamic_css = implode(' ', $sub_menu_dynamic_css);
		}else{
			$sub_menu_dynamic_css ='';
		}

		$sub_menu_hover_settings = $sub_menu_settings['hover'];
		$sub_menu_hover_dynamic_css = array();

		if(isset($sub_menu_hover_settings['background-color']) && $sub_menu_hover_settings['background-color'] !=''){
			$sub_menu_hover_dynamic_css[] = "background-color:{$sub_menu_hover_settings['background-color']} !important;";
		}

		if(isset($sub_menu_hover_settings['font-color']) && $sub_menu_hover_settings['font-color'] !=''){
			$sub_menu_hover_dynamic_css[] = "color:{$sub_menu_hover_settings['font-color']} !important;";
		}

		if(!empty($sub_menu_hover_dynamic_css)){
			$sub_menu_hover_dynamic_css = implode(' ', $sub_menu_hover_dynamic_css);
		}else{
			$sub_menu_hover_dynamic_css ='';
		}


		?>
		
		
		/* admin bar menu settings - font family, font color, font size */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks > ul > li > a,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar a.ab-item,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar>#wp-toolbar span.ab-label,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?>  #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li span.ab-label, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .ab-item,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .ab-icon, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile) > #wp-toolbar li span.ab-label, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile) > #wp-toolbar li span.ab-icon:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar #wp-admin-bar-site-name > .ab-item:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .ab-icon:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .ab-item:after,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .ab-item:before { <?php echo $admin_bar_menu_dynamic_css; ?> }

		/* admin bar menu hover - font color & background color */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar #wp-admin-bar-site-name:hover a.ab-item:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar #wp-admin-bar-site-name.hover a.ab-item:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar #wp-admin-bar-site-name a.ab-item:hover:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li:hover a.ab-item:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li.hover a.ab-item:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li a.ab-item:hover:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile) li:hover #adminbarsearch:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile) > #wp-toolbar li:hover span.ab-icon:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile) li:hover .ab-icon::before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile) li:hover .ab-item::after,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile) li:hover .ab-item::before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile) li:hover .ab-label,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .ab-top-menu>li.menupop.hover>.ab-item, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar.nojq .quicklinks .ab-top-menu>li>.ab-item:focus, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar.nojs .ab-top-menu>li.menupop:hover>.ab-item, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile) .ab-top-menu>li:hover>.ab-item, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile) .ab-top-menu>li>.ab-item:focus {
			<?php echo $admin_bar_menu_hover_dynamic_css; ?>
		}

		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .ab-sub-wrapper .menupop.hover>a, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop ul li a:focus, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop ul li a:focus strong, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop ul li a:hover, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop ul li a:hover strong, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop.hover ul li a:focus, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop.hover ul li a:hover, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li #adminbarsearch.adminbar-focused:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li .ab-item:focus .ab-icon:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li .ab-item:focus:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li a:focus .ab-icon:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li.hover .ab-icon:before,  
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li.hover .ab-item:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li:hover #adminbarsearch:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li:hover .ab-icon:before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar li:hover .ab-item:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar.nojs .quicklinks .menupop:hover ul li a:focus, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar.nojs .quicklinks .menupop:hover ul li a:hover,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?>  #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li:hover span.ab-label,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile)>#wp-toolbar li:hover span.ab-label,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile)>#wp-toolbar a:focus span.ab-label, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile)>#wp-toolbar li.hover span.ab-label, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar:not(.mobile)>#wp-toolbar li:hover span.ab-label {
			<?php echo $admin_bar_menu_hover_dynamic_css; ?>
		}
		
		/* admin bar submenu */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar ul.ab-submenu li a {
			<?php echo $admin_bar_submenu_dynamic_css; ?>
		}

		/* admin bar submenu*/
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .menupop .ab-sub-wrapper { <?php 
			echo $admin_bar_menu_background_dynamic_css; 
			if($template == 'temp-20'){
				echo "background-image: none !important";
			}
		?> 
		}
		
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop ul.ab-sub-secondary,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu {
			<?php echo $admin_bar_menu_background_dynamic_css;
			if($template == 'temp-20'){
				echo "background-image: none !important";
			}
			?>
		}


		/* admin bar submenu hover*/
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop ul li a:hover,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar .quicklinks .menupop.hover ul li a:hover {
			<?php echo $admin_bar_submenu_hover_dynamic_css; ?>
		}

		/*menu background color or image of menu , text color and font family */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu a{ <?php
			echo $menu_dynamic_css;
			?> }

		/* menu icon color  */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu div.wp-menu-image:before {
			<?php echo $menu_icon_dynamic_css; ?>
		}

		/* menu hover */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu a:hover, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li.menu-top:hover, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li.opensub>a.menu-top, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li>a.menu-top:focus,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li:hover > a { 
			<?php echo $menu_hover_dynamic_css; ?>
		 }

		/* menu icon hover color */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu a.current:hover div.wp-menu-image:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li a:focus div.wp-menu-image:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li.opensub div.wp-menu-image:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li.wp-has-current-submenu a:focus div.wp-menu-image:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li.wp-has-current-submenu div.wp-menu-image:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li.wp-has-current-submenu.opensub div.wp-menu-image:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li:hover div.wp-menu-image:before, 
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> .ie8 #adminmenu li.opensub div.wp-menu-image:before {
			<?php echo $menu_hover_icon_dynamic_css; ?>
		}

		/* current active main menu (hover) */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu > li.wp-has-current-submenu a.wp-has-current-submenu,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu > li.current a.current {
			<?php echo $menu_hover_dynamic_css; ?>
		}
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li.opensub > a.menu-top div.wp-menu-image::before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li > a.menu-top:hover div.wp-menu-image::before,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu > li.current a.current div.wp-menu-image::before {
			<?php echo "color:{$menu_hover_settings['font-color']} !important;"; ?>
		}

		/*
		 submenu color and background color
		 */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu .wp-has-current-submenu.opensub .wp-submenu li a,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu .wp-has-submenu .wp-submenu >li a {
			<?php echo $sub_menu_dynamic_css; ?>
		}

		/* submenu hover color and background color */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu .wp-has-submenu .wp-submenu li.current a,
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu .wp-has-submenu .wp-submenu li:hover a{
			<?php echo $sub_menu_hover_dynamic_css; ?>
		}

		/*
		current menu's active li submenu
		 */
		.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #adminmenu li.current a {
			<?php echo $sub_menu_hover_dynamic_css; ?>
		}

		/* for header texts settings */<?php
		$title_font_settings = $plugin_settings['general-settings']['admin-menu-header']['text']['title']['font-settings'];
		$subtitle_font_settings = $plugin_settings['general-settings']['admin-menu-header']['text']['subtitle']['font-settings'];
		
		$title_font_dynamic_css = array();
		if(isset($title_font_settings['font-color']) && $title_font_settings['font-color'] !=''){
			$title_font_dynamic_css[] = "color:{$title_font_settings['font-color']};";
		}

		if(isset($title_font_settings['font-size']) && $title_font_settings['font-size'] !=''){
			$title_font_dynamic_css[] = "font-size:{$title_font_settings['font-size']}px;";
		}

		if(isset($title_font_settings['google-fonts']) && $title_font_settings['google-fonts'] !=''){
			if(!in_array( $title_font_settings['google-fonts'], $google_fonts_used_array) ){
				array_push($google_fonts_used_array, preg_replace('/\s/', '+', $title_font_settings['google-fonts']) );
			}
			$title_font_dynamic_css[] = "font-family: {$title_font_settings['google-fonts']};";
		}

		if(!empty($title_font_dynamic_css)){
			$title_font_dynamic_css = implode(' ', $title_font_dynamic_css);
		}else{
			$title_font_dynamic_css ='';
		}

		$subtitle_font_dynamic_css = array();
		if(isset($subtitle_font_settings['font-color']) && $subtitle_font_settings['font-color'] !=''){
			$subtitle_font_dynamic_css[] = "color:{$subtitle_font_settings['font-color']};";
		}

		if(isset($subtitle_font_settings['font-size']) && $subtitle_font_settings['font-size'] !=''){
			$subtitle_font_dynamic_css[] = "font-size:{$subtitle_font_settings['font-size']}px;";
		}

		if(isset($subtitle_font_settings['google-fonts']) && $subtitle_font_settings['google-fonts'] !=''){
			if(!in_array( $subtitle_font_settings['google-fonts'], $google_fonts_used_array) ){
				array_push($google_fonts_used_array, preg_replace('/\s/', '+', $subtitle_font_settings['google-fonts']) );
			}
			$subtitle_font_dynamic_css[] = "font-family: {$subtitle_font_settings['google-fonts']};";
		}

		if(!empty($subtitle_font_dynamic_css)){
			$subtitle_font_dynamic_css = implode(' ', $subtitle_font_dynamic_css);
		}else{
			$subtitle_font_dynamic_css ='';
		}

		?>.eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> div.ir-admin-logo .ir-admin-menu-logo-title, .eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar div.ir-admin-logo .ir-admin-menu-logo-title { <?php 
			echo $title_font_dynamic_css; ?> }
		  .eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> div.ir-admin-logo .ir-admin-menu-logo-subtitle, .eat-body-class-wrap.eat-dashboard-<?php echo $template; ?> #wpadminbar div.eat-admin-logo .ir-admin-menu-logo-subtitle { <?php 
				echo $subtitle_font_dynamic_css; ?> }
		
		/*.eat-body-class-wrap.eat-dashboard-temp-2 #wpwrap {
		    background-image: url(http://www.lcps-consultants.fr/wp-content/uploads/2016/11/LCPsEngineering_BackgourndHome3.jpg);
		}*/
	</style>
<?php
	$google_fonts = implode('|', $google_fonts_used_array);
	if(!empty($google_fonts)){
		?>
		<link href="https://fonts.googleapis.com/css?family=<?php echo $google_fonts; ?>" rel="stylesheet">
		<?php
	}
?>