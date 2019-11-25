<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class Install
 */
class WP_Radio_Updater_Install {

	public static function activate() {
		self::update_option();
		self::create_pages();

	}

	private static function create_pages() {

	}

	private static function update_option() {

	}

}