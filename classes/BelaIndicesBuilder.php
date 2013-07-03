<?php

/**
 * This object is used to generate the archive cache.
 *
 * @author Charles Tang<charlestang@foxmail.com>
 * @since 1.0
 */
class BelaIndicesBuilder {

    /**
     * @var BelaOptions 
     */
    private $_options = null;

    /**
     * @var BelaCache 
     */
    private $_cache = null;

    /**
     * @var BelaIndex[] 
     */
    private $_indices = array();

    /**
     * The constructor of the BelaIndicesBuilder
     * @param BelaOptions $options
     * @param BelaCache $cache
     * @throws BelaIndexException
     */
    public function __construct($options, $cache) {
        if ($options instanceof BelaOptions) {
            $this->_options = $options;
        } else {
            throw new BelaIndexException("Please initialize the BelaOptions object first.");
        }

        if (!in_array($cache, array('file' /* , 'db' */))) {
            throw new BelaIndexException("Dose not support this kind of cache now");
        }
        switch ($cache) {
            case 'file':
                $this->_cache = new BelaFileCache();
                $this->_cache->cacheFilePath = BELA_CACHE_ROOT;
                break;
            default:
                break;
        }
    }

    /**
     * initialize the indices "files" if the first time run
     */
    public function initializeIndexCache() {
        $types = $this->_options->get(BelaKey::NAVIGATION_TABS_ORDER);

        foreach ($types as $type) {
            $index = $this->getIndex($type);
            $index->build();
        }

        $this->_options->set(BelaKey::CACHE_INITIALIZED, true, true);
    }

    /**
     * Index factory, return the index object according to the index name
     * @param string $type
     * @return BelaIndex
     * @throws BelaIndexException
     */
    public function getIndex($type) {
        if (isset($this->_indices[$type])) {
            return $this->_indices[$type];
        }

        switch ($type) {
            case BelaKey::ORDER_KEY_BY_DATE:
                $this->_indices[$type] = new BelaTimeIndex($this->_options, $this->_cache);
                break;
            case BelaKey::ORDER_KEY_BY_CATEGORY:
                $this->_indices[$type] = new BelaCategoryIndex($this->_options, $this->_cache);
                break;
            case BelaKey::ORDER_KEY_BY_TAGS:
                $this->_indices[$type] = new BelaTagIndex($this->_options, $this->_cache);
                break;
            default:
                throw new BelaIndexException('Not a valid index type.');
        }

        return $this->_indices[$type];
    }

    /**
     * When the post is updated, this hook will be called.
     * @param int $postId
     * @param WP_Post $postAfter
     * @param WP_Post $postBefore
     */
    public function beforePostUpdate($postId, $postAfter, $postBefore) {
        if ($postAfter != null && $postAfter->post_status != 'publish') {
            return;
        }
        $types = $this->_options->get(BelaKey::NAVIGATION_TABS_ORDER);

        foreach ($types as $type) {
            $index = $this->getIndex($type);
            $index->beforeUpdate($postId, $postAfter, $postBefore);
        }
    }

    public function updateIndexCache($postId, $post = null) {
        if ($post != null && $post->post_status != 'publish') {
            return;
        }
        $types = $this->_options->get(BelaKey::NAVIGATION_TABS_ORDER);

        foreach ($types as $type) {
            $index = $this->getIndex($type);
            $index->afterUpdate($postId, $post);
        }
    }

    /**
     * Update the index cache according to the comment
     * @param mixed $commentIds
     * @param boolean $approved
     */
    public function updateIndexCacheByComment($commentIds, $approved = null) {
        if (!is_array($commentIds)) {
            if (!is_null($approved)) {
                
            } else {
                
            }
        } elseif (is_array($commentIds)) {
            
        }
    }

    /**
     * Check the indices are initialized or not
     * @return boolean
     */
    public function isIndicesInitialized() {
        $initialized = $this->_options->get(BelaKey::CACHE_INITIALIZED);

        if ($initialized) { //if the indices are already exists, check them
            foreach ($this->_indices as $idx) {
                $initialized = $initialized && $idx->initialized();
            }
        }

        return $initialized;
    }

}
