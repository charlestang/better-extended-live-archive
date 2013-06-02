<?php

/**
 * Description of Bela
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class Bela {

    /**
     * @var BelaOptions 
     */
    public $options = null;

    /**
     * @var BelaCacheBuilder 
     */
    public $builder = null;

    /**
     * @var BelaCache 
     */
    public $cache = null;

    /**
     * The constructor of the plugin object
     */
    public function __construct() {
        $this->options = new BelaOptions();
        $this->cache = new BelaFileCache();
        $this->builder = new BelaCacheBuilder($this->options, $this->cache);
    }

    /**
     * The entry point of the plugin
     */
    public function run() {
        add_action('plugins_loaded', array($this, 'registerHooks'));
    }

    /**
     * Add the hooks to WordPress
     */
    public function registerHooks() {
        /**
         * static files
         */
        add_action('wp_head', array($this, 'injectStaticFiles'));
        /**
         * when post changes, update the index
         */
        add_action('publish_post', array($this->builder, 'updateIndexCache'));
        add_action('deleted_post', array($this->builder, 'updateIndexCache'));
        /**
         * when comment changes, update the index
         */
        add_action('comment_post', array($this->builder, 'updateIndexCache'));
        add_action('trackback_post', array($this->builder, 'updateIndexCache'));
        add_action('pingback_post', array($this->builder, 'updateIndexCache'));
        add_action('delete_comment', array($this->builder, 'updateIndexCache'));
        /**
         * short code
         */
        add_shortcode('extended-live-archive', 'af_ela_shorcode');

        if (is_admin()) {
            if (isset($_GET['page']) && $_GET['page'] == 'extended-live-archive') {
                add_action('admin_head', 'better_ela_js_code_in_admin_page');
            }
            add_action('admin_menu', 'af_ela_admin_pages');
        }
    }

    public function injectStaticFiles() {
        global $ela_plugin_basename;
        // loading stuff
        $settings = get_option('af_ela_options');
        $plugin_path = WP_PLUGIN_URL . '/' . $ela_plugin_basename;
        if ($settings['use_default_style']) {
            if (file_exists(ABSPATH . 'wp-content/themes/' . get_template() . '/ela.css')) {
                $csspath = get_bloginfo('template_url') . "/ela.css";
            } else {
                $csspath = $plugin_path . "/includes/af-ela-style.css";
            }

            $text = <<<TEXT

	<link rel="stylesheet" href="$csspath" type="text/css" media="screen" />

TEXT;
        } else {
            $text = '';
        }

        echo $text;
    }

}

