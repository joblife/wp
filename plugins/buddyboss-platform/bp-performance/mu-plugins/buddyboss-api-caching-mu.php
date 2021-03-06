<?php
/**
 * Plugin Name: BuddyBoss API Caching
 * Description: Must-Use plugin for BuddyBoss App to enable API Caching.
 * Version: 1.0.0
 * Author: BuddyBoss
 * Author URI:  https://www.buddyboss.com
 *
 * @package BuddyBoss\Performance
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Load the class from Bundle BuddyBoss Platform.
 */
// File from api plugin.
$buddyboss_platform_api = WP_PLUGIN_DIR . '/buddyboss-platform-api/buddyboss-api-mu-loader.php';

// File from the build version.
$buddyboss_performance = WP_PLUGIN_DIR . '/buddyboss-platform/bp-performance/bp-performance-mu-loader.php';

// File from the development version.
$buddyboss_performance_dev = WP_PLUGIN_DIR . '/buddyboss-platform/src/bp-performance/bp-performance-mu-loader.php';

// Cache [ performance ].
if ( file_exists( $buddyboss_platform_api ) ) {
	/**
	 * Included File.
	 */
	require_once $buddyboss_platform_api;
} elseif ( file_exists( $buddyboss_performance ) ) {
	/**
	 * Included File.
	 */
	require_once $buddyboss_performance;
} elseif ( file_exists( $buddyboss_performance_dev ) ) {
	/**
	 * Included File.
	 */
	require_once $buddyboss_performance_dev;
}

/**
 * Load the class from Bundle BuddyBoss APP.
 */
$include = WP_PLUGIN_DIR . '/buddyboss-app/include/Performance/buddyboss-app-mu-loader.php';
// Cache [ performance ].
if ( file_exists( $include ) ) {
	/**
	 * Included File.
	 *
	 * @var string $include file path.
	 */
	require_once $include;
}
