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

    /**
     * Force load Font Awesome assets.
     */
    public function get_style_depends()
    {
        return ['elementor-icons-fa-solid', 'elementor-icons-fa-regular', 'elementor-icons-fa-brands'];
    }

    protected function _register_controls()
    {
        // --- Style Tab: Banner Container ---
        $this->start_controls_section(
            'section_style_banner',
            [
                'label' => 'Banner Background',
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
            'banner_bg_position',
            [
                'label' => 'Position',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'center center',
                'options' => [
                    'default' => 'Default',
                    'top left' => 'Top Left',
                    'top center' => 'Top Center',
                    'top right' => 'Top Right',
                    'center left' => 'Center Left',
                    'center center' => 'Center Center',
                    'center right' => 'Center Right',
                    'bottom left' => 'Bottom Left',
                    'bottom center' => 'Bottom Center',
                    'bottom right' => 'Bottom Right',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-banner' => 'background-position: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'banner_bg_attachment',
            [
                'label' => 'Attachment',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'scroll',
                'options' => [
                    'default' => 'Default',
                    'scroll' => 'Scroll',
                    'fixed' => 'Fixed',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-banner' => 'background-attachment: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'banner_bg_repeat',
            [
                'label' => 'Repeat',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'no-repeat',
                'options' => [
                    'default' => 'Default',
                    'no-repeat' => 'No-repeat',
                    'repeat' => 'Repeat',
                    'repeat-x' => 'Repeat-x',
                    'repeat-y' => 'Repeat-y',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-banner' => 'background-repeat: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'banner_bg_size',
            [
                'label' => 'Display Size',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'default' => 'Default',
                    'auto' => 'Auto',
                    'cover' => 'Cover',
                    'contain' => 'Contain',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-banner' => 'background-size: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'banner_overlay_color',
            [
                'label' => 'Background Overlay',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0)',
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- Style Tab: Content Box (The Card) ---
        $this->start_controls_section(
            'section_style_content_box',
            [
                'label' => 'Content Box',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'content_box_width',
            [
                'label' => 'Max Width',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'default' => [
                    'unit' => '%',
                    'size' => 80,
                ],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-content-box' => 'max-width: {{SIZE}}{{UNIT}}; width: 100%;',
                ],
            ]
        );

        $this->add_control(
            'content_box_bg_color',
            [
                'label' => 'Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.6)',
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-content-box' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_box_blur',
            [
                'label' => 'Background Blur',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-content-box' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}); backdrop-filter: blur({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_box_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-content-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'content_box_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-content-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'label' => 'Width',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 120,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-event-header-logo img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
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

        $this->end_controls_section();

        // --- Style Tab: Typography (Title & Meta) ---
        $this->start_controls_section(
            'section_style_typography',
            [
                'label' => 'Title & Details',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_heading',
            [
                'label' => 'Event Title',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Title Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .wprr-header-title',
            ]
        );

        $this->add_control(
            'meta_heading',
            [
                'label' => 'Meta Details (Date/Loc)',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-meta' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-header-meta i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-header-meta svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'selector' => '{{WRAPPER}} .wprr-header-meta',
            ]
        );

        // --- NEW: Icon Size Control ---
        $this->add_responsive_control(
            'meta_icon_size',
            [
                'label' => 'Icon Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 16,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-meta i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wprr-header-meta svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
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
                    'size' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-social a' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wprr-header-social i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wprr-header-social svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .wprr-header-social a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-header-social i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-header-social svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'social_icon_hover_color',
            [
                'label' => 'Icon Hover Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#cccccc',
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-social a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-header-social a:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wprr-header-social a:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'social_icon_spacing',
            [
                'label' => 'Gap',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wprr-header-social' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $event = null;

        // Detection Logic
        $event_slug = get_query_var('event_slug');
        if (!empty($event_slug)) {
            $event = WPRR_DB::get_event_by_slug($event_slug);
        } elseif (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
            $event = WPRR_DB::get_event_by_id(absint($_GET['event_id']));
        }

        // Editor Placeholder
        if (!$event) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding: 40px; text-align: center; background: #eee;">';
                echo '<p><strong>Event Header Widget</strong></p>';
                echo '<p>No event detected. Previewing with placeholder data.</p>';
                // Mock Object for Preview
                $event = new stdClass();
                $event->event_name = 'Sample Event Name';
                $event->event_date = date('Y-m-d');
                $event->location = 'Sample City, Country';
                $event->event_logo = 'https://via.placeholder.com/150';
                $event->banner_image = '';
                $event->social_media_links = '';
            } else {
                return;
            }
        }

        // Data Prep
        $banner_url = !empty($event->banner_image) ? esc_url($event->banner_image) : '';
        // NOTE: We do NOT add background-size/position here anymore.
        // It is handled by CSS Selectors in _register_controls.
        $banner_style = $banner_url ? 'background-image: url(' . $banner_url . ');' : 'background-color: #333;';

        $social_links = !empty($event->social_media_links) ? json_decode($event->social_media_links, true) : [];

        // Render
        ?>
        <div class="wprr-event-header-banner"
            style="<?php echo $banner_style; ?> position: relative; display: flex; align-items: center; justify-content: center;">
            <div class="wprr-event-header-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></div>

            <div class="wprr-header-content-box"
                style="position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center; text-align: center;">

                <?php if (!empty($event->event_logo)): ?>
                    <div class="wprr-event-header-logo">
                        <img src="<?php echo esc_url($event->event_logo); ?>" alt="Event Logo">
                    </div>
                <?php endif; ?>

                <h1 class="wprr-header-title" style="margin: 0; padding: 0; line-height: 1.2;">
                    <?php echo esc_html($event->event_name); ?>
                </h1>

                <div class="wprr-header-meta" style="margin-top: 10px; display: flex; flex-direction: column; gap: 5px;">
                    <?php if (!empty($event->event_date)): ?>
                        <div class="wprr-meta-item" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <?php
                            \Elementor\Icons_Manager::render_icon(
                                ['value' => 'far fa-calendar-alt', 'library' => 'fa-regular'],
                                ['aria-hidden' => 'true']
                            );
                            ?>
                            <?php echo date_i18n(get_option('date_format'), strtotime($event->event_date)); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($event->location)): ?>
                        <div class="wprr-meta-item" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <?php
                            \Elementor\Icons_Manager::render_icon(
                                ['value' => 'fas fa-map-marker-alt', 'library' => 'fa-solid'],
                                ['aria-hidden' => 'true']
                            );
                            ?>
                            <?php echo esc_html($event->location); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($social_links)): ?>
                    <div class="wprr-header-social" style="display: flex; justify-content: center; margin-top: 15px;">

                        <?php if (!empty($social_links['facebook'])): ?>
                            <a href="<?php echo esc_url($social_links['facebook']); ?>" target="_blank">
                                <?php
                                \Elementor\Icons_Manager::render_icon(
                                    ['value' => 'fab fa-facebook-square', 'library' => 'fa-brands'],
                                    ['aria-hidden' => 'true']
                                );
                                ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($social_links['instagram'])): ?>
                            <a href="<?php echo esc_url($social_links['instagram']); ?>" target="_blank">
                                <?php
                                \Elementor\Icons_Manager::render_icon(
                                    ['value' => 'fab fa-instagram', 'library' => 'fa-brands'],
                                    ['aria-hidden' => 'true']
                                );
                                ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($social_links['website'])): ?>
                            <a href="<?php echo esc_url($social_links['website']); ?>" target="_blank">
                                <?php
                                \Elementor\Icons_Manager::render_icon(
                                    ['value' => 'fas fa-globe', 'library' => 'fa-solid'],
                                    ['aria-hidden' => 'true']
                                );
                                ?>
                            </a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </div>
        </div>
        <?php
    }
}
