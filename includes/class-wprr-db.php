<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Centralized Database Class for WP Race Results
 */
class WPRR_DB
{
    /**
     * Get declared winners for a specific event, distance, and gender.
     */
    public static function get_declared_winners($event_id, $distance, $gender)
    {
        global $wpdb;
        $table_winner_rules = $wpdb->prefix . 'race_winner_rules';

        $clean_dist = str_replace(' ', '', $distance);
        $clean_gender = strtolower($gender);

        $declared = $wpdb->get_var($wpdb->prepare(
            "SELECT declared_winners FROM $table_winner_rules 
             WHERE event_id = %d AND REPLACE(distance, ' ', '') = %s AND LOWER(gender) = %s",
            $event_id,
            $clean_dist,
            $clean_gender
        ));

        // Use detailed logging to help diagnose issues
        if ($declared === null) {
            error_log("[WPRR DB] Rule lookup FAILED for Event $event_id, Dist '$distance', Gender '$gender'. Defaulting to " . WPRR_DEFAULT_WINNERS . ".");
            return WPRR_DEFAULT_WINNERS;
        }

        error_log("[WPRR DB] Rule FOUND for Event $event_id, Dist '$distance', Gender '$gender'. Declared: $declared");
        return (int) $declared;
    }

    /**
     * Get race results for a specific event, distance, and gender.
     */
    public static function get_race_results($event_id, $distance, $gender, $limit = 0)
    {
        global $wpdb;
        $table_results = $wpdb->prefix . 'race_results';

        $clean_dist = str_replace(' ', '', $distance);
        $clean_gender = strtolower($gender);
        $gender_clause = "";
        if ($clean_gender === 'm' || $clean_gender === 'male') {
            $gender_clause = "AND LOWER(gender) IN ('m', 'male')";
        } elseif ($clean_gender === 'f' || $clean_gender === 'female') {
            $gender_clause = "AND LOWER(gender) IN ('f', 'female')";
        }

        $query = "SELECT rank_overall, rank_gender, bib_number, full_name, chip_time
                  FROM $table_results
                  WHERE event_id = %d
                  AND REPLACE(distance, ' ', '') = %s
                  $gender_clause
                  ORDER BY rank_overall ASC, chip_time ASC";

        $params = [$event_id, $clean_dist];

        if ($limit > 0) {
            $query .= " LIMIT %d";
            $params[] = absint($limit);
        }

        return $wpdb->get_results($wpdb->prepare($query, ...$params));
    }

    /**
     * Get race events for the grid widget.
     */
    public static function get_events($limit = 6, $order_by = 'event_date', $order = 'DESC')
    {
        global $wpdb;
        $table_events = $wpdb->prefix . 'race_events';

        $allowed_order_by = ['event_date', 'created_at', 'event_name'];
        $allowed_order = ['ASC', 'DESC'];

        $order_by = in_array($order_by, $allowed_order_by) ? $order_by : 'event_date';
        $order = in_array($order, $allowed_order) ? $order : 'DESC';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_events ORDER BY $order_by $order LIMIT %d",
                absint($limit)
            )
        );
    }

    /**
     * Get all race events for select options.
     */
    public static function get_event_options()
    {
        global $wpdb;
        $table_events = $wpdb->prefix . 'race_events';

        $results = $wpdb->get_results("SELECT id, event_name FROM $table_events ORDER BY created_at DESC");

        $options = [];
        if ($results) {
            foreach ($results as $event) {
                $options[$event->id] = $event->event_name;
            }
        }

        return $options;
    }

    /**
     * Get all unique distances from the results table.
     */
    public static function get_all_distance_options()
    {
        global $wpdb;
        $table_results = $wpdb->prefix . 'race_results';

        return $wpdb->get_col("SELECT DISTINCT distance FROM $table_results ORDER BY distance ASC");
    }

    /**
     * Get a single event name by ID.
     */
    public static function get_event_name($event_id)
    {
        global $wpdb;
        $table_events = $wpdb->prefix . 'race_events';

        return $wpdb->get_var($wpdb->prepare("SELECT event_name FROM $table_events WHERE id = %d", $event_id));
    }

    /**
     * Get a map of Event ID => Distance Categories for all events.
     */
    public static function get_events_distances_map()
    {
        global $wpdb;
        $table_events = $wpdb->prefix . 'race_events';

        $results = $wpdb->get_results("SELECT id, distance_categories FROM $table_events");

        $map = [];
        if ($results) {
            foreach ($results as $event) {
                $map[$event->id] = $event->distance_categories;
            }
        }
        return $map;
    }
}
