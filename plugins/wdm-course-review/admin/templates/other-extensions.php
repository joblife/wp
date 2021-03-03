<?php
/**
 * Partial: Page - Extensions.
 *
 * @var object
 * @package RatingsReviewsFeedback\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$extensions = get_transient( '_crr_extensions_data' );
if ( false === ( $extensions ) ) {
	$extensions_json = wp_remote_get(
		'https://wisdmlabs.com/products-thumbs/ld_extensions.json',
		array(
			'user-agent' => 'RRF Extensions Page',
		)
	);
	if ( ! is_wp_error( $extensions_json ) ) {
		$extensions = json_decode( wp_remote_retrieve_body( $extensions_json ) );

		if ( $extensions ) {
			set_transient( '_crr_extensions_data', $extensions, 72 * HOUR_IN_SECONDS );
		}
	}
}

?>
<div id="ir-other-extensions">
	<?php
	if ( $extensions ) {
		?>
		<ul class="extensions">
		<?php
			$extensions = $extensions->ld_extension;
			$i = 0;
		foreach ( $extensions as $extension ) {
			if ( $i > 7 ) {
				break;
			}

			// If plugin is already installed, don't list this plugin.
			if ( file_exists( WP_PLUGIN_DIR . '/' . $extension->dir . '/' . $extension->plug_file ) ) {
				continue;
			}

			echo '<li class="product" title="' . esc_attr__( 'Click here to know more', 'wdm_instructor_role' ) . '">';
			echo '<a href="' . esc_attr( $extension->link ) . '" target="_blank">';
			echo '<h3>' . esc_html( $extension->title ) . '</h3>';
			if ( ! empty( $extension->image ) ) {
				echo '<img src="' . esc_attr( $extension->image ) . '"/>';
			}
			echo '<p>' . esc_html( $extension->excerpt ) . '</p>';
			echo '</a>';
			echo '</li>';
			++$i;
		}
		?>
		</ul>
		<?php
		// If all the extensions have been installed on the site.
		if ( 0 == $i ) {
			?>
		<h1 class="thank-you"><?php esc_html_e( 'You have all of our extensions. Thank you for your support!', 'wdm_instructor_role' ); ?></h1>
			<?php
		}
	}
	?>
	<p>
		<a href="https://wisdmlabs.com/learndash-extensions/" target="_blank" class="browse-all">
		<?php esc_html_e( 'Browse all our extensions', 'wdm_instructor_role' ); ?>
		</a>
	</p>
</div>
<?php
unset( $extensions );
$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ) ? '' : '.min';

wp_register_style( 'wdmir-promotion', plugins_url( 'css/extension' . $min . '.css', __DIR__ ), array(), '2.0.0' );

// Enqueue admin styles.
wp_enqueue_style( 'wdmir-promotion' );
