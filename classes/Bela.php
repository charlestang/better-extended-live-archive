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
     * @var BelaIndicesBuilder 
     */
    public $builder = null;

    /**
     * The constructor of the plugin object
     */
    public function __construct() {
        $this->options = new BelaOptions();
        $this->builder = new BelaIndicesBuilder($this->options, 'file');
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

        /**
         * BELA admin panels
         */
        $belaAdmin = new BelaAdmin($this->options);
        $belaAdmin->run();

        /**
         * BELA AJAX processor
         */
        $belaAjax = new BelaAjax($this->options);
        add_action('wp_ajax_nopriv_' . BelaAjax::BELA_AJAX_VAR, array($belaAjax, 'entry'));
        add_action('wp_ajax_' . BelaAjax::BELA_AJAX_VAR, array($belaAjax, 'entry'));
    }

    /**
     * Inject static files of this plugin to target pages
     * @global type $ela_plugin_basename
     */
    public function injectStaticFiles() {
        $this->echoAjaxEntry();
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

    /**
     * the Bela component on the web page
     * @return string
     */
    public function getBelaComponent() {
        if (!$this->builder->isIndicesInitialized()) {
            $this->builder->initializeIndexCache();
        }
    }

    public function echoAjaxEntry() {
        echo '<script type="text/javascript">var belaAjaxUrl="', admin_url('admin_ajax.php', 'relative'), '";</script>';
    }

}

