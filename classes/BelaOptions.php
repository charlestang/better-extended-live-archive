<?php

/**
 * This class used to manage the options of the plugin
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaOptions {

    const OPT_KEY = 'better-extended-live-archive-options';

    public $defaultOptions = array(
        BelaKey::CACHE_INITIALIZED => false,
        /**
         * Plugin meta info.
         */
        BelaKey::OFFICIAL_PAGE => 'http://wordpress.org/plugins/better-extended-live-archive/',
        BelaKey::PROJECT_PAGE => 'https://github.com/charlestang/Better-Extended-Live-Archive',
        BelaKey::ISSUE_TRACKER => 'https://github.com/charlestang/Better-Extended-Live-Archive/issues?state=open',
        /**
         * Display switches.
         */
        BelaKey::SHOW_NEWEST_FIRST => true,
        BelaKey::SHOW_NUMBER_OF_ENTRIES => true,
        BelaKey::SHOW_NUMBER_OF_ENTRIES_PER_TAG => true,
        BelaKey::SHOW_NUMBER_OF_COMMENTS => true,
        BelaKey::INCLUDE_TRACKBACKS => true,
        BelaKey::PAGINATE_THE_LIST => true,
        /**
         * Display options.
         */
        BelaKey::SELECTED_SIGN => '',
        BelaKey::SELECTED_CLASS => 'selected',
        BelaKey::TEMPLATE_NUMBER_OF_ENTRIES => '(%)',
        BelaKey::TEMPLATE_NUMBER_OF_ENTRIES_PER_TAG => '(%)',
        BelaKey::TEMPLATE_NUMBER_OF_COMMENTS => '(%)',
        BelaKey::COMMENTS_CLOSED_SIGN => '',
        BelaKey::POST_DATE_FORMAT_STRING => '', //suggest:m-d,jS,d: etc.
        /**
         * Truncate options.
         */
        BelaKey::MAX_ENTRY_TITLE_LENGTH => 0, // 0 means no truncate
        BelaKey::MAX_CATEGORY_NAME_LENGTH => 0,
        BelaKey::TRUNCATED_TEXT => '...',
        BelaKey::TRUNCATE_BREAK_WORD => false,
        BelaKey::ABBREVIATE_MONTH_NAME => false,
        BelaKey::TAGS_PICK_STRATEGY => BelaKey::STRATEGY_SHOW_ALL,
        BelaKey::STRATEGY_THRESHOLD => 5,
        /**
         * Navigate options.
         */
        BelaKey::NAVIGATION_TABS_ORDER => '27,28', //BelaKey::ORDER_KEY_BY_DATE, BelaKey::ORDER_KEY_BY_CATEGORY
        BelaKey::BY_DATE_TEXT => 'By Date',
        BelaKey::BY_CATEGORY_TEXT => 'By Category',
        BelaKey::BY_TAGS_TEXT => 'By Tag',
        BelaKey::TEXT_BEFORE_CHILD_CATEGORY => '&nbsp;&nbsp;',
        BelaKey::TEXT_AFTER_CHILD_CATEGORY => '',
        BelaKey::TEXT_WHEN_CONTENT_LOADING => 'Loading ...',
        BelaKey::TEXT_WHEN_BLANK_CONTENT => '',
        /**
         * Exclude items
         */
        BelaKey::EXCLUDE_CATEGORY_LIST => array(),
        BelaKey::EXCLUDE_PAGE => true,
        BelaKey::EXCLUDE_POST_TYPE_LIST => array(),
        /**
         * Pagination
         */
        BelaKey::PAGE_OPT_NUMBER_PER_PAGE => 15,
        BelaKey::PAGE_OPT_PREVIOUS_PAGE_TEXT => '&lt;-- Previous',
        BelaKey::PAGE_OPT_NEXT_PAGE_TEXT => 'Next --&gt;',

        BelaKey::EXCLUDED_POST_IDS => array(),
    );

    /**
     * The options of the plugin
     * @var array 
     */
    public $options = null;
    public $meta = null;

    /**
     * Constructor to retrieve the option from db.
     */
    public function __construct() {
        $this->options = get_option(self::OPT_KEY);
        if (false === $this->options) {
            $this->options = $this->defaultOptions;
            $this->save();
        }
    }

    /**
     * @return boolean the cache is initialized or not
     */
    public function isCacheInitialized() {
        return $this->options[BelaKey::CACHE_INITIALIZED];
    }

    /**
     * Get the version of this plugin
     * @param string $key The key of the meta info.
     * @return string the version the this plugin
     */
    public function getPluginMeta($key) {
        if (is_null($this->meta)) {
            $this->meta = get_plugin_data(BELA_ENTRY_FILE, false, false);
        }

        if (!is_array($this->meta) || !isset($this->meta[$key])) {
            return '';
        }

        return $this->meta[$key];
    }

    /**
     * Retrieve the option value
     * @param int $key
     * @return mixed the option value
     */
    public function get($key) {
        return $this->options[$key];
    }

    /**
     * Set the options
     * @param int $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->options[$key] = $value;
    }

    /**
     * Save the options to db.
     */
    public function save() {
        update_option(self::OPT_KEY, $this->options);
    }

}

/**
 * Constants define
 */
class BelaKey {

    const CACHE_INITIALIZED = 1000; //<-- whether the cache is initialized
    //meta
    const OFFICIAL_PAGE = 1;
    const PROJECT_PAGE = 2;
    const ISSUE_TRACKER = 3;
    //stitchs
    const SHOW_NEWEST_FIRST = 4;
    const SHOW_NUMBER_OF_ENTRIES = 5;
    const SHOW_NUMBER_OF_ENTRIES_PER_TAG = 6;
    const SHOW_NUMBER_OF_COMMENTS = 7;
    const INCLUDE_TRACKBACKS = 8;
    const PAGINATE_THE_LIST = 9;
    //display options
    const SELECTED_SIGN = 10;
    const SELECTED_CLASS = 11;
    const TEMPLATE_NUMBER_OF_ENTRIES = 12;
    const TEMPLATE_NUMBER_OF_ENTRIES_PER_TAG = 13;
    const TEMPLATE_NUMBER_OF_COMMENTS = 14;
    const COMMENTS_CLOSED_SIGN = 15;
    const POST_DATE_FORMAT_STRING = 16;
    //truncate
    const MAX_ENTRY_TITLE_LENGTH = 17;
    const MAX_CATEGORY_NAME_LENGTH = 18;
    const TRUNCATED_TEXT = 19;
    const TRUNCATE_BREAK_WORD = 20;
    const ABBREVIATE_MONTH_NAME = 21;
    const TAGS_PICK_STRATEGY = 22;
    const STRATEGY_THRESHOLD = 23;
    //option constants
    const STRATEGY_SHOW_ALL = 24;
    const STRATEGY_SHOW_MOST_USED = 25;
    const STRATEGY_SHOW_MOST_POST = 26;
    //option constants
    const ORDER_KEY_BY_DATE = 27;
    const ORDER_KEY_BY_CATEGORY = 28;
    const ORDER_KEY_BY_TAGS = 29;
    //navigate
    const NAVIGATION_TABS_ORDER = 30;
    const BY_DATE_TEXT = 31;
    const BY_CATEGORY_TEXT = 32;
    const BY_TAGS_TEXT = 33;
    const TEXT_BEFORE_CHILD_CATEGORY = 34;
    const TEXT_AFTER_CHILD_CATEGORY = 35;
    const TEXT_WHEN_CONTENT_LOADING = 36;
    const TEXT_WHEN_BLANK_CONTENT = 37;
    //exclude category
    const EXCLUDE_CATEGORY_LIST = 38;
    const EXCLUDE_PAGE = 39;
    const EXCLUDE_POST_TYPE_LIST = 40;
    //pagination
    const PAGE_OPT_NUMBER_PER_PAGE = 41;
    const PAGE_OPT_NEXT_PAGE_TEXT = 42;
    const PAGE_OPT_PREVIOUS_PAGE_TEXT = 43;

    //excluded result cache
    const EXCLUDED_POST_IDS = 44;

}