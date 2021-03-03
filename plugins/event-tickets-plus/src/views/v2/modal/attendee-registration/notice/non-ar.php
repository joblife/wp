<?php
/**
 * Modal: Attendee Registration > Notice > Non AR tickets.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets-plus/v2/modal/attendee-registration/notice/non-ar.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1amp
 *
 * @since 5.1.0
 *
 * @version 5.1.0
 *
 * @var int $non_meta_count The number of tickets in cart, without meta fields.
 */

$notice_classes = [
	'tribe-tickets__notice--non-ar',
	'tribe-common-a11y-hidden' => empty( $non_meta_count ),
];

/** @var Tribe__Tickets__Editor__Template $et_template */
$et_template = tribe( 'tickets.editor.template' );

$et_template->template(
	'components/notice',
	[
		'notice_classes' => $notice_classes,
		'content'        => sprintf(
			// Translators: %s: The HTML wrapped number of tickets.
			esc_html_x(
				'There are %s other tickets in your cart that do not require attendee information.',
				'Note that there are more tickets in the cart, %s is the html-wrapped number.',
				'event-tickets-plus'
			),
			'<span id="tribe-tickets__non-ar-count">' . absint( $non_meta_count ) . '</span>'
		),
	]
);
