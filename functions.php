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
