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
			event_date date DEFAULT NULL,
			location varchar(255) DEFAULT NULL,
			banner_image varchar(255) DEFAULT NULL,
			distance_categories varchar(255) DEFAULT '',
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

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql_events);
		dbDelta($sql_results);
		dbDelta($sql_winner_rules);
	}

}
