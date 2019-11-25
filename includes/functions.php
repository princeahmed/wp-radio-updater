<?php

defined( 'ABSPATH' ) || exit;

function wr_updater_checker( $url ) {
	try {
		@$headers = get_headers( $url );

		if ( ! is_array( $headers ) ) {
			return false;
		}

		return ( 'HTTP/1.0 200 OK' == $headers[0] );

	} catch ( Exception $exception ) {
		return false;
	}
}


