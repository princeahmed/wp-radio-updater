<?php

defined( 'ABSPATH' ) || exit();

class WR_Updater_Checker extends WP_Background_Process {

	protected $action = 'wp_radio_check_stream_link';

	protected function task( $id ) {

		$stream_link = prince_get_meta( $id, 'stream_url' );
		$check       = $this->checker( $stream_link );

		error_log( $id . ' - ' . $stream_link . ' - ' . ( $check ? 'OK' : 'Dead') );

		return false;
	}

	private function checker( $url ) {
		try {
			@$headers = get_headers( $url );

			if ( ! is_array( $headers ) ) {
				return false;
			}

			return ( in_array( 'HTTP/1.0 200 OK', $headers ) );

		} catch ( Exception $exception ) {
			return false;
		}
	}

	protected function complete() {
		parent::complete();
	}

}

$process = new WR_Updater_Checker();

if ( isset( $_GET['start'] ) ) {

	$posts = wp_radio_get_stations( [ 'posts_per_page' => - 1 ] );
	$ids   = wp_list_pluck( $posts, 'ID' );

	foreach ( $ids as $id ) {
		$process->push_to_queue( $id );
	}

	$process->save()->dispatch();
}