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
        
    }

}

