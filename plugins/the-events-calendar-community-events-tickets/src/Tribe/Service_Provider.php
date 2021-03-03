<?php

use Tribe\Community\Tickets\Migration\Queue;

/**
 * Class Tribe__Events__Community__Tickets__Service_Provider
 *
 * Provides the Community Tickets service.
 *
 * This class should handle implementation binding, builder functions and hooking for any first-level hook and be
 * devoid of business logic.
 *
 * @since 4.6.2
 */
class Tribe__Events__Community__Tickets__Service_Provider extends tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.6.2
	 */
	public function register() {
		$this->container->singleton( 'community-tickets.main', Tribe__Events__Community__Tickets__Main::instance() );
		$this->container->singleton( 'community-tickets.fee-handler', Tribe__Events__Community__Tickets__Fee_Handler::class );
		$this->container->singleton( 'community-tickets.fees', Tribe__Events__Community__Tickets__Fees::class );
		$this->container->singleton( 'community-tickets.assets', Tribe__Events__Community__Tickets__Assets::class );
		$this->container->singleton( 'community-tickets.report.sales', Tribe__Events__Community__Tickets__Reports__Sales::class );
		$this->container->singleton( 'community-tickets.shortcodes', Tribe__Events__Community__Tickets__Shortcodes::class );

		// Add the old way for backwards compatibility.
		$this->container->singleton( 'community.tickets.fees', Tribe__Events__Community__Tickets__Fees::class );
		$this->container->singleton( 'community.tickets.fees.migration', Queue::class );

		$this->hook();
	}

	/**
	 * Any hooking for any class needs happen here.
	 *
	 * In place of delegating the hooking responsibility to the single classes they are all hooked here.
	 *
	 * @since 4.6.2
	 */
	protected function hook() {
		add_action( 'init', tribe_callback( 'community-tickets.shortcodes', 'hooks' ) );
		add_action( 'init', tribe_callback( 'community-tickets.fees', 'hooks' ) );
		add_action( 'init', tribe_callback( 'community-tickets.report.sales', 'hooks' ) );
		add_action( 'init', tribe_callback( 'community.tickets.fees.migration', 'hooks' ) );
		add_action( 'tribe_tickets_price_description', tribe_callback( 'community-tickets.main', 'tickets_price_description'), 10, 2);
		add_action( 'tribe_tickets_price_disabled', tribe_callback( 'community-tickets.main', 'tickets_price_disabled'), 10, 2);
	}
}
