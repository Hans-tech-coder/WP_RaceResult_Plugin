<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('\Elementor\Widget_Base')) {
    return;
}

class WPRR_Events_Grid_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'wprr_events_grid';
    }

    public function get_title()
    {
        return 'Race Events Grid';
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['wp-race-results'];
    }

    protected function _register_controls()
    {

        // --- Content Tab: Query ---
        $this->start_controls_section(
            'section_query',
            [
                'label' => 'Query',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'number_of_events',
            [
                'label' => 'Number of Events',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => 'Order By',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'event_date',
                'options' => [
                    'event_date' => 'Event Date',
                    'created_at' => 'Date Created',
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => 'Order',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => 'ASC',
                    'DESC' => 'DESC',
                ],
            ]
        );

        $this->end_controls_section();

        // --- Content Tab: Layout ---
        $this->start_controls_section(
            'section_layout',
            [
                'label' => 'Layout',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => 'Columns',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 3,
                'options' => [
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-events-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label' => 'Gap',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-events-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- Style Tab: Card ---
        $this->start_controls_section(
            'section_style_card',
            [
                'label' => 'Card',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background_color',
            [
                'label' => 'Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .wprr-event-card',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .wprr-event-card',
            ]
        );

        $this->end_controls_section();

        // --- Style Tab: Image ---
        $this->start_controls_section(
            'section_style_image',
            [
                'label' => 'Image',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-card img, {{WRAPPER}} .wprr-event-card .wprr-no-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_bottom_spacing',
            [
                'label' => 'Bottom Spacing',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-card img, {{WRAPPER}} .wprr-event-card .wprr-no-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => 'Height',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 150,
                ],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-card img, {{WRAPPER}} .wprr-event-card .wprr-no-image' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- Style Tab: Title ---
        $this->start_controls_section(
            'section_style_title',
            [
                'label' => 'Title',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .wprr-event-card h3',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-card h3' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_bottom_spacing',
            [
                'label' => 'Bottom Spacing',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-card h3' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- Style Tab: Meta ---
        $this->start_controls_section(
            'section_style_meta',
            [
                'label' => 'Meta Text',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'selector' => '{{WRAPPER}} .wprr-event-meta, {{WRAPPER}} .wprr-event-meta div',
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-meta, {{WRAPPER}} .wprr-event-meta div' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_spacing',
            [
                'label' => 'Spacing Between Items',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-meta div:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- Style Tab: Distance Badges ---
        $this->start_controls_section(
            'section_style_distances',
            [
                'label' => 'Distance Badges',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'distance_typography',
                'selector' => '{{WRAPPER}} .wprr-distance-badge',
            ]
        );

        $this->add_control(
            'distance_text_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-distance-badge' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'distance_bg_color',
            [
                'label' => 'Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wprr-distance-badge' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'distance_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-distance-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'distance_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-distance-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'distance_badges_spacing',
            [
                'label' => 'Gap Between Badges',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-distances' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'distance_container_spacing',
            [
                'label' => 'Top Spacing',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-distances' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $limit = absint($settings['number_of_events']);
        $order_by = $settings['order_by'];
        $order = $settings['order'];

        $results = WPRR_DB::get_events($limit, $order_by, $order);

        if (!$results) {
            echo '<div>No events found.</div>';
            return;
        }

        // CSS Grid applied via Controls selectors
        echo '<div class="wprr-events-grid" style="display: grid;">';

        foreach ($results as $event) {

            // Card style applied via Controls selectors
            echo '<div class="wprr-event-card">';

            if (!empty($event->banner_image)) {
                echo '<img src="' . esc_url($event->banner_image) . '" alt="' . esc_attr($event->event_name) . '" style="width: 100%; object-fit: cover;">';
            } else {
                echo '<div class="wprr-no-image" style="width: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999;">No Image</div>';
            }

            echo '<h3>' . esc_html($event->event_name) . '</h3>';

            echo '<div class="wprr-event-meta">';
            $date_formatted = $event->event_date ? date_i18n(get_option('date_format'), strtotime($event->event_date)) : 'TBA';
            echo '<div><strong>Date:</strong> ' . esc_html($date_formatted) . '</div>';

            if (!empty($event->location)) {
                echo '<div><strong>Location:</strong> ' . esc_html($event->location) . '</div>';
            }
            echo '</div>'; // .wprr-event-meta

            if (!empty($event->distance_categories)) {
                $distances = array_map('trim', explode(',', $event->distance_categories));
                if (!empty($distances)) {
                    echo '<div class="wprr-event-distances" style="display: flex; flex-wrap: wrap;">';
                    foreach ($distances as $distance) {
                        if (!empty($distance)) {
                            echo '<span class="wprr-distance-badge" style="display: inline-block;">' . esc_html($distance) . '</span>';
                        }
                    }
                    echo '</div>';
                }
            }

            echo '</div>'; // .wprr-event-card
        }

        echo '</div>'; // .wprr-events-grid
    }

}
