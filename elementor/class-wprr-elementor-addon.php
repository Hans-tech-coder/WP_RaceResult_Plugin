<?php
class WPRR_Elementor_Addon
{

    public function __construct()
    {
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/elements/categories_registered', [$this, 'register_category']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);
    }

    public function enqueue_editor_scripts()
    {
        wp_enqueue_script(
            'wprr-elementor-editor',
            plugin_dir_url(__FILE__) . '../assets/js/wprr-elementor-editor.js',
            ['jquery', 'elementor-editor'], // Dependency on Elementor Editor
            '1.0.0',
            true
        );

        // Localize data for the script
        $data = [
            'events_distances' => $this->get_events_distances(),
        ];
        wp_localize_script('wprr-elementor-editor', 'wprr_editor_data', $data);
    }

    /**
     * Get a map of Event ID => Distance Categories
     * 
     * @return array
     */
    private function get_events_distances()
    {
        global $wpdb;
        $table_events = $wpdb->prefix . 'race_events';

        // Check if table exists to avoid errors
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_events'") != $table_events) {
            return [];
        }

        $results = $wpdb->get_results("SELECT id, distance_categories FROM $table_events");

        $map = [];
        if ($results) {
            foreach ($results as $event) {
                // Return raw string; JS will split it
                $map[$event->id] = $event->distance_categories;
            }
        }
        return $map;
    }

    public function register_category($elements_manager)
    {
        if (!method_exists($elements_manager, 'add_category')) {
            return;
        }

        $elements_manager->add_category(
            'wp-race-results',
            [
                'title' => 'WP Race Results',
                'icon' => 'eicon-flag',
            ]
        );
    }

    public function register_widgets($widgets_manager)
    {
        require_once(__DIR__ . '/widgets/class-wprr-events-grid-widget.php');
        require_once(__DIR__ . '/widgets/class-wprr-race-winners-widget.php');

        $widgets_manager->register(new \WPRR_Events_Grid_Widget());
        $widgets_manager->register(new \WPRR_Race_Winners_Widget());
    }
}
