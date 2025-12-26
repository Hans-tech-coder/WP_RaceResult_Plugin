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
        global $wpdb;
        $table_events = $wpdb->prefix . 'race_events';

        // Check if table exists to avoid errors during initial setup
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_events'") != $table_events) {
            return [];
        }

        $results = $wpdb->get_results("SELECT id, event_name FROM $table_events ORDER BY created_at DESC");

        $options = ['' => 'Select Event'];
        if ($results) {
            foreach ($results as $event) {
                $options[$event->id] = $event->event_name;
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
        global $wpdb;
        $table_results = $wpdb->prefix . 'race_results';

        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_results'") != $table_results) {
            return [];
        }

        // We fetch distinct distances from results to ensure we have valid data options
        $results = $wpdb->get_col("SELECT DISTINCT distance FROM $table_results ORDER BY distance ASC");

        $options = ['' => 'Select Distance'];
        if ($results) {
            foreach ($results as $distance) {
                // Formatting distance label if needed, or just use raw value
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
        $settings = $this->get_settings_for_display();
        $event_id = $settings['event_id'];
        $distance = $settings['distance'];
        $highlight_limit = 3; // Fixed Podium Limit - Always show only top 3 in highlight

        if (empty($event_id)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding: 20px; background: #eee; text-align: center;">Please select an Event to display winners.</div>';
            }
            return;
        }

        if (empty($distance)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding: 20px; background: #eee; text-align: center;">Please select a Distance to display winners.</div>';
            }
            return;
        }

        // Get Event Name for Modal Subtitle
        global $wpdb;
        $table_events = $wpdb->prefix . 'race_events';
        $event_name = $wpdb->get_var($wpdb->prepare("SELECT event_name FROM $table_events WHERE id = %d", $event_id));

        // 1. Get Declared Winners Rules (Authoritative Source)
        $declared_male = wprr_get_declared_winners($event_id, $distance, 'male');
        $declared_female = wprr_get_declared_winners($event_id, $distance, 'female');

        // 2. Fetch winners from DB (for display/modal)
        $table_results = $wpdb->prefix . 'race_results';

        // Male Data
        $results_male = $wpdb->get_results($wpdb->prepare(
            "SELECT rank_overall, rank_gender, bib_number, full_name, chip_time 
            FROM $table_results 
            WHERE event_id = %d 
            AND REPLACE(distance, ' ', '') = %s 
            AND LOWER(gender) IN ('m','male') 
            ORDER BY rank_overall ASC, chip_time ASC",
            $event_id,
            str_replace(' ', '', $distance)
        ));

        // Female Data
        $results_female = $wpdb->get_results($wpdb->prepare(
            "SELECT rank_overall, rank_gender, bib_number, full_name, chip_time 
            FROM $table_results 
            WHERE event_id = %d 
            AND REPLACE(distance, ' ', '') = %s 
            AND LOWER(gender) IN ('f','female') 
            ORDER BY rank_overall ASC, chip_time ASC",
            $event_id,
            str_replace(' ', '', $distance)
        ));

        // 3. Logic for Button & Podium
        $podium_male = array_slice($results_male, 0, 3);
        $podium_female = array_slice($results_female, 0, 3);

        // AUTHORITATIVE BUTTON LOGIC (Requested)
        $remaining_male = max(0, $declared_male - 3);
        $remaining_female = max(0, $declared_female - 3);

        // MANDATORY DEBUG LOGS
        error_log("WPRR DECLARED: male={$declared_male}, remaining={$remaining_male}");
        error_log("WPRR DECLARED: female={$declared_female}, remaining={$remaining_female}");

        $widget_id = $this->get_id();
        $modal_id_male = "wprr-modal-{$widget_id}-male";
        $modal_id_female = "wprr-modal-{$widget_id}-female";

        // DEBUG LOGS (For Admin/Development)
        if (current_user_can('manage_options')) {
            echo "<!-- WPRR DEBUG: \n";
            echo "Category: " . esc_html($distance) . "\n";
            echo "Male: Fetched " . $total_male . " (Remaining: $remaining_male)\n";
            echo "Female: Fetched " . $total_female . " (Remaining: $remaining_female)\n";
            echo "-->";
        }

        ?>
        <div class="wprr-winners-container" style="border: 1px solid #eee; padding: 20px;">
            <!-- DATA SOURCE: DECLARED WINNERS
                 DECLARED MALE: <?php echo $declared_male; ?> (DB Count: <?php echo count($results_male); ?>)
                 REMAINING MALE: <?php echo $remaining_male; ?>
                 DECLARED FEMALE: <?php echo $declared_female; ?> (DB Count: <?php echo count($results_female); ?>)
                 REMAINING FEMALE: <?php echo $remaining_female; ?>
                 PODIUM MALE: <?php echo count($podium_male); ?>
                 PODIUM FEMALE: <?php echo count($podium_female); ?>
            -->

            <!-- Header -->
            <div class="wprr-winners-header"
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0;"><?php echo esc_html($distance); ?> Winners</h3>
                <a href="#" class="wprr-view-results-btn" style="text-decoration: none; font-weight: bold;">View
                    <?php echo esc_html($distance); ?> Results &rarr;</a>
            </div>

            <!-- Cards Grid -->
            <div class="wprr-winners-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

                <!-- Male Card -->
                <div class="wprr-winner-card"
                    style="background: #f9f9f9; padding: 20px; border-radius: 8px; display: flex; flex-direction: column;">
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
                            style="margin-top: auto; display: block; text-align: center; padding: 10px; background: #333; color: #fff; text-decoration: none; border-radius: 4px;">
                            <?php echo sprintf('View %d More Winners', $remaining_male); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Female Card -->
                <div class="wprr-winner-card"
                    style="background: #f9f9f9; padding: 20px; border-radius: 8px; display: flex; flex-direction: column;">
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
                            style="margin-top: auto; display: block; text-align: center; padding: 10px; background: #333; color: #fff; text-decoration: none; border-radius: 4px;">
                            <?php echo sprintf('View %d More Winners', $remaining_female); ?>
                        </a>
                    <?php endif; ?>
                </div>

            </div> <!-- .wprr-winners-grid -->
        </div> <!-- .wprr-winners-container -->

        <!-- Modals (Server-rendered) -->
        <?php
        $this->render_category_modal($modal_id_male, $distance, 'Male', $event_name, $results_male);
        $this->render_category_modal($modal_id_female, $distance, 'Female', $event_name, $results_female);
    }

    /**
     * Helper to render a winner modal
     */
    protected function render_category_modal($id, $distance, $gender, $event_name, $winners)
    {
        ?>
        <div id="<?php echo esc_attr($id); ?>" class="wprr-modal" aria-hidden="true">
            <div class="wprr-modal-overlay"></div>
            <div class="wprr-modal-content" role="dialog" aria-modal="true">
                <div class="wprr-modal-header">
                    <h2><?php echo esc_html($distance); ?> - <?php echo esc_html($gender); ?></h2>
                    <button class="wprr-modal-close" aria-label="Close modal">&times;</button>
                </div>
                <div class="wprr-modal-body">
                    <div style="margin-bottom: 20px;">
                        <h3 style="margin: 0 0 5px 0;">All Winners</h3>
                        <p style="margin: 0; opacity: 0.7;"><?php echo esc_html($event_name); ?></p>
                    </div>
                    <?php if ($winners): ?>
                        <table class="wprr-modal-table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Bib</th>
                                    <th>Name</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($winners as $index => $w): ?>
                                    <?php
                                    $rank = isset($w->rank_gender) && $w->rank_gender > 0 ? $w->rank_gender : ($index + 1);
                                    $medals = ['🥇', '🥈', '🥉'];
                                    $rank_display = isset($medals[$rank - 1]) ? $medals[$rank - 1] : $rank . '.';
                                    ?>
                                    <tr>
                                        <td><?php echo $rank_display; ?></td>
                                        <td>#<?php echo esc_html($w->bib_number); ?></td>
                                        <td><?php echo esc_html($w->full_name); ?></td>
                                        <td><?php echo esc_html($w->chip_time); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="text-align:center; padding: 20px;">No winners found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
