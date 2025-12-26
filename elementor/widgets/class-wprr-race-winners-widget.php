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
        $event_name = WPRR_DB::get_event_name($event_id);

        // 1. Get Declared Winners Rules (Authoritative Source)
        $declared_male = WPRR_DB::get_declared_winners($event_id, $distance, 'male');
        $declared_female = WPRR_DB::get_declared_winners($event_id, $distance, 'female');

        // 2. Fetch PODIUM ONLY for widget card display (top 3)
        $podium_male = WPRR_DB::get_race_results($event_id, $distance, 'Male', 3);
        $podium_female = WPRR_DB::get_race_results($event_id, $distance, 'Female', 3);

        $highlight_count = absint($settings['highlight_count']);

        // Logic for Button
        $remaining_male = max(0, $declared_male - $highlight_count);
        $remaining_female = max(0, $declared_female - $highlight_count);

        $widget_id = $this->get_id();
        $modal_id_male = "wprr-modal-{$widget_id}-male";
        $modal_id_female = "wprr-modal-{$widget_id}-female";

        // DEBUG LOGS (For Admin/Development)
        if (current_user_can('manage_options')) {
            echo "<!-- WPRR DEBUG: \n";
            echo "Category: " . esc_html($distance) . "\n";
            echo "Male: Podium " . count($podium_male) . " (Declared: $declared_male, Remaining: $remaining_male)\n";
            echo "Female: Podium " . count($podium_female) . " (Declared: $declared_female, Remaining: $remaining_female)\n";
            echo "-->";
        }

        ?>
        <div class="wprr-winners-container" style="border: 1px solid #eee; padding: 20px;">
            <!-- DATA SOURCE: DECLARED WINNERS
                 DECLARED MALE: <?php echo $declared_male; ?> (Podium: <?php echo count($podium_male); ?>)
                 REMAINING MALE: <?php echo $remaining_male; ?>
                 DECLARED FEMALE: <?php echo $declared_female; ?> (Podium: <?php echo count($podium_female); ?>)
                 REMAINING FEMALE: <?php echo $remaining_female; ?>
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

        <?php
        $modal_args_male = [
            'id' => $modal_id_male,
            'event_id' => $event_id,
            'distance' => $distance,
            'gender' => 'Male',
            'event_name' => $event_name,
            'declared' => $declared_male
        ];
        $modal_args_female = [
            'id' => $modal_id_female,
            'event_id' => $event_id,
            'distance' => $distance,
            'gender' => 'Female',
            'event_name' => $event_name,
            'declared' => $declared_female
        ];

        WPRR_Modal_Renderer::render_category_modal($modal_args_male);
        WPRR_Modal_Renderer::render_category_modal($modal_args_female);
    }
}
