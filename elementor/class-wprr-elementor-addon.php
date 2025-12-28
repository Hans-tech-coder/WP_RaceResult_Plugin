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
        return WPRR_DB::get_events_distances_map();
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
        require_once(__DIR__ . '/widgets/class-wprr-event-header-widget.php');

        $widgets_manager->register(new \WPRR_Events_Grid_Widget());
        $widgets_manager->register(new \WPRR_Race_Winners_Widget());
        $widgets_manager->register(new \WPRR_Event_Header_Widget());
    }
}
