<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('\Elementor\Widget_Base')) {
    return;
}

class WPRR_Full_Results_Table_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'wprr_full_results_table';
    }

    public function get_title()
    {
        return 'Full Results Table';
    }

    public function get_icon()
    {
        return 'eicon-table';
    }

    public function get_categories()
    {
        return ['wp-race-results'];
    }

    protected function _register_controls()
    {
        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => 'Settings',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'rows_per_page',
            [
                'label' => 'Rows Per Page',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 20,
                'min' => 5,
                'max' => 100,
                'step' => 5,
            ]
        );

        $this->end_controls_section();

        // Style Section: Table
        $this->start_controls_section(
            'section_style_table',
            [
                'label' => 'Table',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'table_striped_rows',
            [
                'label' => 'Striped Rows',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'table_header_bg_color',
            [
                'label' => 'Header Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f0f0f0',
                'selectors' => [
                    '{{WRAPPER}} .wprr-results-table thead th' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'table_header_text_color',
            [
                'label' => 'Header Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333',
                'selectors' => [
                    '{{WRAPPER}} .wprr-results-table thead th' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'table_typography',
                'selector' => '{{WRAPPER}} .wprr-results-table',
            ]
        );

        $this->add_responsive_control(
            'table_cell_padding',
            [
                'label' => 'Cell Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-results-table td, {{WRAPPER}} .wprr-results-table th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'table_border_color',
            [
                'label' => 'Border Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ddd',
                'selectors' => [
                    '{{WRAPPER}} .wprr-results-table' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-results-table th, {{WRAPPER}} .wprr-results-table td' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section: Pagination
        $this->start_controls_section(
            'section_style_pagination',
            [
                'label' => 'Pagination',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'pagination_typography',
                'selector' => '{{WRAPPER}} .wprr-pagination a',
            ]
        );

        $this->add_control(
            'pagination_text_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0073aa',
                'selectors' => [
                    '{{WRAPPER}} .wprr-pagination a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_color',
            [
                'label' => 'Hover Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#005a87',
                'selectors' => [
                    '{{WRAPPER}} .wprr-pagination a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_disabled_color',
            [
                'label' => 'Disabled Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ccc',
                'selectors' => [
                    '{{WRAPPER}} .wprr-pagination a.disabled' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Visibility Check: Only render if wprr_distance query var is set
        $distance = get_query_var('wprr_distance');
        if (empty($distance)) {
            return;
        }

        // Get event from URL
        $event_slug = get_query_var('event_slug');
        $event = null;
        $event_id = 0;
        $event_name = '';

        if (!empty($event_slug)) {
            $event = WPRR_DB::get_event_by_slug($event_slug);
        } elseif (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
            $event = WPRR_DB::get_event_by_id(absint($_GET['event_id']));
        }

        if (!$event) {
            echo '<div class="wprr-results-error">Event not found.</div>';
            return;
        }

        $event_id = absint($event->id);
        $event_name = $event->event_name;

        // MATCHING LOGIC: Match URL slug to DB stored value
        $target_distance = $distance; // Default fallback
        $available_distances = WPRR_DB::get_distances_for_event($event_id);

        if (!empty($available_distances)) {
            foreach ($available_distances as $db_dist) {
                if (sanitize_title($db_dist) === $distance) {
                    $target_distance = $db_dist;
                    break;
                }
            }
        }

        // Get pagination and filter parameters
        $page = isset($_GET['wprr_page']) ? max(1, absint($_GET['wprr_page'])) : 1;
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $gender = isset($_GET['gender']) ? sanitize_text_field($_GET['gender']) : '';
        $limit = max(1, absint($settings['rows_per_page']));
        $offset = ($page - 1) * $limit;

        // Fetch results using matched target_distance
        $results = WPRR_DB::get_results_for_table($event_id, $target_distance, $gender, $search, $limit, $offset);
        $total_results = WPRR_DB::count_results_for_table($event_id, $target_distance, $gender, $search);
        $total_pages = ceil($total_results / $limit);

        // Construct Clean Base URL for Pagination & Filtering
        $perm_base = get_option('wprr_permalink_base', 'results');

        // If we are on a pretty URL (which we should be if wprr_distance is set via rewrite rules)
        if (!empty($event_slug) && !empty($distance)) {
            // Trailing slash is important for pretty structure
            $base_url = home_url('/' . $perm_base . '/' . $event_slug . '/' . $distance . '/');
        } else {
            // Fallback for non-pretty URLs
            $base_url = add_query_arg([
                'event_id' => $event_id,
                'wprr_distance' => $distance
            ], home_url('/'));
        }

        // Determine striped rows class
        $striped_class = ($settings['table_striped_rows'] === 'yes') ? 'wprr-table-striped' : '';

        ?>
        <div class="wprr-full-results-container">
            <!-- Header -->
            <div class="wprr-results-header" style="margin-bottom: 20px;">
                <h2 style="margin: 0 0 10px 0;">Results for <?php echo esc_html($target_distance); ?> -
                    <?php echo esc_html($event_name); ?></h2>
            </div>

            <!-- Filter Form -->
            <form method="get" action="<?php echo esc_url($base_url); ?>" class="wprr-results-filters"
                style="margin-bottom: 20px; display: flex; gap: 10px; align-items: flex-end;">

                <?php if (empty($event_slug)): // Only add hidden fields if NOT using pretty URLs ?>
                    <input type="hidden" name="event_id" value="<?php echo esc_attr($event_id); ?>">
                    <input type="hidden" name="wprr_distance" value="<?php echo esc_attr($distance); ?>">
                <?php endif; ?>

                <!-- Explicitly reset page to 1 on new filter -->
                <input type="hidden" name="wprr_page" value="1">

                <div style="flex: 1;">
                    <label for="wprr-search" style="display: block; margin-bottom: 5px; font-weight: bold;">Search
                        (Name/Bib):</label>
                    <input type="text" id="wprr-search" name="search" value="<?php echo esc_attr($search); ?>"
                        placeholder="Search by name or bib number..."
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div style="flex: 0 0 150px;">
                    <label for="wprr-gender" style="display: block; margin-bottom: 5px; font-weight: bold;">Gender:</label>
                    <select id="wprr-gender" name="gender"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="" <?php selected($gender, ''); ?>>All</option>
                        <option value="Male" <?php selected($gender, 'Male'); ?>>Male</option>
                        <option value="Female" <?php selected($gender, 'Female'); ?>>Female</option>
                    </select>
                </div>

                <div>
                    <button type="submit"
                        style="padding: 8px 20px; background: #0073aa; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Filter</button>
                    <?php if (!empty($search) || !empty($gender)): ?>
                        <a href="<?php echo esc_url($base_url); ?>"
                            style="margin-left: 10px; padding: 8px 20px; display: inline-block; background: #ccc; color: #333; text-decoration: none; border-radius: 4px;">Reset</a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Results Table -->
            <?php if (!empty($results)): ?>
                <div class="wprr-results-table-wrapper" style="overflow-x: auto;">
                    <table class="wprr-results-table <?php echo esc_attr($striped_class); ?>"
                        style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                        <thead>
                            <tr>
                                <th style="text-align: left; font-weight: bold;">Rank</th>
                                <th style="text-align: left; font-weight: bold;">Bib</th>
                                <th style="text-align: left; font-weight: bold;">Name</th>
                                <th style="text-align: left; font-weight: bold;">Gender</th>
                                <th style="text-align: left; font-weight: bold;">Chip Time</th>
                                <th style="text-align: left; font-weight: bold;">Gun Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $index => $result): ?>
                                <tr>
                                    <td><?php echo esc_html($result->rank_overall); ?></td>
                                    <td><?php echo esc_html($result->bib_number); ?></td>
                                    <td><?php echo esc_html($result->full_name); ?></td>
                                    <td><?php echo esc_html($result->gender); ?></td>
                                    <td><?php echo esc_html($result->chip_time); ?></td>
                                    <td><?php echo esc_html($result->gun_time); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="wprr-pagination"
                        style="margin-top: 20px; display: flex; justify-content: center; gap: 10px; align-items: center;">
                        <?php
                        // Pagination Args
                        $pagination_args = [
                            'base' => $base_url . '%_%',
                            'format' => '?wprr_page=%#%',
                            'current' => $page,
                            'total' => $total_pages,
                            'prev_text' => '&larr; Prev',
                            'next_text' => 'Next &rarr;',
                            'type' => 'plain',
                            'add_args' => array_filter([
                                'search' => $search,
                                'gender' => $gender,
                            ]),
                        ];

                        echo paginate_links($pagination_args);
                        ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="wprr-results-empty"
                    style="padding: 40px; text-align: center; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                    <p style="margin: 0; color: #666;">No results
                        found<?php echo !empty($search) || !empty($gender) ? ' matching your filters' : ''; ?>.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php
        // Add CSS for striped rows if enabled
        if ($settings['table_striped_rows'] === 'yes') {
            echo '<style>
                .wprr-table-striped tbody tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .wprr-pagination .page-numbers {
                    padding: 8px 12px;
                    border: 1px solid #ddd;
                    margin: 0 4px;
                    text-decoration: none;
                    color: inherit;
                    border-radius: 4px;
                }
                .wprr-pagination .page-numbers.current {
                    background-color: #f0f0f0;
                    font-weight: bold;
                    pointer-events: none;
                }
                .wprr-pagination a.page-numbers:hover {
                    background-color: #eee;
                }
            </style>';
        }
    }
}
