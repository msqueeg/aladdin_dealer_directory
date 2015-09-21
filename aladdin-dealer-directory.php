<?php
/**
 * Plugin Name: Aladdin Dealer Directory
 * Plugin URI: http://michael-miller.org
 * Description: This plugin adds a dealer directory to your WordPress website.
 * Version: 1.0.0
 * Author: Michael Miller
 * Author URI: http://michael-miller.org
 * License: GPL2
 */

global $aladdin_db_version;
$aladdin_db_version = '1.0';

if(!class_exists('WP_List_table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

if(!class_exists('Link_List_Table')){
	require_once(plugin_dir_path(__FILE__) . '/link_list_table.php');
}

function install_dealer_table(){
	global $wpdb;
	global $aladdin_db_version;

	$table_name = $wpdb->prefix . 'aladdin_dealers';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name(
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name varchar(55) NOT NULL,
		phone varchar(55) NOT NULL,
		email varchar(55) DEFAULT '' NOT NULL,
		street varchar(55) DEFAULT '' NOT NULL,
		city varchar(55) DEFAULT '' NOT NULL,
		state varchar(55) DEFAULT '' NOT NULL,
		zip mediumint(5) DEFAULT 0 NOT NULL,
		latitude varchar(55),
		longitude varchar(55),
		website varchar(100) DEFAULT '' NOT NULL,
		UNIQUE KEY id (id)
		) $charset_collate;";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta( $sql );

	add_option('aladdin_db_version', $aladdin_db_version);
}

function install_dealer_data(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'aladdin_dealers';

	$wpdb->insert(
			$table_name,
			array(
				'time' => current_time( 'mysql'),
				'name' => mysql_real_escape_string("Lehman's Hardware & Appliances"),
				'phone' => mysql_real_escape_string("000-000-0000"),
				'email' => mysql_real_escape_string("no-reply@lehmans.com"),
				'street' => mysql_real_escape_string("1 Lehman's Way"),
				'city' => mysql_real_escape_string("Dalton"),
				'state' => mysql_real_escape_string("OH"),
				'zip' => mysql_real_escape_string("44631"),
				'latitude' => mysql_real_escape_string("000"),
				'longitude' => mysql_real_escape_string("000"),
				'website' => mysql_real_escape_string("http://www.lehmans.com")

				)
		);
}

register_activation_hook( __FILE__, 'install_dealer_table');
register_activation_hook( __FILE__, 'install_dealer_data');

 add_action('admin_menu', 'aladdin_dealer_setup_menu');

 function aladdin_dealer_setup_menu(){
 	add_menu_page('Dealer Directory Page', 'Dealer Directory', 'manage_options', 'aladdin-dealers', 'dealer_init');
 }

 function dealer_init(){
 	$wp_list_table = new Link_List_Table();
 	$wp_list_table-> prepare_items();
 	$wp_list_table->display();
 }

 ?>