<?php

interface WPDesk_Tracker_Data_Provider {

	/**
	 * Provides data
	 *
	 * @return array Data provided to tracker.
	 */
	public function get_data();

}