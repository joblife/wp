<?php
/**
 * Class Tribe__Events__Community__Tickets__Updater
 *
 * @since 4.5.4
 */
class Tribe__Events__Community__Tickets__Updater extends Tribe__Updater {

	protected $version_option = 'tribe-events-community-tickets-schema-version';

	/**
	 * Force upgrade script to run even without an existing version number
	 * The version was not previously stored for Community Tickets.
	 *
	 * @since 4.5.4
	 *
	 * @return bool
	 */
	public function is_new_install() {
		return false;
	}

	/**
	 * Returns an array of callbacks that should be called
	 * every time the version is updated.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function get_constant_update_callbacks() {
		return array(
			array( $this, 'migrate_fee_data_to_existing_orders' ),
		);
	}

	/**
	 * Trigger Setup of Cron Task to Add Fee Data to Community Ticket Orders.
	 *
	 * @since 4.7.0
	 */
	public function migrate_fee_data_to_existing_orders() {
		/** @var \Tribe\Community\Tickets\Migration\Queue $migration */
		$migration = tribe( 'community.tickets.fees.migration' );

		// Trigger adding task to cron.
		if ( 'complete' !== $migration->get_current_offset() ) {
			$migration->action_init();
		}
	}
}
