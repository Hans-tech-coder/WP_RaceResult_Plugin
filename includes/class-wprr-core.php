<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Core Plugin Class
 */
class WPRR_Core
{
    public function __construct()
    {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    private function define_constants()
    {
        if (!defined('WPRR_VERSION')) {
            define('WPRR_VERSION', '1.0.0');
        }
        if (!defined('WPRR_PATH')) {
            define('WPRR_PATH', plugin_dir_path(dirname(__FILE__)));
        }
        if (!defined('WPRR_URL')) {
            define('WPRR_URL', plugin_dir_url(dirname(__FILE__)));
        }
        if (!defined('WPRR_DEFAULT_WINNERS')) {
            define('WPRR_DEFAULT_WINNERS', 3);
        }
    }

    private function includes()
    {
        require_once WPRR_PATH . 'includes/class-wprr-db.php';
        require_once WPRR_PATH . 'includes/class-wprr-modal-renderer.php';
        require_once WPRR_PATH . 'includes/class-wp-race-results-admin.php';
        require_once WPRR_PATH . 'includes/csv-import-handler.php';

        // Initialize Admin
        if (is_admin()) {
            $admin = new WP_Race_Results_Admin('wp-race-results', WPRR_VERSION);
            $admin->init();
        }

        // Initialize Elementor
        if (did_action('elementor/loaded')) {
            require_once WPRR_PATH . 'elementor/elementor-init.php';
        }
    }

    private function init_hooks()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets']);
    }

    public function enqueue_public_assets()
    {
        wp_enqueue_style(
            'wprr-public-style',
            WPRR_URL . 'assets/css/wp-race-results-public.css',
            [],
            WPRR_VERSION
        );

        wp_enqueue_script(
            'wprr-modal-js',
            WPRR_URL . 'assets/js/wprr-winners-modal.js',
            ['jquery'],
            WPRR_VERSION,
            true
        );

        wp_localize_script('wprr-modal-js', 'wprr_modal_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wprr_modal_nonce')
        ]);
    }
}
