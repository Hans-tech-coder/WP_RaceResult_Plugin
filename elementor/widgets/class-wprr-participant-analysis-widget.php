<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('\Elementor\Widget_Base')) {
    return;
}

class WPRR_Participant_Analysis_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'wprr_participant_analysis';
    }

    public function get_title()
    {
        return 'Participant Analysis';
    }

    public function get_icon()
    {
        return 'eicon-info-circle';
    }

    public function get_categories()
    {
        return ['wp-race-results'];
    }

    protected function _register_controls()
    {
        // Style Section
        $this->start_controls_section(
            'section_style',
            [
                'label' => 'Style',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_bg_color',
            [
                'label' => 'Card Background',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .wprr-analysis-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .wprr-analysis-card' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Helper to convert HH:MM:SS or HH:MM:SS.ms to total seconds.
     */
    private function time_to_seconds($time_str)
    {
        if (empty($time_str)) {
            return 0;
        }
        $parts = explode(':', $time_str);
        $seconds = 0;
        if (count($parts) === 3) {
            $seconds += intval($parts[0]) * 3600;
            $seconds += intval($parts[1]) * 60;
            $seconds += floatval($parts[2]);
        } elseif (count($parts) === 2) {
            $seconds += intval($parts[0]) * 60;
            $seconds += floatval($parts[1]);
        }
        return $seconds;
    }

    /**
     * Helper to format seconds back to HH:MM:SS
     */
    private function seconds_to_time($seconds)
    {
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds / 60) % 60);
        $secs = floor($seconds % 60);
        return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
    }

    protected function render()
    {
        // 1. Visibility Check
        $entry_id = get_query_var('wprr_entry_id');
        if (empty($entry_id)) {
            return;
        }

        // 2. Fetch Data
        $result = WPRR_DB::get_result_by_id($entry_id);

        if (!$result) {
            echo '<div class="wprr-error">Participant not found.</div>';
            return;
        }

        // Get all finish times for this category to calculate stats (Histogram)
        $all_times_raw = WPRR_DB::get_all_finish_times($result->event_id, $result->distance, $result->gender);

        // Fetch Total Counts
        $total_overall = WPRR_DB::get_total_participants_count($result->event_id, $result->distance);
        $total_gender  = WPRR_DB::get_total_participants_count($result->event_id, $result->distance, $result->gender);

        // --- Data Prep for Dual Charts ---
        
        // 1. Gun Stats
        $rank_gun_overall = $result->rank_overall;
        $rank_gun_gender  = $result->rank_gender;
        $passed_gun_overall = max(0, $total_overall - $rank_gun_overall);
        $passed_gun_gender  = max(0, $total_gender - $rank_gun_gender);

        // 2. Chip Stats
        $rank_chip_overall = WPRR_DB::get_calculated_rank($result->event_id, $result->distance, $result->chip_time, 'chip_time');
        $rank_chip_gender  = WPRR_DB::get_calculated_rank($result->event_id, $result->distance, $result->chip_time, 'chip_time', $result->gender);
        $passed_chip_overall = max(0, $total_overall - $rank_chip_overall);
        $passed_chip_gender  = max(0, $total_gender - $rank_chip_gender);

        // Prepare Data for JS
        $js_data = [
            'gun' => [
                'passedOverall' => (int)$passed_gun_overall,
                'rankOverall' => (int)$rank_gun_overall,
                'passedGender' => (int)$passed_gun_gender,
                'rankGender' => (int)$rank_gun_gender
            ],
            'chip' => [
                'passedOverall' => (int)$passed_chip_overall,
                'rankOverall' => (int)$rank_chip_overall,
                'passedGender' => (int)$passed_chip_gender,
                'rankGender' => (int)$rank_chip_gender
            ],
            'userGender' => esc_js($result->gender)
        ];

        // 3. Process Data for Histogram
        $all_seconds = [];
        $user_seconds = $this->time_to_seconds($result->chip_time);

        foreach ($all_times_raw as $t) {
            $sec = $this->time_to_seconds($t);
            if ($sec > 0) {
                $all_seconds[] = $sec;
            }
        }
        sort($all_seconds); 

        $min_time = !empty($all_seconds) ? min($all_seconds) : 0;
        $max_time = !empty($all_seconds) ? max($all_seconds) : 0;
        if ($max_time == $min_time) $max_time += 60;

        $bin_count = 15;
        $range = $max_time - $min_time;
        $bin_size = $range / $bin_count;

        $histogram_data = array_fill(0, $bin_count, 0);
        $histogram_labels = [];
        for ($i = 0; $i < $bin_count; $i++) {
            $start_sec = $min_time + ($i * $bin_size);
            $histogram_labels[] = $this->seconds_to_time($start_sec);
        }

        foreach ($all_seconds as $time) {
            $bin = floor(($time - $min_time) / $bin_size);
            if ($bin >= $bin_count) $bin = $bin_count - 1;
            if ($bin < 0) $bin = 0;
            $histogram_data[$bin]++;
        }

        $user_bin = floor(($user_seconds - $min_time) / $bin_size);
        if ($user_bin >= $bin_count) $user_bin = $bin_count - 1;
        if ($user_bin < 0) $user_bin = 0;

        $background_colors = array_fill(0, $bin_count, 'rgba(200, 200, 200, 0.6)'); 
        $background_colors[$user_bin] = 'rgba(255, 102, 0, 0.8)'; 

        ?>
        <style>
            .wprr-analysis-wrapper { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
            .wprr-fade-in { animation: wprrFadeIn 0.5s ease-out; }
            @keyframes wprrFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .wprr-view-container { transition: all 0.3s ease; }
            .wprr-rank-bubble { width: 50px; height: 50px; background: #000; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; margin: 0 auto; transition: all 0.3s ease; }
        </style>

        <div class="wprr-analysis-wrapper">

            <!-- Header Card -->
            <div class="wprr-analysis-card" style="box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 12px; padding: 30px; margin-bottom: 30px; display: flex; flex-wrap: wrap; gap: 20px; align-items: center; justify-content: space-between; background: #fff;">
                
                <div style="display: flex; align-items: center; gap: 20px;">
                    <div class="wprr-analysis-rank-wrapper" style="text-align: center; margin-right: 10px;">
                        <div class="wprr-rank-circle" style="background: #333; color: #fff; width: 80px; height: 80px; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; margin: 0 auto;">
                            <span style="font-size: 10px; opacity: 0.8; text-transform: uppercase;">Rank</span>
                            <span id="wprr-header-rank" style="font-size: 24px; font-weight: bold; line-height: 1;"><?php echo esc_html($rank_gun_overall); ?></span>
                        </div>
                        <div style="font-size: 11px; text-transform: uppercase; margin-top: 5px; color: #666; font-weight: bold;">
                            OUT OF <?php echo esc_html($total_overall); ?>
                        </div>
                    </div>

                    <div>
                        <h2 style="margin: 0; font-size: 28px;"><?php echo esc_html($result->full_name); ?></h2>
                        <div style="opacity: 0.7; font-size: 16px; margin-top: 5px;">
                            Bib: <strong><?php echo esc_html($result->bib_number); ?></strong> &nbsp;|&nbsp;
                            Distance: <strong><?php echo esc_html($result->distance); ?></strong>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 20px;">
                    <div style="padding: 15px 25px; background: rgba(0,0,0,0.05); border-radius: 8px; text-align: center;">
                        <div style="font-size: 12px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px;">Chip Time</div>
                        <div style="font-size: 20px; font-weight: bold; color: #333;"><?php echo esc_html($result->chip_time); ?></div>
                    </div>
                    <div style="padding: 15px 25px; background: rgba(0,0,0,0.05); border-radius: 8px; text-align: center;">
                        <div style="font-size: 12px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px;">Gun Time</div>
                        <div style="font-size: 20px; font-weight: bold; color: #555;"><?php echo esc_html($result->gun_time); ?></div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="wprr-charts-grid" style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

                <!-- Performance Chart Box -->
                <div class="wprr-chart-box" style="background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 12px; padding: 25px;">
                    
                    <!-- Header with Luxury Toggle -->
                    <div class="wprr-chart-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h4 id="wprr-analysis-title" style="margin: 0; font-size: 16px; font-weight: 600; color: #333;">Gun Time Performance</h4>
                        
                        <div class="wprr-toggle-wrapper" style="background: #f0f2f5; padding: 4px; border-radius: 50px; display: inline-flex; align-items: center;">
                            <button id="wprr-btn-gun" onclick="toggleAnalysisMode('gun')" 
                                style="border: none; background: #333; color: #fff; padding: 6px 16px; border-radius: 40px; font-size: 11px; font-weight: 700; text-transform: uppercase; cursor: pointer; transition: all 0.3s ease; outline: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                Gun Time
                            </button>
                            <button id="wprr-btn-chip" onclick="toggleAnalysisMode('chip')" 
                                style="border: none; background: transparent; color: #888; padding: 6px 16px; border-radius: 40px; font-size: 11px; font-weight: 700; text-transform: uppercase; cursor: pointer; transition: all 0.3s ease; outline: none;">
                                Chip Time
                            </button>
                        </div>
                    </div>

                    <!-- Dual Views -->
                    <div style="position: relative; height: 250px;">
                        
                        <!-- Gun View -->
                        <div id="wprr-view-gun" class="wprr-view-container wprr-fade-in" style="display: flex; gap: 20px; height: 100%;">
                            <div style="flex: 7; position: relative;">
                                <canvas id="wprr-chart-gun"></canvas>
                            </div>
                            <div style="flex: 3; display: flex; flex-direction: column; justify-content: center; gap: 15px; align-items: center;">
                                <div style="text-align: center;">
                                    <div class="wprr-rank-bubble"><?php echo esc_html($rank_gun_overall); ?></div>
                                    <div style="font-size: 10px; text-transform: uppercase; margin-top: 5px; font-weight: bold;">Rank Overall</div>
                                </div>
                                <div style="text-align: center;">
                                    <div class="wprr-rank-bubble"><?php echo esc_html($rank_gun_gender); ?></div>
                                    <div style="font-size: 10px; text-transform: uppercase; margin-top: 5px; font-weight: bold;">Rank Gender</div>
                                </div>
                            </div>
                        </div>

                        <!-- Chip View (Hidden by default) -->
                        <div id="wprr-view-chip" class="wprr-view-container" style="display: none; gap: 20px; height: 100%;">
                            <div style="flex: 7; position: relative;">
                                <canvas id="wprr-chart-chip"></canvas>
                            </div>
                            <div style="flex: 3; display: flex; flex-direction: column; justify-content: center; gap: 15px; align-items: center;">
                                <div style="text-align: center;">
                                    <div class="wprr-rank-bubble"><?php echo esc_html($rank_chip_overall); ?></div>
                                    <div style="font-size: 10px; text-transform: uppercase; margin-top: 5px; font-weight: bold;">Rank Overall</div>
                                </div>
                                <div style="text-align: center;">
                                    <div class="wprr-rank-bubble"><?php echo esc_html($rank_chip_gender); ?></div>
                                    <div style="font-size: 10px; text-transform: uppercase; margin-top: 5px; font-weight: bold;">Rank Gender</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Histogram Chart Box -->
                <div class="wprr-chart-box" style="background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 12px; padding: 25px;">
                    <h3 style="margin-top: 0; font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">Finisher's Distribution</h3>
                    <div style="height: 250px; width: 100%;">
                        <canvas id="wprr_histogram_chart"></canvas>
                    </div>
                </div>

            </div>

            <!-- Back Button -->
            <div style="margin-top: 30px; text-align: center;">
                <a href="javascript:history.back()" style="display: inline-block; padding: 10px 20px; border: 1px solid #333; border-radius: 4px; color: #333; text-decoration: none; font-weight: bold;">&larr; Back to Full Results</a>
            </div>

        </div>

        <script>
            // Global Toggle Function
            window.toggleAnalysisMode = function(mode) {
                const data = <?php echo json_encode($js_data); ?>;
                const gunView = document.getElementById('wprr-view-gun');
                const chipView = document.getElementById('wprr-view-chip');
                const title = document.getElementById('wprr-analysis-title');
                const headerRank = document.getElementById('wprr-header-rank');
                const btnGun = document.getElementById('wprr-btn-gun');
                const btnChip = document.getElementById('wprr-btn-chip');

                // 1. Toggle Containers
                if (mode === 'gun') {
                    chipView.style.display = 'none';
                    gunView.style.display = 'flex';
                    gunView.classList.add('wprr-fade-in');
                    title.innerText = 'Gun Time Performance';
                    headerRank.innerText = data.gun.rankOverall;
                } else {
                    gunView.style.display = 'none';
                    chipView.style.display = 'flex';
                    chipView.classList.add('wprr-fade-in');
                    title.innerText = 'Chip Time Performance';
                    headerRank.innerText = data.chip.rankOverall;
                    
                    // Lazy init Chip Chart if needed
                    if (!window.chipChartInstance) {
                        const ctxChip = document.getElementById('wprr-chart-chip').getContext('2d');
                        window.chipChartInstance = new Chart(ctxChip, window.wprrChartMeta.config('chip'));
                    }
                }

                // 2. Update Button Styles
                const activeStyle = "border: none; background: #333; color: #fff; padding: 6px 16px; border-radius: 40px; font-size: 11px; font-weight: 700; text-transform: uppercase; cursor: pointer; transition: all 0.3s ease; outline: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);";
                const inactiveStyle = "border: none; background: transparent; color: #888; padding: 6px 16px; border-radius: 40px; font-size: 11px; font-weight: 700; text-transform: uppercase; cursor: pointer; transition: all 0.3s ease; outline: none;";

                if (mode === 'gun') {
                    btnGun.style.cssText = activeStyle;
                    btnChip.style.cssText = inactiveStyle;
                } else {
                    btnGun.style.cssText = inactiveStyle;
                    btnChip.style.cssText = activeStyle;
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
                const data = <?php echo json_encode($js_data); ?>;
                const userGender = data.userGender;

                // Meta for shared config
                window.wprrChartMeta = {
                    config: (mode) => ({
                        type: 'doughnut',
                        data: {
                            labels: ['Slower', 'Rank'],
                            datasets: [
                                {
                                    label: 'Overall',
                                    data: [data[mode].passedOverall, data[mode].rankOverall],
                                    backgroundColor: ['#e05a2b', '#333333'],
                                    borderWidth: 3,
                                    borderColor: '#ffffff',
                                    hoverOffset: 15,
                                    cutout: '55%'
                                },
                                {
                                    label: 'Gender',
                                    data: [data[mode].passedGender, data[mode].rankGender],
                                    backgroundColor: ['#e05a2b', '#333333'],
                                    borderWidth: 3,
                                    borderColor: '#ffffff',
                                    hoverOffset: 10,
                                    cutout: '60%'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { animateScale: true, animateRotate: true, duration: 1500, easing: 'easeOutQuart' },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: { size: 0 },
                                    bodyFont: { size: 14, family: "'Inter', sans-serif" },
                                    callbacks: {
                                        label: function(context) {
                                            const ds = context.datasetIndex; 
                                            const idx = context.dataIndex; 
                                            const val = context.raw;
                                            if (ds === 0) {
                                                return (idx === 0) ? 'Finished after you: ' + val : 'Rank Overall: ' + val;
                                            } else {
                                                return (idx === 0) ? userGender + 's finished after you: ' + val : 'Rank ' + userGender + ': ' + val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    })
                };

                // Init Gun Chart
                const ctxGun = document.getElementById('wprr-chart-gun').getContext('2d');
                new Chart(ctxGun, window.wprrChartMeta.config('gun'));

                // Histogram
                const ctxHist = document.getElementById('wprr_histogram_chart').getContext('2d');
                new Chart(ctxHist, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($histogram_labels); ?>,
                        datasets: [{
                            label: 'Finishers',
                            data: <?php echo json_encode($histogram_data); ?>,
                            backgroundColor: <?php echo json_encode($background_colors); ?>,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f0f0f0' } },
                            x: { grid: { display: false }, ticks: { maxRotation: 45, maxTicksLimit: 8 } }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
            });
        </script>
        <?php
    }
}
