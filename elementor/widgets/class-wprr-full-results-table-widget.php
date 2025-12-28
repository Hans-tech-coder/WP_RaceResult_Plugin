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

        // Step 1: Determine Active Distance
        // Prioritize GET parameter (user switch) over URL path (initial load)
        $path_distance = get_query_var('wprr_distance');
        $get_distance = isset($_GET['wprr_distance']) ? sanitize_text_field($_GET['wprr_distance']) : '';
        $distance = !empty($get_distance) ? $get_distance : $path_distance;

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

        // Step 2: DB Matching
        // Fetch all available distances for the event
        $available_distances = WPRR_DB::get_distances_for_event($event_id);

        // Match "Active Distance" (slug or raw) to the correct Database Value
        $target_distance = $distance; // Default fallback
        if (!empty($available_distances)) {
            foreach ($available_distances as $db_dist) {
                if (sanitize_title($db_dist) === sanitize_title($distance)) {
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

        // Construct Clean Base URL
        $perm_base = get_option('wprr_permalink_base', 'results');

        // Initialize base URL for JS redirect
        $event_base_url = '';

        // If we are on a pretty URL (which we should be if wprr_distance is set via rewrite rules)
        if (!empty($event_slug)) {
            // We use the NEW $distance (which might be from GET) to construct the base URL
            // This effectively "redirects" the form action to the pretty URL of the selected distance
            $slug_distance = sanitize_title($distance);
            $base_url = home_url('/' . $perm_base . '/' . $event_slug . '/' . $slug_distance . '/');
            // Logic for View Winners Button (Event Overview): /results/event-slug/
            $winners_url = home_url('/' . $perm_base . '/' . $event_slug . '/');
            // Assign event_base_url for JS redirect in dropdown
            $event_base_url = $winners_url;
        } else {
            // Fallback for non-pretty URLs
            $base_url = add_query_arg([
                'event_id' => $event_id,
                'wprr_distance' => $distance
            ], home_url('/'));
            $winners_url = add_query_arg('event_id', $event_id, home_url('/'));
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

            <style>
                .wprr-filter-container {
                    margin-bottom: 20px;
                    border: 1px solid #eee;
                    padding: 15px;
                    border-radius: 8px;
                    background: #fafafa;
                }

                .wprr-filter-top-row {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    flex-wrap: wrap;
                }

                .wprr-filter-category-group {
                    flex: 0 0 auto;
                }

                .wprr-filter-search-group {
                    flex: 1;
                    display: flex;
                    gap: 5px;
                    min-width: 250px;
                }

                .wprr-filter-search-group input {
                    flex: 1;
                    padding: 8px 12px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }

                .wprr-view-winners-btn {
                    margin-left: auto;
                    padding: 8px 20px;
                    background: #333;
                    color: #fff;
                    border-radius: 4px;
                    text-decoration: none;
                    font-weight: bold;
                    font-size: 14px;
                    border: 1px solid #333;
                }

                .wprr-view-winners-btn:hover {
                    background: #555;
                    color: #fff;
                }

                .wprr-additional-filters-toggle {
                    margin-top: 10px;
                    font-size: 13px;
                    color: #0073aa;
                    cursor: pointer;
                    display: inline-block;
                }

                .wprr-additional-filters {
                    margin-top: 15px;
                    padding-top: 15px;
                    border-top: 1px solid #eee;
                    display: none;
                    /* Hidden by default */
                    gap: 15px;
                    align-items: flex-end;
                }

                .wprr-additional-filters.active {
                    display: flex;
                }

                .wprr-form-group label {
                    display: block;
                    font-weight: bold;
                    margin-bottom: 5px;
                    font-size: 13px;
                }

                .wprr-form-group select {
                    min-width: 150px;
                    padding: 6px;
                }
            </style>

            <!-- Filter FORM Wrapper -->
            <!-- Form Action links to the Current Page (Results for Active Distance) 
                 This ensures Search and Gender filters work on the current distance. -->
            <form method="get" action="<?php echo esc_url($base_url); ?>" class="wprr-results-filters">

                <?php
                // Only add event_id hidden field if NOT using pretty URLs
                // This prevents redundant "event_id" param in pretty URL mode
                if (empty($event_slug)): ?>
                    <input type="hidden" name="event_id" value="<?php echo esc_attr($event_id); ?>">
                <?php endif; ?>

                <!-- Explicitly reset page to 1 on new filter -->
                <input type="hidden" name="wprr_page" value="1">

                <div class="wprr-filter-container">

                    <!-- Section 1: Top Bar -->
                    <div class="wprr-filter-top-row">

                        <!-- Category (Distance) -->
                        <div class="wprr-filter-category-group">
                            <?php
                            // Logic for Dropdown Name and JS Redirect
                            $select_name = 'wprr_distance'; // Default name for form submit
                            $onchange_logic = "this.form.submit()"; // Default Logic
                    
                            if (!empty($event_base_url)) {
                                // Pretty URL Mode:
                                // 1. JS Redirect to clean URL
                                $onchange_logic = "window.location.href = '" . esc_url($event_base_url) . "' + this.value + '/';";
                                // 2. Remove 'name' attribute so searching doesn't add 'wprr_distance=...' to URL
                                $select_name = '';
                            }
                            ?>
                            <select <?php echo $select_name ? 'name="' . esc_attr($select_name) . '"' : ''; ?>
                                onchange="<?php echo $onchange_logic; ?>"
                                style="padding: 8px; border-radius: 4px; border: 1px solid #ddd; font-weight: bold;">
                                <?php if (!empty($available_distances)): ?>
                                    <?php foreach ($available_distances as $dist): ?>
                                        <option value="<?php echo esc_attr(sanitize_title($dist)); ?>" <?php selected(sanitize_title($dist), sanitize_title($distance)); ?>>
                                            <?php echo esc_html($dist); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="<?php echo esc_attr($distance); ?>"><?php echo esc_html($distance); ?></option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Search Bar -->
                        <div class="wprr-filter-search-group">
                            <input type="text" name="search" value="<?php echo esc_attr($search); ?>"
                                placeholder="Search Name or Bib...">
                            <button type="submit"
                                style="padding: 8px 15px; background: #0073aa; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Search</button>
                        </div>

                        <!-- View Winners Button -->
                        <a href="<?php echo esc_url($winners_url); ?>" class="wprr-view-winners-btn">View Winners</a>
                    </div>

                    <!-- Toggle Link -->
                    <div class="wprr-additional-filters-toggle"
                        onclick="document.getElementById('wprr-additional-filters').classList.toggle('active');">
                        Additional Filters ^
                    </div>

                    <!-- Section 2: Additional Filters -->
                    <div id="wprr-additional-filters"
                        class="wprr-additional-filters <?php echo (!empty($gender)) ? 'active' : ''; ?>">
                        <div class="wprr-form-group">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="" <?php selected($gender, ''); ?>>All Genders</option>
                                <option value="Male" <?php selected($gender, 'Male'); ?>>Male</option>
                                <option value="Female" <?php selected($gender, 'Female'); ?>>Female</option>
                            </select>
                        </div>

                        <div class="wprr-form-group">
                            <button type="submit"
                                style="padding: 6px 15px; background: #666; color: white; border: none; border-radius: 4px;">Filter</button>
                        </div>

                        <?php if (!empty($search) || !empty($gender)): ?>
                            <div class="wprr-form-group">
                                <a href="<?php echo esc_url($base_url); ?>"
                                    style="color: #999; text-decoration: underline; font-size: 13px;">Reset Filters</a>
                            </div>
                        <?php endif; ?>
                    </div>

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
