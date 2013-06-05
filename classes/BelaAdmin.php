<?php

/**
 * the admin page of the plugin
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaAdmin {

    const PAGE_SLUG = 'better-extended-live-archive';
    const PAGE_TITLE = 'Better Extended Live Archive Options';
    const MENU_CAPTION = 'Better Ext. Live Archive';

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

    public function run() {
        if (is_admin()) {
            if (is_admin() && $_GET['page'] == self::PAGE_SLUG) {
                add_action('admin_head', array($this, 'injectAdminStylesAndScripts'));
            }
            add_action('admin_menu', array($this, 'registerAdminPage'));
        }
    }

    public function adminPanelEntryPoint() {
        
    }

}
