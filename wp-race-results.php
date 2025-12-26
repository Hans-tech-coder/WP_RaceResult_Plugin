<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * administrative area. This file also includes all of the plugin dependencies.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           WP_Race_Results
 *
 * @wordpress-plugin
 * Plugin Name:       WP Race Results
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       A plugin to manage race results.
 * Version:           1.0.0
 * Author:            HansTech
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-race-results
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WP_RACE_RESULTS_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-race-results-activator.php
 */
function activate_wp_race_results()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-race-results-activator.php';
    WP_Race_Results_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-race-results-deactivator.php
 */
function deactivate_wp_race_results()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-race-results-deactivator.php';
    WP_Race_Results_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_race_results');
register_deactivation_hook(__FILE__, 'deactivate_wp_race_results');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
// require plugin_dir_path( __FILE__ ) . 'includes/class-wp-race-results.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_race_results()
{

    // $plugin = new WP_Race_Results();
    // $plugin->run();

    // Initialize Admin Menu
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-race-results-admin.php';
    $plugin_admin = new WP_Race_Results_Admin('wp-race-results', WP_RACE_RESULTS_VERSION);
    $plugin_admin->init();

    // Require Standalone CSV Import Handler (if it exists)
    if (file_exists(plugin_dir_path(__FILE__) . 'includes/csv-import-handler.php')) {
        require_once plugin_dir_path(__FILE__) . 'includes/csv-import-handler.php';
    }
}

/**
 * Load Elementor support.
 *
 * Checks if Elementor is loaded or waits for it to load.
 */
if (did_action('elementor/loaded')) {
    require_once plugin_dir_path(__FILE__) . 'elementor/elementor-init.php';
} else {
    add_action('elementor/loaded', function () {
        require_once plugin_dir_path(__FILE__) . 'elementor/elementor-init.php';
    });
}
/**
 * Helper: Get Declared Winners count for a specific category
 * Source of Truth: Event Admin > Winner Declaration settings
 *
 * @param int $event_id
 * @param string $distance
 * @param string $gender
 * @return int
 */
function wprr_get_declared_winners($event_id, $distance, $gender)
{
    global $wpdb;
    $table_winner_rules = $wpdb->prefix . 'race_winner_rules';

    $declared = $wpdb->get_var($wpdb->prepare(
        "SELECT declared_winners FROM $table_winner_rules 
         WHERE event_id = %d AND REPLACE(distance, ' ', '') = %s AND LOWER(gender) = %s",
        $event_id,
        str_replace(' ', '', $distance),
        strtolower($gender)
    ));

    if ($declared === null) {
        error_log("[WPRR] No specific declared winners rule found for Event $event_id, Distance '$distance', Gender '$gender'. Falling back to 3.");
    }

    return $declared !== null ? (int) $declared : 3;
}

/**
 * Enqueue Public Assets
 */
add_action('wp_enqueue_scripts', function () {
    // Style - Global Enqueue
    wp_enqueue_style('wprr-public-style', plugin_dir_url(__FILE__) . 'assets/css/wp-race-results-public.css', [], WP_RACE_RESULTS_VERSION);

    // Foundation JS - Global Enqueue in Footer
    wp_enqueue_script(
        'wprr-winners-modal',
        plugin_dir_url(__FILE__) . 'assets/js/wprr-winners-modal.js',
        [],
        WP_RACE_RESULTS_VERSION,
        true
    );

    // Prepare for AJAX (Phase 3.2 logic)
    wp_localize_script('wprr-winners-modal', 'wprr_ajax', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
});

run_wp_race_results();

