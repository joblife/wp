<?php

$plugin_settings = $this->plugin_settings;
if(isset($plugin_settings['admin_bar']['hide_in_frontend'])){
	add_filter('show_admin_bar', '__return_false');
}
