<?php

namespace your\space;

/**
 * Validates if value is not empty.
 *
 * @param mixed  $value
 * @param string $key
 * @param array  $data
 *
 * @return bool
 */
function is_not_empty( $value, $key = '', array $data = array() ) {
	return ! empty( $value );
}

/**
 * Checks if year is current or future.
 *
 * @param int    $value
 * @param string $key
 * @param array  $data
 *
 * @return bool
 */
function is_valid_year( $value, $key = '', array $data = array() ) {
	return $value >= date( 'Y' );
}

/**
 * Checks if month is valid.
 *
 * @param int    $value
 * @param string $key
 * @param array  $data
 *
 * @return bool
 */
function is_valid_month( $value, $key = '', array $data = array() ) {
	return $value >= 1 && $value <= 12;
}

/**
 * Checks if day is valid as a date of the month.
 *
 * @param int    $value
 * @param string $key
 * @param array  $data
 *
 * @return bool
 */
function is_valid_day( $value, $key = '', array $data = array() ) {
	return $value >= 1 && $value <= 31;
}

/**
 * Checks if hour is valid.
 *
 * @param int    $value
 * @param string $key
 * @param array  $data
 *
 * @return bool
 */
function is_valid_hour( $value, $key = '', array $data = array() ) {
	return $value >= 0 && $value <= 23;
}

/**
 * Checks if minute is valid.
 *
 * @param int    $value
 * @param string $key
 * @param array  $data
 *
 * @return bool
 */
function is_valid_minute( $value, $key = '', array $data = array() ) {
	return $value >= 0 && $value <= 59;
}