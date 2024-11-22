<?php

namespace SMPLFY\boilerplate;

class WordpressAdapter {
	private WPHeartbeatExample $wpHeartbeatExample;

	public function __construct( WPHeartbeatExample $wpHeartbeatExample ) {
		$this->wpHeartbeatExample = $wpHeartbeatExample;

		$this->register_hooks();
		$this->register_filters();
	}

	/**
	 * Register Wordpress hooks to handle custom logic
	 *
	 * @return void
	 */
	public function register_hooks() {

	}

	/**
	 * Register Wordpress filters to handle custom logic
	 *
	 * @return void
	 */
	public function register_filters() {
		add_filter( 'heartbeat_received', [ $this->wpHeartbeatExample, 'receive_heartbeat' ], 10, 2 );
	}
}