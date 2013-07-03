<?php

/**
 * The interface of Cache
 * 
 * @method void set(string $key, mixed $value) put some data into cache
 * @method mixed get(string $key) retrieve data from cache, if the data dose not exist, false will be returned
 * @method void del(string $key) clear data with the specific key in cache
 * @method void clearAllCache() clear all the cache
 * @method boolean exists(string $key) decide if the cache file exists or not
 */
interface BelaCache {

    public function set($key, $value);

    public function get($key);

    public function del($key);

    public function clearAllCache();

    public function exists($key);
}

/**
 * The abstract of the index
 */
abstract class BelaIndex {

    /**
     * The cache used for save the index.
     * @var BelaCache 
     */
    private $_cache;

    /**
     * The options of the plugin.
     * @var BelaOptions 
     */
    private $_options;

    /**
     * The index must have a cache.
     * @param BelaCache $cache
     */
    public function __construct($options, $cache = null) {
        if (is_object($options) && $options instanceof BelaOptions) {
            $this->_options = $options;
        } else {
            throw new BelaIndexException("Options not found for this plugin.");
        }
        if (!is_null($cache)) {
            $this->setCache($cache);
        }
    }

    /**
     * Get the BelaOptions object.
     * @return BelaOptions
     */
    public function getOptions() {
        return $this->_options;
    }

    /**
     * Get the wpdb reference
     * @global wpdb $wpdb
     * @return wpdb
     */
    public function getDb() {
        global $wpdb;
        return $wpdb;
    }

    /**
     * Reset the cache object of the index.
     * @param BelaCache $cache
     */
    public function setCache($cache) {
        if (!is_object($cache) || !($cache instanceof BelaCache)) {
            throw new BelaIndexException("The " . __CLASS__ . " need a cache to save index. Given: " . var_export($cache, true));
        }
        $this->_cache = $cache;
    }

    /**
     * Get the cache object of the index.
     * @return BelaCache
     */
    public function getCache() {
        if (is_null($this->_cache)) {
            throw new BelaIndexException("No cache assigned.");
        }
        return $this->_cache;
    }

    /**
     * Build this kind of the index
     */
    abstract public function build();

    /**
     * Decide if the index is intialized
     * @return boolean If this kind of index is initialized
     */
    abstract public function initialized();

    /**
     * Before the post is updated.
     */
    abstract public function beforeUpdate($postId, $postAfter, $postBefore);

    /**
     * After the post is updated.
     * @param int $postId the post ID of the current inserted post
     * @param WP_Post $post the current post object
     */
    abstract public function afterUpdate($postId, $post);
}