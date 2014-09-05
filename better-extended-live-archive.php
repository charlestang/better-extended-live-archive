<?php

/*
  Plugin Name: Better Extended Live Archives
  Plugin URI: https://github.com/charlestang/better-extended-live-archive
  Description: Bloggers can generate a multidimensional archive of all posts in his or her blog with this plugin.
  Version: 1.5
  Author: Charles Tang
  Author URI: http://sexywp.com
 */

$bela_path = dirname(__FILE__);
require_once $bela_path . '/define-constants.php';
require_once $bela_path . '/classes/BelaLogger.php';
require_once $bela_path . '/classes/interface.php';
require_once $bela_path . '/classes/exception.php';
require_once $bela_path . '/classes/BelaFileCache.php';
require_once $bela_path . '/classes/BelaTimeIndex.php';
require_once $bela_path . '/classes/BelaCategoryIndex.php';
require_once $bela_path . '/classes/BelaTagIndex.php';
require_once $bela_path . '/classes/BelaIndicesBuilder.php';
require_once $bela_path . '/classes/BelaOptions.php';
require_once $bela_path . '/classes/BelaHtml.php';
require_once $bela_path . '/classes/BelaAdmin.php';
require_once $bela_path . '/classes/BelaAjax.php';
require_once $bela_path . '/classes/BelaCategoryWalker.php';
require_once $bela_path . '/classes/Bela.php';

/**
 * The entry file path of this plugin.
 */
define('BELA_ENTRY_FILE', __FILE__);
$directory_name = plugin_basename(dirname(BELA_ENTRY_FILE));
define('BELA_BASE_PATH', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $directory_name);
define('BELA_BASE_URL', WP_PLUGIN_URL . DIRECTORY_SEPARATOR . $directory_name);
define('BELA_CACHE_TYPE', 'file');
define('BELA_CACHE_ROOT', BELA_BASE_PATH . DIRECTORY_SEPARATOR . 'cache');
define('BELA_DEBUG', false);
define('BELA_DEBUG_CLASS', 'BelaAdmin');

/**
 * This is a template tag offered by this plugin,
 * call this function in your theme will output a 
 * BELA component on your page.
 * @return void 
 */
function better_extended_live_archive() {
    global $belaObj;
    $belaObj->printBelaContainer();
}

$belaObj = new Bela();
$belaObj->run();
