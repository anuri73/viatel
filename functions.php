<?php
function array_filter_recursive( $input, $callback = 'boolval' ) {
	foreach ( $input as &$value ) {
		if ( is_array( $value ) ) {
			$value = array_filter_recursive( $value, $callback );
		}
	}

	return array_filter( $input, $callback );
}

/**
 * https://stackoverflow.com/a/43004994/7265862
 */
function array_map_assoc( callable $f, array $a ) {
	return array_column( array_map( $f, array_keys( $a ), $a ), 1, 0 );
}

function viatel_notice() {
	$notifications = get_option( 'viatel_notifications' );
	if ( ! empty( $notifications ) ) {
		$notifications = json_decode( $notifications, true );
		#notifications[0] = (string) Type of notification: error, updated or update-nag
		#notifications[1] = (string) Message
		#notifications[2] = (boolean) is_dismissible?
		switch ( $notifications[0] ) {
			case 'error': # red
			case 'updated': # green
			case 'update-nag': # ?
				$class = $notifications[0];
				break;
			default:
				# Defaults to error just in case
				$class = 'error';
				break;
		}

		$is_dismissible = '';
		if ( isset( $notifications[2] ) && $notifications[2] == true ) {
			$is_dismissible = 'is_dismissible';
		}
		echo '<div class="' . $class . ' notice ' . $is_dismissible . '">';
		echo '<p>' . $notifications[1] . '</p>';
		echo '</div>';
		# Let's reset the notification
		update_option( 'viatel_notifications', false );
	}
}
