<?php
/**
 * Adds a link back to the event, at the start of the order item meta section (which
 * displays in various locations).
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/community-tickets/modules/email-item-event-details.php
 *
 * @link http://m.tri.be/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */
?>
<div class="event-title" style="color:#999;font-size:90%;">
	<a href="<?php echo esc_url( get_permalink( $event_id ) ); ?>"><?php echo esc_html( $title ); ?></a>
</div>
