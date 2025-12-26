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
        $event_name = isset($args['event_name']) ? $args['event_name'] : '';
        $declared = isset($args['declared']) ? absint($args['declared']) : 0;

        // CRITICAL: Modal must independently fetch ALL declared winners from DB.
        // The widget only fetches podium (top 3) for card display.
        // This ensures modal shows complete results per admin settings.
        $winners = WPRR_DB::get_race_results($event_id, $distance, $gender, $declared);

        // Temporary debug (remove after verification)
        error_log("MODAL DEBUG: event=$event_id dist=$distance gender=$gender declared=$declared rows=" . count($winners));

        ?>
        <div id="<?php echo esc_attr($id); ?>" class="wprr-modal" aria-hidden="true">
            <div class="wprr-modal-overlay"></div>
            <div class="wprr-modal-content" role="dialog" aria-modal="true">
                <div class="wprr-modal-header">
                    <h2><?php echo esc_html($distance); ?> - <?php echo esc_html($gender); ?></h2>
                    <button class="wprr-modal-close" aria-label="Close modal">&times;</button>
                </div>
                <div class="wprr-modal-body">
                    <?php if (current_user_can('manage_options')): ?>
                        <!--
                    WPRR MODAL DEBUG
                    Declared: <?php echo esc_html($declared); ?>
                    Rows Rendered: <?php echo count($winners); ?>
                    -->
                    <?php endif; ?>


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
                                    $rank_position = $index + 1;
                                    if ($rank_position === 1) {
                                        $rank_display = '🥇';
                                    } elseif ($rank_position === 2) {
                                        $rank_display = '🥈';
                                    } elseif ($rank_position === 3) {
                                        $rank_display = '🥉';
                                    } else {
                                        $rank_display = $rank_position . '.';
                                    }
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
