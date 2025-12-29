<?php
if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class WPRR_Race_Winners_Widget extends Widget_Base
{

    public function get_name()
    {
        return 'wprr_race_winners';
    }

    public function get_title()
    {
        return 'Race Winners Highlight';
    }

    public function get_icon()
    {
        return 'eicon-trophy';
    }

    public function get_categories()
    {
        return ['wp-race-results'];
    }

    /**
     * Get all race events for the dropdown.
     *
     * @return array
     */
    protected function get_event_options()
    {
        $options = ['' => 'Select Event'];
        $events = WPRR_DB::get_event_options();
        if ($events) {
            foreach ($events as $id => $name) {
                $options[$id] = $name;
            }
        }

        return $options;
    }

    /**
     * Get all available distances for the dropdown.
     *
     * @return array
     */
    protected function get_distance_options()
    {
        $options = ['' => 'Select Distance'];
        $distances = WPRR_DB::get_all_distance_options();

        if ($distances) {
            foreach ($distances as $distance) {
                $options[$distance] = $distance;
            }
        }

        // Fallback/Common defaults if DB is empty
        if (count($options) <= 1) {
            $options = array_merge($options, [
                '5KM' => '5KM',
                '10KM' => '10KM',
                '21KM' => '21KM',
                '42KM' => '42KM'
            ]);
        }

        return $options;
    }

    protected function _register_controls()
    {

        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => 'Settings',
            ]
        );

        $this->add_control(
            'event_id',
            [
                'label' => 'Select Event',
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_event_options(),
                'default' => '',
            ]
        );

        $this->add_control(
            'distance',
            [
                'label' => 'Select Distance',
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_distance_options(),
                'default' => '',
            ]
        );

        $this->add_control(
            'highlight_count',
            [
                'label' => 'Highlight Count',
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'default' => 3,
                'description' => 'Number of winners to highlight in the widget card.',
            ]
        );

        $this->end_controls_section();

        // Style Section: Section Container
        $this->start_controls_section(
            'section_style_container',
            [
                'label' => 'Container',
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'container_background',
                'label' => 'Background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .wprr-winners-container',
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => 'Padding',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-winners-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'container_border_radius',
            [
                'label' => 'Border Radius',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-winners-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section: Header
        $this->start_controls_section(
            'section_style_header',
            [
                'label' => 'Header',
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'header_typography',
                'label' => 'Typography',
                'selector' => '{{WRAPPER}} .wprr-winners-header h3',
            ]
        );

        $this->add_control(
            'header_text_color',
            [
                'label' => 'Text Color',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-winners-header h3' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'view_results_btn_color',
            [
                'label' => 'Header Button Color',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-view-results-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'view_results_btn_hover_color',
            [
                'label' => 'Header Button Hover Color',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-view-results-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section: Cards
        $this->start_controls_section(
            'section_style_cards',
            [
                'label' => 'Cards',
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background_color',
            [
                'label' => 'Background Color',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-winner-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_title_color',
            [
                'label' => 'Title Color',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-winner-card h4' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'card_title_typography',
                'label' => 'Title Typography',
                'selector' => '{{WRAPPER}} .wprr-winner-card h4',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'card_list_typography',
                'label' => 'List Typography',
                'selector' => '{{WRAPPER}} .wprr-winners-list li, {{WRAPPER}} .wprr-winners-list li span',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .wprr-winner-card',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => 'Border Radius',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-winner-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => 'Padding',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-winner-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_spacing',
            [
                'label' => 'Spacing Between Cards',
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-winners-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section: Buttons
        $this->start_controls_section(
            'section_style_buttons',
            [
                'label' => 'Action Buttons',
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => 'Typography',
                'selector' => '{{WRAPPER}} .wprr-view-more-btn',
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => 'Text Color',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-view-more-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => 'Background Color',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-view-more-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => 'Hover Text Color',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-view-more-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => 'Hover Background Color',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-view-more-btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => 'Padding',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-view-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => 10,
                    'right' => 20,
                    'bottom' => 10,
                    'left' => 20,
                    'unit' => 'px',
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => 'Border Radius',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-view-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        // Hide widget when viewing full results table
        if (!empty(get_query_var('wprr_distance'))) {
            return;
        }

        $settings = $this->get_settings_for_display();
        $event = null;
        $event_id = 0;
        $event_name = '';
        $distances = [];

        // Step 1: Detect Event - Check URL params first, then fallback to manual settings
        $event_slug = get_query_var('event_slug');
        if (!empty($event_slug)) {
            $event = WPRR_DB::get_event_by_slug($event_slug);
        } elseif (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
            $event = WPRR_DB::get_event_by_id(absint($_GET['event_id']));
        }

        // If event found from URL, use it; otherwise use manual settings
        if ($event) {
            $event_id = absint($event->id);
            $event_name = $event->event_name;

            // Step 2: Dynamic Mode - Get all distances from event
            if (!empty($event->distance_categories)) {
                $distances = array_map('trim', explode(',', $event->distance_categories));
                $distances = array_filter($distances); // Remove empty values
            }
        } else {
            // Step 2: Manual Mode - Use settings
            $event_id = !empty($settings['event_id']) ? absint($settings['event_id']) : 0;
            $distance = !empty($settings['distance']) ? $settings['distance'] : '';

            if ($event_id) {
                $event = WPRR_DB::get_event_by_id($event_id);
            }

            if (empty($event_id) || !$event) {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    echo '<div style="padding: 20px; background: #eee; text-align: center;">Please select an Event to display winners.</div>';
                }
                return;
            }
            $event_name = $event->event_name;

            if (empty($distance)) {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    echo '<div style="padding: 20px; background: #eee; text-align: center;">Please select a Distance to display winners.</div>';
                }
                return;
            }

            // Use single distance as array for loop
            $distances = [$distance];
        }

        // Sort distances in descending order (longest to shortest)
        if (count($distances) > 1) {
            usort($distances, function ($a, $b) {
                // Remove non-numeric characters (except decimal points) to get raw number
                $val_a = (float) filter_var($a, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $val_b = (float) filter_var($b, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                // Sort Descending (Longest to Shortest)
                return ($val_a < $val_b) ? 1 : -1;
            });
        }

        // If no distances found, show message
        if (empty($distances)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding: 20px; background: #eee; text-align: center;">No distance categories found for this event.</div>';
            }
            return;
        }

        $highlight_count = absint($settings['highlight_count']);
        $widget_id = $this->get_id();
        $modal_args_collection = []; // Collect modals to render at the end

        // Step 3: Loop through distances
        foreach ($distances as $loop_index => $distance) {
            $distance = trim($distance);
            if (empty($distance)) {
                continue;
            }

            // Sanitize distance for use in IDs and URL
            $distance_slug = sanitize_title($distance);

            // --- URL Generation for "View Results" button ---
            $permalink_base = get_option('wprr_permalink_base', 'race');
            $button_url = home_url('/' . $permalink_base . '/' . $event->slug . '/' . $distance_slug . '/');

            // 1. Get Declared Winners Rules (Authoritative Source)
            $declared_male = WPRR_DB::get_declared_winners($event_id, $distance, 'male');
            $declared_female = WPRR_DB::get_declared_winners($event_id, $distance, 'female');

            // 2. Fetch PODIUM ONLY for widget card display (top 3)
            $podium_male = WPRR_DB::get_race_results($event_id, $distance, 'Male', 3);
            $podium_female = WPRR_DB::get_race_results($event_id, $distance, 'Female', 3);

            // Logic for Button
            $remaining_male = max(0, $declared_male - $highlight_count);
            $remaining_female = max(0, $declared_female - $highlight_count);

            // Unique modal IDs per distance
            $modal_id_male = "wprr-modal-{$widget_id}-{$distance_slug}-male";
            $modal_id_female = "wprr-modal-{$widget_id}-{$distance_slug}-female";

            // DEBUG LOGS (For Admin/Development)
            if (current_user_can('manage_options')) {
                echo "<!-- WPRR DEBUG: \n";
                echo "Category: " . esc_html($distance) . "\n";
                echo "Male: Podium " . count($podium_male) . " (Declared: $declared_male, Remaining: $remaining_male)\n";
                echo "Female: Podium " . count($podium_female) . " (Declared: $declared_female, Remaining: $remaining_female)\n";
                echo "-->";
            }

            ?>
            <style>
                /* Grid Layout */
                .wprr-winners-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                }

                /* Header Layout */
                .wprr-winners-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                }

                /* Mobile Responsive Overrides */
                @media (max-width: 767px) {

                    /* Stack Cards */
                    .wprr-winners-grid {
                        grid-template-columns: 1fr !important;
                    }

                    /* Center Header & Stack Button */
                    .wprr-winners-header {
                        flex-direction: column;
                        gap: 15px;
                        text-align: center;
                        justify-content: center;
                    }

                    /* Ensure button doesn't stretch weirdly */
                    .wprr-view-results-btn {
                        display: inline-block;
                        width: auto;
                    }
                }
            </style>

            <div class="wprr-distance-section wprr-winners-container"
                style="border: 1px solid #eee; padding: 20px; margin-bottom: 30px;">
                <!-- DATA SOURCE: DECLARED WINNERS
                     DECLARED MALE: <?php echo $declared_male; ?> (Podium: <?php echo count($podium_male); ?>)
                     REMAINING MALE: <?php echo $remaining_male; ?>
                     DECLARED FEMALE: <?php echo $declared_female; ?> (Podium: <?php echo count($podium_female); ?>)
                     REMAINING FEMALE: <?php echo $remaining_female; ?>
                -->

                <!-- Header -->
                <div class="wprr-winners-header">
                    <h3 style="margin: 0;"><?php echo esc_html($distance); ?> Winners</h3>
                    <?php
                    $button_key = 'btn_' . $loop_index;
                    $this->remove_render_attribute($button_key);
                    $this->add_render_attribute($button_key, 'href', $button_url);
                    $this->add_render_attribute($button_key, 'class', 'wprr-view-results-btn');
                    $this->add_render_attribute($button_key, 'style', 'text-decoration: none; font-weight: bold;');

                    echo '<a ' . $this->get_render_attribute_string($button_key) . '>View ' . esc_html($distance) . ' Results &rarr;</a>';
                    ?>
                </div>

                <!-- Cards Grid -->
                <div class="wprr-winners-grid">

                    <!-- Male Card -->
                    <div class="wprr-winner-card" style="display: flex; flex-direction: column;">
                        <h4 style="margin-top: 0; margin-bottom: 15px;"><?php echo esc_html($distance); ?> - Male</h4>

                        <?php if ($podium_male): ?>
                            <ul class="wprr-winners-list" style="list-style: none; padding: 0; margin: 0 0 25px 0;">
                                <?php foreach ($podium_male as $index => $winner): ?>
                                    <li
                                        style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 8px;">
                                        <span>
                                            <?php
                                            $rank = isset($winner->rank_gender) && $winner->rank_gender > 0 ? $winner->rank_gender : ($index + 1);
                                            $medals = ['🥇', '🥈', '🥉'];
                                            echo isset($medals[$rank - 1]) ? $medals[$rank - 1] : $rank . '.';
                                            ?>
                                            <?php echo esc_html($winner->full_name); ?>
                                        </span>
                                        <span style="opacity: 0.6;">#<?php echo esc_html($winner->bib_number); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p style="opacity: 0.6;">No winners found.</p>
                        <?php endif; ?>

                        <?php if ($remaining_male > 0): ?>
                            <a href="javascript:void(0);" class="wprr-view-more-btn"
                                data-wprr-modal="<?php echo esc_attr($modal_id_male); ?>"
                                style="margin-top: auto; display: block; text-align: center; text-decoration: none;">
                                <?php echo sprintf('View %d More Winners', $remaining_male); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Female Card -->
                    <div class="wprr-winner-card" style="display: flex; flex-direction: column;">
                        <h4 style="margin-top: 0; margin-bottom: 15px;"><?php echo esc_html($distance); ?> - Female</h4>

                        <?php if ($podium_female): ?>
                            <ul class="wprr-winners-list" style="list-style: none; padding: 0; margin: 0 0 25px 0;">
                                <?php foreach ($podium_female as $index => $winner): ?>
                                    <li
                                        style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 8px;">
                                        <span>
                                            <?php
                                            $rank = isset($winner->rank_gender) && $winner->rank_gender > 0 ? $winner->rank_gender : ($index + 1);
                                            $medals = ['🥇', '🥈', '🥉'];
                                            echo isset($medals[$rank - 1]) ? $medals[$rank - 1] : $rank . '.';
                                            ?>
                                            <?php echo esc_html($winner->full_name); ?>
                                        </span>
                                        <span style="opacity: 0.6;">#<?php echo esc_html($winner->bib_number); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p style="opacity: 0.6;">No winners found.</p>
                        <?php endif; ?>

                        <?php if ($remaining_female > 0): ?>
                            <a href="javascript:void(0);" class="wprr-view-more-btn"
                                data-wprr-modal="<?php echo esc_attr($modal_id_female); ?>"
                                style="margin-top: auto; display: block; text-align: center; text-decoration: none;">
                                <?php echo sprintf('View %d More Winners', $remaining_female); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                </div> <!-- .wprr-winners-grid -->
            </div> <!-- .wprr-distance-section .wprr-winners-container -->

            <?php
            // Collect modal arguments for rendering at the end
            $modal_args_collection[] = [
                'id' => $modal_id_male,
                'event_id' => $event_id,
                'distance' => $distance,
                'gender' => 'Male',
                'event_name' => $event_name,
                'declared' => $declared_male
            ];
            $modal_args_collection[] = [
                'id' => $modal_id_female,
                'event_id' => $event_id,
                'distance' => $distance,
                'gender' => 'Female',
                'event_name' => $event_name,
                'declared' => $declared_female
            ];
        }

        // Render all modals at the end
        foreach ($modal_args_collection as $modal_args) {
            WPRR_Modal_Renderer::render_category_modal($modal_args);
        }
    }
}
