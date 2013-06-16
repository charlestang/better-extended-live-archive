<?php

/**
 * This object is used to generate the archive cache.
 *
 * @author Charles Tang<charlestang@foxmail.com>
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

    public function __construct($options, $cache) {
        global $ela_cache_root;
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
                $this->_cache->cacheFilePath = $ela_cache_root;
                break;
            default:
                break;
        }
    }

    public function initializeIndexCache() {
        $types = $this->_options->get(BelaKey::NAVIGATION_TABS_ORDER);

        foreach ($types as $type) {
            $index = $this->getIndex($type);
            $index->build();
        }

        $this->_options->set(BelaKey::CACHE_INITIALIZED, false);
    }

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

    public function updateIndexCache() {
        
    }

    /**
     * Check the indices are initialized or not
     * @return boolean
     */
    public function isIndicesInitialized() {
        $initialized = $this->_options->get(BelaKey::CACHE_INITIALIZED);

        if ($initialized) {
            foreach ($this->_indices as $idx) {
                $initialized = $initialized && $idx->initialized();
            }
        }

        return $initialized;
    }

}
