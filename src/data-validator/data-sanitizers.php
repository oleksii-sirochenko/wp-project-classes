<?php

namespace your\space;

/**
 * Recursively casts any object to array.
 *
 * @param $input
 *
 * @return array|mixed
 */
function object_to_array( $input ) {
	if ( is_scalar( $input ) ) {
		return $input;
	}
	
	return array_map( '\your\space\object_to_array', (array) $input );
}

/**
 * Converts value to int.
 *
 * @param mixed  $value
 * @param string $key
 * @param array  $data
 *
 * @return int
 */
function sanitize_int( $value, $key, array $data ) {
	return (int) $value;
}

/**
 * Converts value to float.
 *
 * @param mixed  $value
 * @param string $key
 * @param array  $data
 *
 * @return float
 */
function sanitize_float( $value, $key, array $data ) {
	return (float) $value;
}

/**
 * Converts value to float.
 *
 * @param mixed  $value
 * @param string $key
 * @param array  $data
 *
 * @return bool
 */
function sanitize_bool( $value, $key, array $data ) {
	if ( is_bool( $value ) ) {
		return $value;
	} elseif ( is_numeric( $value ) ) {
		return (bool) $value;
	} elseif ( is_string( $value ) ) {
		if ( in_array( strtolower( $value ), array( 'true', 'on', 'yes' ), true ) ) {
			return true;
		} elseif ( in_array( strtolower( $value ), array( 'false', 'off', 'no' ), true ) ) {
			return false;
		}
	}
	
	return (bool) $value;
}