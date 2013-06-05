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

    public $options = null;
    public $defaultAction = '';

    public function __construct($options) {
        $this->options = $options;
    }

    /**
     * This function inject the style files or scripts files 
     * to the WordPress admin panel of the plugin
     */
    public function injectAdminStylesAndScripts() {
        
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
            if (is_admin() && $_GET['page'] == self::PAGE_SLUG) {
                add_action('admin_head', array($this, 'injectAdminStylesAndScripts'));
            }
            add_action('admin_menu', array($this, 'registerAdminPage'));
        }
    }

    /**
     * Admin pages dispatcher
     */
    public function adminPanelEntryPoint() {
        $belaAction = $_GET[self::SUBPAGE_VAR];
        $methodName = $this->parseRoute($belaAction);
        call_user_func(array($this, $methodName), $_GET, $_POST);
    }

    /**
     * Use action name to generate the admin page url
     * @param string $action
     * @return string the url of the action page
     */
    public static function URL($action) {
        $names = preg_split('~(?=[A-Z])~', $action);
        $param = implode('-', array_map('lcfirst', $names));
        return menu_page_url(self::PAGE_SLUG) . '&' . self::SUBPAGE_VAR . '=' . $param;
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

}
