<?php
/*
Plugin Name: CF Whodunit
Plugin URI: http://crowdfavorite.com/
Description: This will add an HTML comment at the end of every page including some information regarding the server(s) used to generate the page, the amount of time the page took to generate on the server, and the datetime when the page was generated.
Author: Crowd Favorite
Version: 1.0
Author URI: http://crowdfavorite.com/
*/

class cf_whodunit {
	static function on_shutdown() {
		global $wpdb;
		$used_db_servers = array();
		if (!empty($wpdb->used_servers)) { // HyperDB configuration
			foreach ($wpdb->used_servers as $server) {
				$used_db_servers[$server['host']] = apply_filters('cf_debug_db_server', $server['host']);
			}
		}
		else if (defined('DB_HOST')) {
			$used_db_servers[DB_HOST] = apply_filters('cf_debug_db_server', DB_HOST);
		}
		$web_server = esc_html(apply_filters('cf_debug_web_server', $_SERVER['SERVER_ADDR']));
		$gen_duration = timer_stop();
		$gen_date = current_time('mysql');
		$output = '<!-- Page generated ';
		if (!empty($web_server)) {
				$output .= "by [{$web_server}] ";
		}
		if (!empty($used_db_servers)) {
				$db_output = esc_html(implode(', ', $used_db_servers));
				$output .= "using [{$db_output}] ";
		}
		if (!empty($gen_duration)) {
				$output .= "in {$gen_duration} seconds ";
		}
		if (!empty($gen_date)) {
				$output .= "on {$gen_date} ";
		}
		$output .= '-->';
		echo $output;
	}
	
}
add_action('shutdown', 'cf_whodunit::on_shutdown', 1000);