<?php
/**
 * IAC resend email checkbox template used by "My Tickets" page.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets-plus/v2/iac/my-tickets/resend-email-template.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1amp
 *
 * @since 5.1.0
 *
 * @version 5.1.0
 *
 * @var WP_Post $ticket The ticket post object.
 * @var array $attendee The attendee information.
 * @var string $field_slug_for_resend_email The slug to use for the Re-send Email field.
 */

/**
 * This filter allows the admin to control the re-send email option when an attendee's email is updated.
 *
 * @since 5.1.0
 *
 * @param bool    $iac_resend_email Whether to allow email resending.
 * @param WP_Post $ticket           The ticket post object.
 * @param array   $attendee         The attendee information.
 */
$iac_resend_email = (int) apply_filters( 'tribe_tickets_my_tickets_allow_email_resend_on_attendee_email_update', true, $ticket, $attendee );

if ( empty( $iac_resend_email ) ) {
	return;
}
?>
<script
	class="tribe-tickets__tickets-page-attendee-meta-resend-email-template"
	id="tmpl-tribe-tickets__tickets-page-attendee-meta-resend-email-template-<?php echo esc_attr( $attendee['attendee_id'] ); ?>"
	type="text/template"
>
	<div class="tribe-tickets__tickets-page-attendee-meta-resend-email">
		<label
			class="tribe-tickets-meta-field-header"
			for="tribe-tickets-plus-iac-resend-<?php echo esc_attr( $attendee['attendee_id'] ); ?>"
		>
			<input
				type="checkbox"
				class="ticket-meta"
				name="tribe-tickets-meta[<?php echo esc_attr( $attendee['attendee_id'] ); ?>][<?php echo esc_attr( $field_slug_for_resend_email ); ?>]"
				id="tribe-tickets-plus-iac-resend-<?php echo esc_attr( $attendee['attendee_id'] ); ?>"
				checked
			/>
			<span class="tribe-tickets-meta-option-label">
				<?php esc_html_e( 'Re-send ticket email', 'event-tickets-plus' ); ?>
			</span>
		</label>
	</div>
</script>
