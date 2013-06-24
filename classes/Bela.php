<?php

/**
 * Description of Bela
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class Bela {

    const JSVER = '20130615';

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
        $this->builder = new BelaIndicesBuilder($this->options, BELA_CACHE_TYPE);
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
        add_shortcode('extended-live-archive', array($this, 'shortCode'));

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
        $style_name = $this->options->get(BelaKey::STYLE_NAME);
        $style = BELA_BASE_URL . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'bela-' . $style_name . '.css';
        echo '<link rel="stylesheet" href="', $style , '" type="text/css" medir="screen" />';
    }

    /**
     * the Bela component on the web page
     * @return string
     */
    public function printBelaContainer() {
        if (!$this->builder->isIndicesInitialized()) {
            $this->builder->initializeIndexCache();
        }
        $tabs = $this->options->get(BelaKey::NAVIGATION_TABS_ORDER);
        $jsurl = BELA_BASE_URL . '/js/bela.js';
        if (!empty($tabs)) {
            ?>
            <script type="text/javascript">
                var belaLoadingTip = "loading ...";
                var belaIdleTip = "";
            </script> 
            <script type="text/javascript" src="<?php echo $jsurl; ?>?ver=<?php echo self::JSVER; ?>"></script>
            <div id="bela-container">
                <div class="bela-loading" style="display:none;"></div>
                <ul id="bela-navi-menu">
                    <?php foreach ($tabs as $tab) : ?>
                        <li class="bela-navi-tab" data="<?php echo $tab; ?>"><?php echo $this->options->getLabel($tab); ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="bela-indices"></div>
            </div>
            <div style="clear:both;"></div>
            <?php
        }
    }

    /**
     * For front end to config the ajax entry
     */
    public function echoAjaxEntry() {
        $belaAjaxUrl = admin_url('admin-ajax.php', 'relative');
        $belaAjaxAction = BelaAjax::BELA_AJAX_VAR;
        $js = <<< JSCODE
        <script type="text/javascript">
            var belaAjaxUrl="{$belaAjaxUrl}";
            var belaAjaxAction = "{$belaAjaxAction}";
        </script>
JSCODE;
        echo $js;
    }

    /**
     * Short code process method
     * @return string
     */
    public function shortCode() {
        ob_start();
        $this->printBelaContainer();
        $bela = ob_get_contents();
        ob_end_clean();
        return $bela;
    }

}

