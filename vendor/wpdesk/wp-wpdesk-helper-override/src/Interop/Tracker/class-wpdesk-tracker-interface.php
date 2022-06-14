<?php

interface WPDesk_Tracker_Interface {

	/**
	 * Setter for object that sends data.
	 *
	 * @param WPDesk_Tracker_Sender $sender Object that can send payloads.
	 */
	public function set_sender( WPDesk_Tracker_Sender $sender );

	/**
	 * Attach data provider class to tracker
	 *
	 * @param WPDesk_Tracker_Data_Provider $provider
	 *
	 * @return void
	 */
	public function add_data_provider( WPDesk_Tracker_Data_Provider $provider );
}
