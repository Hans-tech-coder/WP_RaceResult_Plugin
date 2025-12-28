<?php
/**
 * Fired during plugin activation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Race_Results
 * @subpackage WP_Race_Results/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WP_Race_Results
 * @subpackage WP_Race_Results/includes
 * @author     Your Name <email@example.com>
 */
class WP_Race_Results_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Table: wp_race_events.
		$table_events = $wpdb->prefix . 'race_events';
		$sql_events = "CREATE TABLE $table_events (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			event_name varchar(255) NOT NULL,
			slug varchar(255) DEFAULT NULL,
			event_date date DEFAULT NULL,
			location varchar(255) DEFAULT NULL,
			banner_image varchar(255) DEFAULT NULL,
			event_logo varchar(255) DEFAULT NULL,
			social_media_links longtext DEFAULT NULL,
			distance_categories varchar(255) DEFAULT '',
			results_page_id bigint(20) DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;";

		// Table: wp_race_winner_rules.
		$table_winner_rules = $wpdb->prefix . 'race_winner_rules';
		$sql_winner_rules = "CREATE TABLE $table_winner_rules (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			event_id bigint(20) NOT NULL,
			distance varchar(20) NOT NULL,
			gender varchar(10) NOT NULL,
			declared_winners int(11) DEFAULT 3,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY event_dist_gender (event_id, distance(20), gender(10)),
			KEY event_id (event_id)
		) $charset_collate;";

		// Table: wp_race_results.
		$table_results = $wpdb->prefix . 'race_results';
		$sql_results = "CREATE TABLE $table_results (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			event_id bigint(20) NOT NULL,
			bib_number int(11) DEFAULT 0,
			full_name varchar(255) DEFAULT '',
			gender varchar(10) DEFAULT '',
			distance varchar(50) DEFAULT '',
			gun_time varchar(20) DEFAULT '',
			chip_time varchar(20) DEFAULT '',
			rank_overall int(11) DEFAULT 0,
			rank_gender int(11) DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY event_id (event_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql_events);
		dbDelta($sql_results);
		dbDelta($sql_winner_rules);
	}

}
