<?php
add_filter( 'admin_footer_text', 'remove_footer_admin' );
add_filter( 'update_footer', 'change_footer_version', 9999);

function remove_footer_admin ($msg) {
	$plugin_settings = get_option('eat_admin_theme_settings');
	$footer_settings = $plugin_settings['footer_info'];

	if(isset($footer_settings['hide-all'])){
		return " ";
	}else{

		if(isset($footer_settings['left']['mysql_version']['enable'])){
			$mysql_version = " | MySQL Version: ".everestAdminThemeClass:: get_mysql_version();
		}else {
			$mysql_version = '';
		}

		if(isset($footer_settings['left']['php_version']['enable'])){
			$php_version = " | PHP Version: ".everestAdminThemeClass::get_php_version();
		}else{
			$php_version = '';
		}

		if(isset($footer_settings['left']['hide'])){
			return " ";
		}else{
			if(isset($footer_settings['left']['hide-default'])){
				$msg = ' ';
			}
			if(isset($footer_settings['left']['custom_texts']['enable'])){
				$custom_msg = $footer_settings['left']['custom_texts']['content'].' ';
				$msg = $msg.$custom_msg.$php_version.$mysql_version;
				return $msg;
			}else{
				return $msg.$php_version.$mysql_version;
			}
		}
	}

}

function change_footer_version($msg) {
	$plugin_settings = get_option('eat_admin_theme_settings');
	$footer_settings = $plugin_settings['footer_info'];
	if(isset($footer_settings['hide-all'])){
		return " ";
	}else{

		if(isset($footer_settings['right']['mysql_version']['enable'])){
			$mysql_version = " | MySQL Version: ".everestAdminThemeClass:: get_mysql_version();
		}else {
			$mysql_version = '';
		}

		if(isset($footer_settings['right']['php_version']['enable'])){
			$php_version = " | PHP Version: ".everestAdminThemeClass::get_php_version();
		}else{
			$php_version = '';
		}

		if(isset($footer_settings['right']['hide'])){
			return " ";
		}else{
			if(isset($footer_settings['right']['hide-default'])){
				$msg = ' ';
			}
			if(isset($footer_settings['right']['custom_texts']['enable'])){
				$custom_msg = $footer_settings['right']['custom_texts']['content'].' ';
				$msg = $msg.$custom_msg.$php_version.$mysql_version;
				return $msg;
			}else{
				return $msg.$php_version.$mysql_version;
			}
		}
	}

}