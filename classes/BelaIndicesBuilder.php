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
        $typesStr = trim($this->_options->get(BelaKey::NAVIGATION_TABS_ORDER));
        if (empty($typesStr)) {
            return true;
        }

        $types = explode(',', $typesStr);

        foreach ($types as $type) {
            switch ($type) {
                case BelaKey::ORDER_KEY_BY_DATE:
                    $indexr = new BelaTimeIndex($this->_options, $this->_cache);
                    $indexr->build();
                    break;
                case BelaKey::ORDER_KEY_BY_CATEGORY:
                    $indexr = new BelaCategoryIndex($this->_options, $this->_cache);
                    $indexr->build();
                    break;
                case BelaKey::ORDER_KEY_BY_TAGS:
                    $indexr = new BelaTagIndex($this->_options, $this->_cache);
                    $indexr->build();
                    break;
                default:
                    break;
            }
        }

        $this->_options->set(BelaKey::CACHE_INITIALIZED, false);
    }

    public function updateIndexCache() {

    }

}
