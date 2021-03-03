<?php
$plugin_settings    = get_option('eat_admin_theme_settings');
$dashboard_settings = $plugin_settings['dashboard'];


if(isset($dashboard_settings['custom-widget']['enable'])){
	add_action( 'wp_dashboard_setup', 'eat_add_dashboard_widgets' );
}

add_action( 'wp_dashboard_setup', 'remove_dashboard_widgets' );

function remove_dashboard_widgets() {
	$plugin_settings = get_option('eat_admin_theme_settings');
	$dashboard_settings = $plugin_settings['dashboard'];
	// echo "<pre>";
	// print_r($dashboard_settings);
	// echo "</pre>";
	if(isset($dashboard_settings['hide_welcome_panel'])){
		remove_action('welcome_panel', 'wp_welcome_panel');
	}

	if(isset($dashboard_settings['hide_wordpress_events_news'])){
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );   // Other WordPress News (Wordpress events and news)
	}

	if(isset($dashboard_settings['hide_quick_draft'])){
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );  // Quick Press (Quick draft)
	}

	if(isset($dashboard_settings['hide_at_a_glance'])){
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );   // Right Now (At a glance)
	}

	if(isset($dashboard_settings['hide_activity'])){
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );   // (activity)
	}

	if(isset($dashboard_settings['hide_recent_draft'])){
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );  // Recent Drafts
	}

	if(isset($dashboard_settings['hide_recent_comments'])){
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' ); // Recent Comments
	}

	if(isset($dashboard_settings['hide_incoming_links'])){
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );  // Incoming Links
	}

	if(isset($dashboard_settings['hide_plugins'])){
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );   // Plugins
	}

	if(isset($dashboard_settings['hide_wordpress_blog'])){
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );   // WordPress blog
	}
	// use 'dashboard-network' as the second parameter to remove widgets from a network dashboard.
}



/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function eat_add_dashboard_widgets() {
	$plugin_settings = get_option('eat_admin_theme_settings');
	$dashboard_settings = $plugin_settings['dashboard'];
	// echo "<pre>";
	// print_r($dashboard_settings);
	// echo "</pre>";
	// die();
	// $widget_title = $dashboard_settings['custom-widget']['widget_title'];
	// $widget_slug = strtolower(str_replace( ' ','-', $widget_title ));
	for($i=1; $i<=5; $i++ ){
	// echo "<pre>";
	// print_r($dashboard_settings);
	// echo "</pre>";
		// $widget_slug = "eat_widget_slug_$i";
		// $widget_title = "eat_widget_title_$i";
			$widget_title = isset($dashboard_settings['custom-widget'][$i]['widget_title']) ? $dashboard_settings['custom-widget'][$i]['widget_title'] : '';
			$widget_content = isset($dashboard_settings['custom-widget'][$i]['widget_content']) ? $dashboard_settings['custom-widget'][$i]['widget_content'] : '';
		if($widget_title !='' || $widget_content !=''){
			$widget_slug = strtolower(str_replace( ' ','-', $widget_title ));
			wp_add_dashboard_widget(
				$widget_slug,         // Widget slug.
				$widget_title,         // Title.
				'eat_dashboard_widget_function', // Display function.
				null,
	        	"$i"
		    );
		}
	}

}

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function eat_dashboard_widget_function( $var, $args) {
	// var_dump($args);
	$id = $args['args'];
	$plugin_settings = get_option('eat_admin_theme_settings');
	$dashboard_settings = $plugin_settings['dashboard'];
	$widget_content = $dashboard_settings['custom-widget'][$id]['widget_content'];
	// Display whatever it is you want to show.
	echo $widget_content;
}