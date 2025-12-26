<?php
/**
 * Standalone CSV Import Handler
 * 
 * This file handles CSV imports for the WP Race Results plugin.
 * It is required directly by the main plugin file to ensure
 * capability on all hosting environments.
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Handle CSV import via admin_init.
 * LISTENS for: $_POST['wp_race_import_csv']
 */
function wprr_handle_csv_import_standalone()
{
    // Only run if our specific hidden input is present.
    if (!isset($_POST['wp_race_import_csv'])) {
        return;
    }

    // 1. Verify nonce
    if (!isset($_POST['wp_race_import_nonce']) || !wp_verify_nonce($_POST['wp_race_import_nonce'], 'wp_race_import_csv')) {
        wp_die('Security check failed. Please try again.');
    }

    // 2. Verify capability
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // 3. Validate file and event
    if (empty($_FILES['csv_file']['tmp_name'])) {
        wp_die('Please select a CSV file.');
    }

    $event_id = isset($_POST['event_id']) ? absint($_POST['event_id']) : 0;
    if (!$event_id) {
        wp_die('Please select an event.');
    }

    // 4. Open CSV
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, 'r');

    if ($handle !== false) {
        global $wpdb;
        $table_results = $wpdb->prefix . 'race_results';
        $row_count = 0;

        // Check for BOM (Byte Order Mark) and skip it if present.
        $bom = fread($handle, 3);
        if ("\xEF\xBB\xBF" !== $bom) {
            rewind($handle);
        } else {
            fseek($handle, 3);
        }

        // Detect delimiter (comma or semicolon).
        $first_line = fgets($handle);
        $delimiter = (substr_count($first_line, ';') > substr_count($first_line, ',')) ? ';' : ',';
        rewind($handle);
        if ("\xEF\xBB\xBF" === $bom) {
            fseek($handle, 3);
        }

        // Skip header row
        fgetcsv($handle, 0, $delimiter);

        // 6. Process rows
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            // Validate row has at least Bib Number or Name
            // 0=bib, 1=name, 2=gender, 3=distance, 4=gun, 5=chip, 6=rank_overall, 7=rank_gender

            // Basic empty check for first two columns
            if (empty($data[0]) && empty($data[1])) {
                continue;
            }

            $wpdb->insert(
                $table_results,
                array(
                    'event_id' => $event_id,
                    'bib_number' => isset($data[0]) ? absint($data[0]) : 0,
                    'full_name' => isset($data[1]) ? sanitize_text_field($data[1]) : '',
                    'gender' => isset($data[2]) ? sanitize_text_field($data[2]) : '',
                    'distance' => isset($data[3]) ? sanitize_text_field($data[3]) : '',
                    'gun_time' => isset($data[4]) ? sanitize_text_field($data[4]) : '',
                    'chip_time' => isset($data[5]) ? sanitize_text_field($data[5]) : '',
                    'rank_overall' => isset($data[6]) ? absint($data[6]) : 0,
                    'rank_gender' => isset($data[7]) ? absint($data[7]) : 0,
                )
            );
            $row_count++;
        }
        fclose($handle);

        // 8. Redirect with success message
        wp_redirect(admin_url('admin.php?page=wp_race_results_import&message=imported&count=' . $row_count));
        exit;
    } else {
        wp_die('Could not open CSV file.');
    }
}
add_action('admin_init', 'wprr_handle_csv_import_standalone');
