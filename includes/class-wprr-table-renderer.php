<?php
if (!defined('ABSPATH')) {
    exit;
}

class WPRR_Table_Renderer
{

    public static function render_html($results, $base_url, $page, $total_pages, $search, $gender, $settings)
    {
        $striped_class = (isset($settings['table_striped_rows']) && $settings['table_striped_rows'] === 'yes') ? 'wprr-table-striped' : '';
        $action_header = isset($settings['action_header_text']) ? $settings['action_header_text'] : 'Action';
        $action_icon = isset($settings['action_icon']) ? $settings['action_icon'] : ['value' => 'fas fa-chart-bar', 'library' => 'fa-solid'];

        ob_start();

        if (!empty($results)): ?>
            <div class="wprr-desktop-view">
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
                                <th style="text-align: left; font-weight: bold;"><?php echo esc_html($action_header); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result):
                                $analysis_url = esc_url(trailingslashit($base_url) . 'entry/' . $result->id . '/');
                                ?>
                                <tr>
                                    <td><?php echo esc_html($result->rank_overall); ?></td>
                                    <td><?php echo esc_html($result->bib_number); ?></td>
                                    <td><?php echo esc_html($result->full_name); ?></td>
                                    <td><?php echo esc_html($result->gender); ?></td>
                                    <td><?php echo esc_html($result->chip_time); ?></td>
                                    <td><?php echo esc_html($result->gun_time); ?></td>
                                    <td style="text-align: left;">
                                        <a href="<?php echo $analysis_url; ?>" class="wprr-analysis-link" title="View Analysis">
                                            <?php \Elementor\Icons_Manager::render_icon($action_icon, ['aria-hidden' => 'true']); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="wprr-mobile-view">
                <?php foreach ($results as $result):
                    $analysis_url = esc_url(trailingslashit($base_url) . 'entry/' . $result->id . '/');
                    ?>
                    <div class="wprr-result-card">
                        <div class="wprr-card-rank"><?php echo esc_html($result->rank_overall); ?></div>
                        <div class="wprr-card-info">
                            <div class="wprr-card-name"><?php echo esc_html($result->full_name); ?></div>
                            <div class="wprr-card-bib">Bib: <?php echo esc_html($result->bib_number); ?></div>
                        </div>
                        <div class="wprr-time-grid">
                            <div class="wprr-time-col dark">
                                <span class="wprr-time-val"><?php echo esc_html($result->gun_time); ?></span>
                                <span class="wprr-time-label">Official Time</span>
                            </div>
                            <div class="wprr-time-col light">
                                <span class="wprr-time-val"><?php echo esc_html($result->chip_time); ?></span>
                                <span class="wprr-time-label">Chip Time</span>
                            </div>
                        </div>
                        <div class="wprr-card-action">
                            <a href="<?php echo $analysis_url; ?>" class="wprr-card-btn">
                                <i class="fas fa-chart-line"></i> View Analysis
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="wprr-pagination"
                    style="margin-top: 20px; display: flex; justify-content: center; gap: 10px; align-items: center;">
                    <?php
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
                <p style="margin: 0; color: #666;">No results found.</p>
            </div>
        <?php endif;

        return ob_get_clean();
    }
}
