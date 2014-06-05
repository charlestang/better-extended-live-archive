<?php

/**
 * the admin page of the plugin
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaAdmin {

    const PAGE_SLUG = 'better-extended-live-archive';
    const PAGE_TITLE = 'Better Extended Live Archive Options';
    const MENU_CAPTION = 'Ext. Live Archive';
    const SUBPAGE_VAR = 'bela_a';
    const VIEWS_DIR = 'views';

    /**
     * @var BelaOptions 
     */
    public $options = null;
    public $defaultAction = 'whatToShow';
    public $viewPath = '';
    public $layout = '/common/layout';

    public function __construct($options) {
        $this->options = $options;
        $this->viewPath = BELA_BASE_PATH . DIRECTORY_SEPARATOR . self::VIEWS_DIR;
    }

    /**
     * This function inject the style files or scripts files 
     * to the WordPress admin panel of the plugin
     */
    public function injectAdminStylesAndScripts() {
        if ($this->isCurr('menuSettings')) {
            $cssSrc1 = BELA_BASE_URL . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'jquery-ui-1.9.2.custom.min.css';
            $cssSrc2 = BELA_BASE_URL . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'jquery-ui-multiselect.css';
            wp_enqueue_style('jquery-ui-common', $cssSrc1, array(), Bela::CSSVER);
            wp_enqueue_style('jquery-ui-multiselect', $cssSrc2, array(), Bela::CSSVER);
            $jsSrc = BELA_BASE_URL . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'jquery-ui-multiselect.js';
            wp_enqueue_script('jquery-ui-multiselect', $jsSrc, array('jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-draggable'), Bela::JSVER);
        }
    }

    /**
     * This method register an admin page entry point of the plugin
     */
    public function registerAdminPage() {
        add_options_page(
                self::PAGE_TITLE, // the admin page title of the plugin
                self::MENU_CAPTION, // the text on the settings menu item
                'activate_plugins', //the permission of the page is the save with activate plugins
                self::PAGE_SLUG, // the GET param that navigate to the plugin admin page
                array($this, 'adminPanelEntryPoint') //the callback of the entry point
        );
    }

    /**
     * Admin functions bootstrap
     */
    public function run() {
        if (is_admin()) {
            if (is_admin() && self::getParam('page') == self::PAGE_SLUG) {
                add_action('admin_head', array($this, 'injectAdminStylesAndScripts'));
            }
            add_action('admin_menu', array($this, 'registerAdminPage'));
            add_filter('plugin_action_links', array($this, 'showOptionLink'), 10, 3);
        }
    }

    public function showOptionLink($actions, $plugin_file, $plugin_data) {
        if ($plugin_data['Name'] == 'Better Extended Live Archives') {
            $actions = array('options' => '<a href="' . self::URL('what-to-show') . '">' . __('Options') . '</a>') + $actions;
        }
        return $actions;
    }

    public static function getParam($key, $default = null) {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        return $default;
    }

    public static function postParam($key, $default = null) {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        return $default;
    }

    /**
     * Admin pages dispatcher
     */
    public function adminPanelEntryPoint() {
        $belaAction = self::getParam(self::SUBPAGE_VAR);
        $methodName = $this->parseRoute($belaAction);
        call_user_func(array($this, $methodName));
    }

    /**
     * Use action name to generate the admin page url
     * @param string $action
     * @return string the url of the action page
     */
    public static function URL($action) {
        $names = preg_split('~(?=[A-Z])~', $action);
        $param = implode('-', array_map('lcfirst', $names));
        return menu_page_url(self::PAGE_SLUG, false) . '&' . self::SUBPAGE_VAR . '=' . $param;
    }

    /**
     * To test if the given $action is the current visiting action.
     * @param string $action
     * @return boolean
     */
    public function isCurr($action) {
        $belaAction = self::getParam(self::SUBPAGE_VAR);
        return $this->parseRoute($belaAction) == ('action' . ucfirst($action));
    }

    /**
     * Parse the action name according to the SUBPAGE_VAR
     * @param string $action
     * @return string method name
     * @throws BelaAdminException
     */
    private function parseRoute($action) {
        $action = trim($action);
        if (empty($action)) {
            $action = $this->defaultAction;
        }

        $names = explode('-', $action);
        if (empty($names)) {
            throw new BelaAdminException('Cannot parse the route');
        }

        $methodName = 'action' . implode('', array_map('ucfirst', $names));
        if (!method_exists($this, $methodName)) {
            throw new BelaAdminException('Action name not found: ' . $methodName);
        }

        return $methodName;
    }

    /**
     * Render the view page 
     * @param string $viewName
     */
    public function renderPartial($viewName) {
        $filename = $this->viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        if (!file_exists($filename)) {
            throw new BelaAdminException('Cannot find the view file: ' . $filename);
        }

        if (func_num_args() > 1) {
            $arg1 = func_get_arg(1);
            if (!is_array($arg1)) {
                throw new BelaAdminException('The seconde param of render method should be an array. Given: ' . var_export($arg1, true));
            }

            extract($arg1);
        }

        include $filename;
    }

    /**
     * Render the view file with the layout file
     * @param stirng $viewName the name of the view
     * @param mixed $... Any other parameters passed to the view
     */
    public function render() {
        ob_start();
        call_user_func_array(array($this, 'renderPartial'), func_get_args());
        $pagecontent = ob_get_contents();
        ob_end_clean();

        include $this->viewPath . $this->layout . '.php';
    }

    protected function saveOptions() {
        if (isset($_POST['submit']) && isset($_POST['BelaOptions'])) {
            $this->options->setOptions($_POST['BelaOptions']);
            $this->options->save();
        }
    }

    public function actionWhatToShow() {
        $this->saveOptions();
        $this->render('what-to-show', array('options' => $this->options));
    }

    public function actionHowToShow() {
        $this->saveOptions();
        $this->render('how-to-show', array('options' => $this->options));
    }

    public function actionHowToCut() {
        $tagsPickStrategy = $this->options->get(BelaKey::TAGS_PICK_STRATEGY);
        $this->saveOptions();
        $newStrategy = $this->options->get(BelaKey::TAGS_PICK_STRATEGY);
        if ($newStrategy != $tagsPickStrategy) {//when the tags cut strategy change, rebuild tags table.
            $indexBuilder = new BelaIndicesBuilder($this->options, BELA_CACHE_TYPE);
            $indexBuilder->getIndex(BelaKey::ORDER_KEY_BY_TAGS)->build();
        }
        $this->render('how-to-cut', array('options' => $this->options));
    }

    public function actionMenuSettings() {
        $this->saveOptions();
        $this->render('menu-settings', array('options' => $this->options));
    }

    public function actionCatSettings() {
        $catExlcudes = $this->options->get(BelaKey::EXCLUDE_CATEGORY_LIST);
        $this->saveOptions();
        $newExcludes = $this->options->get(BelaKey::EXCLUDE_CATEGORY_LIST);
        $intersect = array_intersect($catExlcudes, $newExcludes);
        if (!empty($intersect)) {
            $indexBuilder = new BelaIndicesBuilder($this->options, BELA_CACHE_TYPE);
            $indexBuilder->getIndex(BelaKey::ORDER_KEY_BY_CATEGORY)->build();
        }

        $this->render('category-exclusion', array('options' => $this->options));
    }

    public function actionPagination() {
        $this->saveOptions();
        $this->render('pagination', array('options' => $this->options));
    }

    public function actionAppearance() {
        $this->saveOptions();
        $styles_path = BELA_BASE_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
        $styles = glob($styles_path . 'bela-*.css');
        $styles = array_map(create_function('$item', '$temp = str_replace("' . $styles_path . 'bela-' . '", "", $item); return str_replace(".css", "", $temp);'), $styles);
        $this->render('appearance', array('options' => $this->options, 'styles' => $styles));
    }

}
