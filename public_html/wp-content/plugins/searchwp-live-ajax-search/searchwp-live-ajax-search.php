<?php
/*
Plugin Name: SearchWP Live Ajax Search
Plugin URI: https://searchwp.com/
Description: Enhance your search forms with live search, powered by SearchWP (if installed)
Version: 1.4.6
Requires PHP: 5.6
Author: SearchWP, LLC
Author URI: https://searchwp.com/
Text Domain: swplas

Copyright 2014-2020 SearchWP, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Widget support
include_once( dirname( __FILE__ ) . '/includes/class-widget.php' );

/**
 * Class SearchWP_Live_Search
 *
 * The main SearchWP Live Ajax Search Class properly routes searches and all other requests/utilization
 *
 * @since 1.0
 */
class SearchWP_Live_Search {
	public $dir;
	public $url;
	public $version = '1.4.6';
	public $results = array();

	function __construct() {
		$this->dir = dirname( __FILE__ );
		$this->url = plugins_url( 'searchwp-live-ajax-search', $this->dir );
	}
}

function searchwp_live_search_request_handler( $execute_search = false ) {
	include_once dirname( __FILE__ ) . '/includes/class-client.php';
	include_once dirname( __FILE__ ) . '/includes/class-relevanssi-bridge.php';

	$client = new SearchWP_Live_Search_Client();
	$client->setup();

	if ( $execute_search ) {
		$client->search();
	}
}

/**
 * Bootloader
 *
 * @since 1.0
 */
function searchwp_live_search_init() {
	load_plugin_textdomain( 'swplas', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// if an AJAX request is taking place, it's potentially a search so we'll want to prepare for that
	// else we'll prep the environment for the search form itself
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		searchwp_live_search_request_handler();
	} else {
		include_once dirname( __FILE__ ) . '/includes/class-form.php';
		$form = new SearchWP_Live_Search_Form();
		$form->setup();
	}
}

add_action( 'init', 'searchwp_live_search_init' );
