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

        // --- Action Column Content Section ---
        $this->start_controls_section(
            'section_action_content',
            [
                'label' => 'Action Column',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'action_header_text',
            [
                'label' => 'Header Text',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Action',
            ]
        );

        $this->add_control(
            'action_icon',
            [
                'label' => 'Icon',
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-chart-bar',
                    'library' => 'fa-solid',
                ],
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

        // --- Action Column Style Section ---
        $this->start_controls_section(
            'section_style_action',
            [
                'label' => 'Action Column',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'action_icon_color',
            [
                'label' => 'Icon Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .wprr-analysis-link' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-analysis-link i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-analysis-link svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-analysis-link svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'action_icon_hover_color',
            [
                'label' => 'Hover Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0073aa',
                'selectors' => [
                    '{{WRAPPER}} .wprr-analysis-link:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-analysis-link:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-analysis-link:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-analysis-link:hover svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'action_icon_size',
            [
                'label' => 'Icon Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-analysis-link i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wprr-analysis-link svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        // Check if we are viewing a specific entry analysis
        if (!empty(get_query_var('wprr_entry_id'))) {
            return;
        }

        $settings = $this->get_settings_for_display();

        // 1. DATA FETCHING LOGIC
        $path_distance = get_query_var('wprr_distance');
        $get_distance = isset($_GET['wprr_distance']) ? sanitize_text_field($_GET['wprr_distance']) : '';
        $distance = !empty($get_distance) ? $get_distance : $path_distance;

        if (empty($distance)) {
            return;
        }

        $event_slug = get_query_var('event_slug');
        $event = null;
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
        $available_distances = WPRR_DB::get_distances_for_event($event_id);

        $target_distance = $distance;
        if (!empty($available_distances)) {
            foreach ($available_distances as $db_dist) {
                if (sanitize_title($db_dist) === sanitize_title($distance)) {
                    $target_distance = $db_dist;
                    break;
                }
            }
        }

        $page = isset($_GET['wprr_page']) ? max(1, absint($_GET['wprr_page'])) : 1;
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $gender = isset($_GET['gender']) ? sanitize_text_field($_GET['gender']) : '';
        $limit = max(1, absint($settings['rows_per_page']));
        $offset = ($page - 1) * $limit;

        $results = WPRR_DB::get_results_for_table($event_id, $target_distance, $gender, $search, $limit, $offset);
        $total_results = WPRR_DB::count_results_for_table($event_id, $target_distance, $gender, $search);
        $total_pages = ceil($total_results / $limit);

        $perm_base = get_option('wprr_permalink_base', 'results');
        $event_base_url = '';
        if (!empty($event_slug)) {
            $slug_distance = sanitize_title($distance);
            $base_url = home_url('/' . $perm_base . '/' . $event_slug . '/' . $slug_distance . '/');
            $winners_url = home_url('/' . $perm_base . '/' . $event_slug . '/');
            $event_base_url = $winners_url;
        } else {
            $base_url = add_query_arg(['event_id' => $event_id, 'wprr_distance' => $distance], home_url('/'));
            $winners_url = add_query_arg('event_id', $event_id, home_url('/'));
        }

        $striped_class = ($settings['table_striped_rows'] === 'yes') ? 'wprr-table-striped' : '';

        // 2. STYLES (Desktop & Mobile)
        ?>
        <style>
            .wprr-desktop-view {
                display: block;
            }

            .wprr-mobile-view {
                display: none;
            }

            /* Filter Category Group & Winners Button */
            .wprr-filter-category-group {
                display: flex;
                gap: 10px;
                align-items: center;
                flex: 0 1 auto;
                width: auto;
            }

            .wprr-view-winners-btn {
                padding: 8px 15px;
                background: #333;
                color: #fff !important;
                border-radius: 4px;
                text-decoration: none;
                font-weight: 700;
                font-size: 13px;
                white-space: nowrap;
                border: 1px solid #333;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background 0.3s;
                height: 40px;
                box-sizing: border-box;
            }

            .wprr-view-winners-btn:hover {
                background: #555;
                border-color: #555;
                color: #fff;
            }

            /* Mobile Card Styles */
            .wprr-result-card {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                margin: 35px 5px 25px;
                overflow: visible;
                position: relative;
                padding-top: 25px;
                border: 1px solid #f0f0f0;
            }

            .wprr-card-rank {
                position: absolute;
                top: -20px;
                left: 50%;
                transform: translateX(-50%);
                width: 44px;
                height: 44px;
                background: #222;
                color: #fff;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                font-size: 16px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                z-index: 10;
                border: 3px solid #fff;
            }

            .wprr-card-info {
                text-align: center;
                padding: 10px 15px 15px;
            }

            .wprr-card-name {
                font-size: 20px;
                font-weight: 800;
                color: #222;
                margin-bottom: 4px;
                line-height: 1.2;
            }

            .wprr-card-bib {
                font-size: 13px;
                color: #888;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .wprr-time-grid {
                display: flex;
                border-top: 1px solid #f0f0f0;
            }

            .wprr-time-col {
                flex: 1;
                padding: 15px 10px;
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .wprr-time-col.dark {
                background: #222;
                color: #fff;
            }

            .wprr-time-col.light {
                background: #e8e8e8;
                color: #333;
            }

            .wprr-time-label {
                display: block;
                font-size: 9px;
                text-transform: uppercase;
                opacity: 0.8;
                margin-top: 4px;
                font-weight: 700;
                letter-spacing: 0.5px;
            }

            .wprr-time-val {
                display: block;
                font-size: 18px;
                font-weight: 800;
                line-height: 1;
            }

            .wprr-card-action {
                padding: 15px;
                text-align: center;
                background: #fcfcfc;
            }

            .wprr-card-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: #333;
                color: #fff;
                padding: 10px 24px;
                border-radius: 8px;
                text-decoration: none;
                font-size: 13px;
                text-transform: uppercase;
                font-weight: 700;
                transition: all 0.2s ease;
                width: 100%;
            }

            .wprr-card-btn:hover {
                background: #000;
                color: #fff;
            }

            /* Table & Pagination Refinements */
            .wprr-results-table th {
                background: #f4f4f4;
                padding: 12px 15px;
                border-bottom: 2px solid #ddd;
            }

            .wprr-results-table td {
                padding: 12px 15px;
                border-bottom: 1px solid #eee;
            }

            .wprr-table-striped tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .wprr-pagination {
                margin-top: 30px;
                display: flex;
                justify-content: center;
                gap: 8px;
                align-items: center;
            }

            .wprr-pagination .page-numbers {
                padding: 8px 16px;
                border: 1px solid #ddd;
                text-decoration: none;
                color: #333;
                border-radius: 6px;
                font-weight: 600;
            }

            .wprr-pagination .page-numbers.current {
                background-color: #333;
                color: #fff;
                border-color: #333;
            }

            @media (max-width: 767px) {
                .wprr-desktop-view {
                    display: none;
                }

                .wprr-mobile-view {
                    display: block;
                }

                .wprr-results-header h2 {
                    font-size: 22px;
                    text-align: center;
                }

                .wprr-filter-form {
                    flex-direction: column;
                    align-items: stretch !important;
                }

                /* Mobile Responsive Filters */
                .wprr-filter-top-row {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 15px;
                }

                .wprr-filter-category-group {
                    width: 100% !important;
                    display: flex;
                    gap: 10px;
                }

                /* Force Equal 50/50 Split on Mobile */
                .wprr-filter-category-group select,
                .wprr-view-winners-btn {
                    flex: 1;
                    width: 50%;
                    max-width: 50%;
                    box-sizing: border-box;
                    height: 40px;
                }

                .wprr-filter-category-group select {
                    width: 50% !important;
                }
            }
        </style>

        <div class="wprr-full-results-container">

            <!-- Component 1: Category Switcher -->
            <div class="wprr-results-header wprr-filter-top-row"
                style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <h2 style="margin: 0;"><?php echo esc_html($event_name); ?> - <?php echo esc_html($target_distance); ?></h2>

                <div class="wprr-filter-category-group">
                    <?php
                    // Logic for Dropdown Name and JS Redirect
                    $select_name = 'wprr_distance';
                    $onchange_logic = "this.form.submit()";

                    if (!empty($event_base_url)) {
                        $onchange_logic = "window.location.href = '" . esc_url($event_base_url) . "' + this.value + '/';";
                        $select_name = '';
                    }
                    ?>

                    <select id="wprr-distance-select" <?php echo $select_name ? 'name="' . esc_attr($select_name) . '"' : ''; ?>
                        onchange="<?php echo $onchange_logic; ?>"
                        style="padding: 0 10px; border-radius: 4px; border: 1px solid #ddd; font-weight: bold; min-width: 120px; height: 40px; line-height: 40px;">
                        <?php if (!empty($available_distances)): ?>
                            <?php foreach ($available_distances as $dist): ?>
                                <option value="<?php echo esc_attr(sanitize_title($dist)); ?>" <?php selected(sanitize_title($dist), sanitize_title($target_distance)); ?>>
                                    <?php echo esc_html($dist); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="<?php echo esc_attr($distance); ?>"><?php echo esc_html($distance); ?></option>
                        <?php endif; ?>
                    </select>

                    <a href="<?php echo esc_url($winners_url); ?>" class="wprr-view-winners-btn" title="View Winners">
                        <i class="fas fa-trophy" style="margin-right: 6px;"></i> View Winners
                    </a>
                </div>
            </div>

            <!-- Component 2: Filter Form (Search & Gender) -->
            <div class="wprr-filter-container"
                style="margin-bottom: 30px; padding: 20px; border-radius: 12px; background: #f9f9f9; border: 1px solid #eee;">
                <form id="wprr-filter-form" method="get" action="<?php echo esc_url($base_url); ?>" class="wprr-filter-form"
                    style="display: flex; gap: 12px; width: 100%; align-items: center; flex-wrap: wrap;">

                    <input type="hidden" name="event_id" value="<?php echo esc_attr($event_id); ?>">
                    <input type="hidden" name="wprr_page" value="1">

                    <div style="flex: 1; min-width: 200px;">
                        <input type="text" name="search" placeholder="Search runner or bib..."
                            value="<?php echo esc_attr($search); ?>"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    </div>

                    <div style="min-width: 150px;">
                        <select name="gender"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; background: #fff;">
                            <option value="">All Genders</option>
                            <option value="Male" <?php selected($gender, 'Male'); ?>>Male</option>
                            <option value="Female" <?php selected($gender, 'Female'); ?>>Female</option>
                        </select>
                    </div>

                    <button type="submit"
                        style="padding: 10px 25px; background: #333; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; text-transform: uppercase; font-size: 13px;">
                        Filter Results
                    </button>

                    <?php if (!empty($search) || !empty($gender)): ?>
                        <a href="<?php echo esc_url($base_url); ?>"
                            style="font-size: 13px; color: #888; text-decoration: underline;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Component 3: Results Views (Desktop vs Mobile) -->
            <div id="wprr-results-data-container" data-settings='<?php echo wp_json_encode($settings); ?>'>
                <?php echo WPRR_Table_Renderer::render_html($results, $base_url, $page, $total_pages, $search, $gender, $settings); ?>
            </div>
        </div>
        <?php
    }

}
