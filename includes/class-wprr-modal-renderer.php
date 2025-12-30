<?php
/**
 * WPRR_Modal_Renderer
 * 
 * Handles server-side rendering of modals for the WP Race Results plugin.
 */

if (!defined('WPINC')) {
    die;
}

class WPRR_Modal_Renderer
{
    /**
     * Render a winner category modal
     * 
     * @param array $args {
     *     @type string $id         The modal HTML ID.
     *     @type string $distance   Race distance.
     *     @type string $gender     Race gender (Male/Female).
     *     @type string $event_name Name of the race event.
     *     @type array  $winners    Array of winner objects.
     * }
     */
    public static function render_category_modal($args)
    {
        $id = isset($args['id']) ? $args['id'] : '';
        $event_id = isset($args['event_id']) ? absint($args['event_id']) : 0;
        $distance = isset($args['distance']) ? $args['distance'] : '';
        $gender = isset($args['gender']) ? $args['gender'] : '';
        $declared = isset($args['declared']) ? absint($args['declared']) : 0;

        // 1. Fetch Data
        $winners = WPRR_DB::get_race_results($event_id, $distance, $gender, $declared);
        $event = WPRR_DB::get_event_by_id($event_id);

        // 2. Fetch Global Styles
        $header_bg = get_option('wprr_modal_header_bg', '#7A2e66');
        $text_color = get_option('wprr_modal_text_color', '#ffffff');

        $banner_url = ($event && !empty($event->banner_image)) ? $event->banner_image : '';
        $event_name = ($event) ? $event->event_name : '';
        $count = count($winners);

        ?>
        <div id="<?php echo esc_attr($id); ?>" class="wprr-modal" aria-hidden="true">
            <div class="wprr-modal-overlay"></div>
            <div class="wprr-modal-content" role="dialog" aria-modal="true">

                <style>
                    /* Main Container */
                    #<?php echo esc_attr($id); ?> .wprr-modal-content {
                        padding: 0;
                        border-radius: 12px;
                        overflow: hidden;
                        display: flex;
                        flex-direction: column;
                        width: 95%;
                        max-width: 640px;
                        margin: auto;
                        max-height: 95vh;
                        background: #fff;
                        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.35);
                        /* Fix for Safari border-radius clipping */
                        transform: translateZ(0);
                    }

                    /* Top Bar - FIXED BORDERS & EDGES */
                    #<?php echo esc_attr($id); ?> .wprr-modal-top-bar {
                        background-color:
                            <?php echo esc_attr($header_bg); ?>
                        ;
                        color:
                            <?php echo esc_attr($text_color); ?>
                        ;
                        /* Use vertical padding to define height, let flex align items */
                        padding: 16px 24px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        /* Critical for vertical alignment */
                        flex-shrink: 0;

                        /* RADIUS: Top rounded, Bottom Flat */
                        border-radius: 12px 12px 0 0;

                        /* FIX WHITE EDGES: Pull header slightly over the container background */
                        margin: -1px -1px 0 -1px;
                        width: calc(100% + 2px);
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-title {
                        font-size: 18px;
                        font-weight: 700;
                        margin: 0;
                        color: inherit;
                        line-height: 1.2;
                    }

                    /* Close Button - PERFECT ALIGNMENT */
                    #<?php echo esc_attr($id); ?> .wprr-modal-close-btn {
                        background: transparent;
                        border: none;
                        color: inherit;
                        padding: 0;
                        width: 32px;
                        height: 32px;
                        font-size: 28px;
                        /* Reset default button styles */
                        margin: 0;
                        line-height: 1;
                        cursor: pointer;

                        /* Flex Center the Icon */
                        display: flex;
                        align-items: center;
                        justify-content: center;

                        opacity: 0.7;
                        transition: opacity 0.2s;
                    }

                    /* Optical adjustment for the 'x' character */
                    #<?php echo esc_attr($id); ?> .wprr-modal-close-btn span,
                    #<?php echo esc_attr($id); ?> .wprr-modal-close-btn {
                        padding-bottom: 2px;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-close-btn:hover {
                        opacity: 1;
                    }

                    /* --- Rest of Styles (Keep Compact) --- */

                    #<?php echo esc_attr($id); ?> .wprr-modal-scroll-body {
                        overflow-y: auto;
                        flex: 1;
                        padding-bottom: 30px;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-banner {
                        width: 100%;
                        height: auto;
                        max-height: 160px;
                        object-fit: cover;
                        display: block;
                        /* Ensure it touches the header with no gap */
                        margin-top: 0;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-headings {
                        text-align: center;
                        padding: 20px 20px 15px;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-headings h2 {
                        margin: 0 0 5px;
                        font-size: 26px;
                        font-weight: 800;
                        color: #222;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-headings p {
                        margin: 0;
                        color: #666;
                        font-size: 14px;
                        font-weight: 600;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-table-wrapper {
                        padding: 0 25px;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-table {
                        width: 100%;
                        border-collapse: collapse;
                        border: 1px solid #e0e0e0;
                        border-radius: 0;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-table th {
                        text-align: left;
                        padding: 12px 15px;
                        font-size: 12px;
                        text-transform: uppercase;
                        color: #888;
                        font-weight: 700;
                        letter-spacing: 0.5px;
                        border: 1px solid #e0e0e0;
                        background: #f9f9f9;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-table td {
                        padding: 12px 15px;
                        font-size: 15px;
                        color: #333;
                        border: 1px solid #e0e0e0;
                        font-weight: 600;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-table td:first-child,
                    #<?php echo esc_attr($id); ?> .wprr-modal-table th:first-child {
                        text-align: center;
                        width: 60px;
                    }

                    #<?php echo esc_attr($id); ?> .wprr-modal-table td:first-child {
                        color:
                            <?php echo esc_attr($header_bg); ?>
                        ;
                        font-weight: 900;
                        font-size: 16px;
                    }
                </style>

                <div class="wprr-modal-top-bar">
                    <h3 class="wprr-modal-title"><?php echo esc_html($distance . ' - ' . $gender); ?></h3>
                    <button class="wprr-modal-close wprr-modal-close-btn" aria-label="Close">&times;</button>
                </div>

                <div class="wprr-modal-scroll-body">

                    <?php if ($banner_url): ?>
                        <img src="<?php echo esc_url($banner_url); ?>" alt="Event Banner" class="wprr-modal-banner">
                    <?php endif; ?>

                    <div class="wprr-modal-headings">
                        <h2>Top <?php echo count($winners); ?> Winners</h2>
                        <p><?php echo esc_html($event_name); ?></p>
                    </div>

                    <div class="wprr-modal-table-wrapper">
                        <?php if ($winners): ?>
                            <table class="wprr-modal-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Bib Number</th>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($winners as $index => $w):
                                        $rank = $index + 1; // Or $w->rank_gender if preferred
                                        ?>
                                        <tr>
                                            <td style="text-align: center; font-weight: bold;"><?php echo $rank; ?></td>
                                            <td><?php echo esc_html($w->bib_number); ?></td>
                                            <td><?php echo esc_html($w->full_name); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p style="text-align:center; color:#999;">No winners found.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }
}
