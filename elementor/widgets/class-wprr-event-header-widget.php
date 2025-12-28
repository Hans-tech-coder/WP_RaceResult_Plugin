<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('\Elementor\Widget_Base')) {
    return;
}

class WPRR_Event_Header_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'wprr_event_header';
    }

    public function get_title()
    {
        return 'Event Dynamic Header';
    }

    public function get_icon()
    {
        return 'eicon-banner';
    }

    public function get_categories()
    {
        return ['wp-race-results'];
    }

    protected function _register_controls()
    {
        // --- Style Tab: Banner Container ---
        $this->start_controls_section(
            'section_style_banner',
            [
                'label' => 'Banner Container',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'banner_height',
            [
                'label' => 'Height',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 400,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'vh'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-banner' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'banner_overlay_color',
            [
                'label' => 'Overlay Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.3)',
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'banner_content_alignment',
            [
                'label' => 'Content Alignment',
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'center' => [
                        'title' => 'Center',
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-start' => [
                        'title' => 'Left',
                        'icon' => 'eicon-text-align-left',
                    ],
                    'flex-end' => [
                        'title' => 'Bottom Left',
                        'icon' => 'eicon-text-align-left',
                    ],
                ],
                'default' => 'center',
                'prefix_class' => 'wprr-header-align-',
            ]
        );

        $this->add_responsive_control(
            'banner_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- Style Tab: Logo ---
        $this->start_controls_section(
            'section_style_logo',
            [
                'label' => 'Logo',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'logo_max_width',
            [
                'label' => 'Max Width',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 200,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 800,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-logo img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'logo_margin',
            [
                'label' => 'Margin',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'logo_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-logo img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'logo_box_shadow',
                'selector' => '{{WRAPPER}} .wprr-event-header-logo img',
            ]
        );

        $this->end_controls_section();

        // --- Style Tab: Social Icons ---
        $this->start_controls_section(
            'section_style_social',
            [
                'label' => 'Social Icons',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'social_icon_size',
            [
                'label' => 'Icon Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 24,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-social a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'social_icon_color',
            [
                'label' => 'Icon Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-social a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'social_icon_hover_color',
            [
                'label' => 'Icon Hover Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0073aa',
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-social a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'social_icon_spacing',
            [
                'label' => 'Spacing Between Icons',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-social a:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'social_icon_margin',
            [
                'label' => 'Margin',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-social' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $event = null;

        // Detection Logic: First check query_var, then GET parameter
        $event_slug = get_query_var('event_slug');
        if (!empty($event_slug)) {
            $event = WPRR_DB::get_event_by_slug($event_slug);
        } elseif (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
            $event = WPRR_DB::get_event_by_id(absint($_GET['event_id']));
        }

        // Show placeholder in editor if no event found
        if (!$event) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="wprr-event-header-placeholder" style="padding: 40px; text-align: center; background: #f0f0f0; border: 2px dashed #ccc; border-radius: 4px;">';
                echo '<p style="margin: 0; color: #666;">Event Dynamic Header</p>';
                echo '<p style="margin: 10px 0 0; font-size: 12px; color: #999;">No event found. This widget will display event branding when an event is selected via slug or event_id parameter.</p>';
                echo '</div>';
            }
            return;
        }

        // Parse social media links
        $social_links = [];
        if (!empty($event->social_media_links)) {
            $decoded = json_decode($event->social_media_links, true);
            if (is_array($decoded)) {
                $social_links = $decoded;
            }
        }

        // Banner image URL
        $banner_url = !empty($event->banner_image) ? esc_url($event->banner_image) : '';
        $banner_style = $banner_url ? 'background-image: url(' . $banner_url . ');' : 'background-color: #333;';

        // Get alignment class
        $alignment = isset($settings['banner_content_alignment']) ? $settings['banner_content_alignment'] : 'center';
        $alignment_class = 'wprr-header-align-' . esc_attr($alignment);

        // Render banner container with overlay
        $overlay_color = isset($settings['banner_overlay_color']) ? $settings['banner_overlay_color'] : 'rgba(0, 0, 0, 0.3)';
        echo '<div class="wprr-event-header-banner" style="' . $banner_style . ' background-size: cover; background-position: center; position: relative; display: flex;">';
        echo '<div class="wprr-event-header-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: ' . esc_attr($overlay_color) . '; z-index: 1;"></div>';
        
        // Content container
        echo '<div class="wprr-event-header-content ' . $alignment_class . '" style="position: relative; z-index: 2; width: 100%; display: flex; flex-direction: column; justify-content: ' . esc_attr($alignment === 'flex-end' ? 'flex-end' : ($alignment === 'flex-start' ? 'flex-start' : 'center')) . '; align-items: ' . esc_attr($alignment === 'flex-end' ? 'flex-start' : ($alignment === 'flex-start' ? 'flex-start' : 'center')) . ';">';

        // Logo
        if (!empty($event->event_logo)) {
            echo '<div class="wprr-event-header-logo">';
            echo '<img src="' . esc_url($event->event_logo) . '" alt="' . esc_attr($event->event_name) . ' Logo" style="max-width: 100%; height: auto;">';
            echo '</div>';
        }

        // Social Media Links
        if (!empty($social_links)) {
            echo '<div class="wprr-event-header-social" style="display: flex; align-items: center;">';
            
            if (!empty($social_links['facebook'])) {
                echo '<a href="' . esc_url($social_links['facebook']) . '" target="_blank" rel="noopener noreferrer" aria-label="Facebook">';
                echo '<i class="fab fa-facebook"></i>';
                echo '</a>';
            }
            
            if (!empty($social_links['instagram'])) {
                echo '<a href="' . esc_url($social_links['instagram']) . '" target="_blank" rel="noopener noreferrer" aria-label="Instagram">';
                echo '<i class="fab fa-instagram"></i>';
                echo '</a>';
            }
            
            if (!empty($social_links['website'])) {
                echo '<a href="' . esc_url($social_links['website']) . '" target="_blank" rel="noopener noreferrer" aria-label="Website">';
                echo '<i class="fas fa-globe"></i>';
                echo '</a>';
            }
            
            echo '</div>';
        }

        echo '</div>'; // .wprr-event-header-content
        echo '</div>'; // .wprr-event-header-banner
    }
}
