<?php

/**
 * This class used to manage the options of the plugin
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaOptions {

    const OPT_KEY = 'better-extended-live-archive-options';

    public $defaultOptions = array(
        BelaKey::CACHE_INITIALIZED                  => false,
        /**
         * Plugin meta info.
         */
        BelaKey::OFFICIAL_PAGE                      => 'http://wordpress.org/plugins/better-extended-live-archive/',
        BelaKey::PROJECT_PAGE                       => 'https://github.com/charlestang/Better-Extended-Live-Archive',
        BelaKey::ISSUE_TRACKER                      => 'https://github.com/charlestang/Better-Extended-Live-Archive/issues?state=open',
        /**
         * Display switches.
         */
        BelaKey::SHOW_LATEST_FIRST                  => true,
        BelaKey::SHOW_NUMBER_OF_ENTRIES             => true,
        BelaKey::SHOW_NUMBER_OF_ENTRIES_PER_TAG     => true,
        BelaKey::SHOW_NUMBER_OF_COMMENTS            => true,
        BelaKey::EXCLUDE_TRACKBACKS                 => false,
        BelaKey::PAGINATE_THE_LIST                  => true,
        BelaKey::FADE_EVERYTHING                    => true,
        /**
         * Display options.
         */
        BelaKey::SELECTED_SIGN                      => '',
        BelaKey::SELECTED_CLASS                     => 'selected',
        BelaKey::TEMPLATE_NUMBER_OF_ENTRIES         => '(%)',
        BelaKey::TEMPLATE_NUMBER_OF_ENTRIES_PER_TAG => '(%)',
        BelaKey::TEMPLATE_NUMBER_OF_COMMENTS        => '(%)',
        BelaKey::COMMENTS_CLOSED_SIGN               => '',
        BelaKey::POST_DATE_FORMAT_STRING            => '', //suggest:m-d,jS,d: etc.
        /**
         * Truncate options.
         */
        BelaKey::MAX_ENTRY_TITLE_LENGTH             => 0, // 0 means no truncate
        BelaKey::MAX_CATEGORY_NAME_LENGTH           => 0,
        BelaKey::TRUNCATED_TEXT                     => '...',
        BelaKey::TRUNCATE_BREAK_WORD                => false,
        BelaKey::ABBREVIATE_MONTH_NAME              => false,
        BelaKey::TAGS_PICK_STRATEGY                 => BelaKey::TAG_STRATEGY_SHOW_ALL,
        BelaKey::TAG_STRATEGY_THRESHOLD             => 5,
        /**
         * Navigate options.
         */
        BelaKey::NAVIGATION_TABS_ORDER              => array(
            BelaKey::ORDER_KEY_BY_DATE,
            BelaKey::ORDER_KEY_BY_CATEGORY,
        ),
        BelaKey::BY_DATE_TEXT                       => 'By Date',
        BelaKey::BY_CATEGORY_TEXT                   => 'By Category',
        BelaKey::BY_TAGS_TEXT                       => 'By Tag',
        BelaKey::TEXT_BEFORE_CHILD_CATEGORY         => '&nbsp;&nbsp;',
        BelaKey::TEXT_AFTER_CHILD_CATEGORY          => '',
        BelaKey::TEXT_WHEN_CONTENT_LOADING          => 'Loading ...',
        BelaKey::TEXT_WHEN_BLANK_CONTENT            => '',
        /**
         * Exclude items
         */
        BelaKey::EXCLUDE_CATEGORY_LIST              => array(), //term_taxonomy_id's array
        BelaKey::EXCLUDE_PAGE                       => true,
        BelaKey::EXCLUDE_POST_TYPE_LIST             => array(),
        /**
         * Pagination
         */
        BelaKey::PAGE_OPT_NUMBER_PER_PAGE           => 15,
        BelaKey::PAGE_OPT_PREVIOUS_PAGE_TEXT        => '&lt;-- Previous',
        BelaKey::PAGE_OPT_NEXT_PAGE_TEXT            => 'Next --&gt;',
        BelaKey::EXCLUDED_POST_IDS                  => array(),
        /**
         * Appearance
         */
        BelaKey::STYLE_NAME                         => 'default',
    );

    /**
     * Attribute label
     * @return array
     */
    public function getLabels() {
        return array(
            //what to show
            BelaKey::SHOW_LATEST_FIRST                  => __('Show Newest First:', 'bela'),
            BelaKey::SHOW_NUMBER_OF_ENTRIES             => __('Show Number of Entries:', 'bela'),
            BelaKey::SHOW_NUMBER_OF_ENTRIES_PER_TAG     => __('Show Number of Entries Per Tag:', 'bela'),
            BelaKey::SHOW_NUMBER_OF_COMMENTS            => __('Show Number of Comments:', 'bela'),
            BelaKey::FADE_EVERYTHING                    => __('Fade Anything Technique:', 'bela'),
            BelaKey::EXCLUDE_TRACKBACKS                 => __('Hide Ping- and Trackbacks:', 'bela'),
            BelaKey::PAGINATE_THE_LIST                  => __('Layout the posts link into pages:', 'bela'),
            //how to show
            BelaKey::SELECTED_SIGN                      => __('Selected Text:', 'bela'),
            BelaKey::SELECTED_CLASS                     => __('Selected Class:', 'bela'),
            BelaKey::TEMPLATE_NUMBER_OF_ENTRIES         => __('# of Entries Text:', 'bela'),
            BelaKey::TEMPLATE_NUMBER_OF_ENTRIES_PER_TAG => __('# of Tagged-Entries Text:', 'bela'),
            BelaKey::TEMPLATE_NUMBER_OF_COMMENTS        => __('# of Comments Text:', 'bela'),
            BelaKey::COMMENTS_CLOSED_SIGN               => __('Closed Comment Text:', 'bela'),
            BelaKey::POST_DATE_FORMAT_STRING            => __('Day of Posting Format:', 'bela'),
            //how to cut
            BelaKey::MAX_ENTRY_TITLE_LENGTH             => __('Max Entry Title Length:', 'bela'),
            BelaKey::MAX_CATEGORY_NAME_LENGTH           => __('Max Cat. Title Length:', 'bela'),
            BelaKey::TRUNCATED_TEXT                     => __('Truncated Text:', 'bela'),
            BelaKey::TRUNCATE_BREAK_WORD                => __('Truncate at space:', 'bela'),
            BelaKey::ABBREVIATE_MONTH_NAME              => __('Abbreviate month names:', 'bela'),
            BelaKey::TAGS_PICK_STRATEGY                 => __('Displayed tags:', 'bela'),
            BelaKey::TAG_STRATEGY_THRESHOLD             => __('The X in the selected above description:', 'bela'),
            BelaKey::NAVIGATION_TABS_ORDER              => __('Tab Order:', 'bela'),
            BelaKey::BY_DATE_TEXT                       => __('Chronological Tab Text:', 'bela'),
            BelaKey::BY_CATEGORY_TEXT                   => __('By Category Tab Text:', 'bela'),
            BelaKey::BY_TAGS_TEXT                       => __('By Tag Tab Text:', 'bela'),
            BelaKey::TEXT_BEFORE_CHILD_CATEGORY         => __('Before Child Text:', 'bela'),
            BelaKey::TEXT_AFTER_CHILD_CATEGORY          => __('After Child Text:', 'bela'),
            BelaKey::TEXT_WHEN_CONTENT_LOADING          => __('Loading Content:', 'bela'),
            BelaKey::TEXT_WHEN_BLANK_CONTENT            => __('Idle Content:', 'bela'),
            /**
             * Exclude items
             */
            BelaKey::EXCLUDE_CATEGORY_LIST              => __('Select categories:', 'ela'),
            BelaKey::EXCLUDE_PAGE                       => '',
            BelaKey::EXCLUDE_POST_TYPE_LIST             => '',
            /**
             * Pagination
             */
            BelaKey::PAGE_OPT_NUMBER_PER_PAGE           => __('Max # of Posts per page:', 'bela'),
            BelaKey::PAGE_OPT_PREVIOUS_PAGE_TEXT        => __('Previous Page of Posts:', 'bela'),
            BelaKey::PAGE_OPT_NEXT_PAGE_TEXT            => __('Next Page of Posts:', 'bela'),
            /**
             * Appearance
             */
            BelaKey::STYLE_NAME                         => __('Stylesheet:', 'bela'),
            /**
             * Tag Strategy
             */
            BelaKey::TAGS_PICK_STRATEGY                 => __('Displayed tags:', 'bela'),
            BelaKey::TAG_STRATEGY_SHOW_ALL              => __('Show all tags.', 'bela'),
            BelaKey::TAG_STRATEGY_FIRST_X_MOST_USED     => __('Show the first <strong>X</strong> most-used tags.', 'bela'),
            BelaKey::TAG_STRATEGY_TAG_AT_LEAST_X_POST   => __('Show tags with more than <strong>X</strong> posts.', 'bela'),
            /**
             * Menu tabs name 
             */
            BelaKey::ORDER_KEY_BY_DATE                  => $this->get(BelaKey::BY_DATE_TEXT),
            BelaKey::ORDER_KEY_BY_CATEGORY              => $this->get(BelaKey::BY_CATEGORY_TEXT),
            BelaKey::ORDER_KEY_BY_TAGS                  => $this->get(BelaKey::BY_TAGS_TEXT),
        );
    }

    public function getDescriptions() {
        return array(
            //what to show
            BelaKey::SHOW_LATEST_FIRST                  => __('The latest posts should be shown on top of the listings.', 'bela'),
            BelaKey::SHOW_NUMBER_OF_ENTRIES             => __('The number of entries for each year/month/category should be shown.', 'bela'),
            BelaKey::SHOW_NUMBER_OF_ENTRIES_PER_TAG     => __('The number of entries for each tags should be shown.', 'bela'),
            BelaKey::SHOW_NUMBER_OF_COMMENTS            => __('The number of comments for each entry should be shown.', 'bela'),
            BelaKey::FADE_EVERYTHING                    => __('Changes should fade using the Fade Anything.(Not supported now)', 'bela'),
            BelaKey::EXCLUDE_TRACKBACKS                 => __('Trackbacks should influence the number of comments on an entry.', 'bela'),
            BelaKey::PAGINATE_THE_LIST                  => __('The posts list should be cut into several pages or a chunk.', 'bela'),
            //how to show
            BelaKey::SELECTED_SIGN                      => __('The text shown after the selected year, month or category.', 'bela'),
            BelaKey::SELECTED_CLASS                     => __('The CSS class for the selected year, month or category.', 'bela'),
            BelaKey::TEMPLATE_NUMBER_OF_ENTRIES         => __('The string to show for number of entries per year, month or category. 
                                                                    Can contain HTML. % is replaced with number of entries.', 'bela'),
            BelaKey::TEMPLATE_NUMBER_OF_ENTRIES_PER_TAG => __('The string to show for number of entries per tag. Can contain HTML. 
                                                                    % is replaced with number of entries.', 'bela'),
            BelaKey::TEMPLATE_NUMBER_OF_COMMENTS        => __('The string to show for comments. Can contain HTML. 
                                                                    % is replaced with number of comments.', 'bela'),
            BelaKey::COMMENTS_CLOSED_SIGN               => __('The string to show if comments are closed on an entry. 
                                                                    Can contain HTML.', 'bela'),
            BelaKey::POST_DATE_FORMAT_STRING            => __('A date format string to show the day for each entry in the chronological 
                                                                    tab only (\'jS\' to show 1st, 3rd, and 14th). Format string is in 
                                                                    the <a href="http://www.php.net/date" target="blank">php date 
                                                                    format</a>. Reference to year and month in there will result 
                                                                    in error : this intended for days only. 
                                                                    Leave empty to show no date.', 'bela'),
            //how to cut
            BelaKey::MAX_ENTRY_TITLE_LENGTH             => __('Length at which to truncate title of entries. Set to 
                                                                    <strong>0</strong> to leave the titles not truncated.', 'bela'),
            BelaKey::MAX_CATEGORY_NAME_LENGTH           => __('Length at which to truncate name of categories. Set to 
                                                                    <strong>0</strong> to leave the category names not truncated', 'bela'),
            BelaKey::TRUNCATED_TEXT                     => __('The text that will be written after the entries titles and the categories 
                                                                    names that have been truncated. &#8230; (<strong>&amp;#8230;</strong>) 
                                                                    is a common example.', 'bela'),
            BelaKey::TRUNCATE_BREAK_WORD                => __('Sets whether at title should be truncated at the last space before the 
                                                                    length to be truncated to, or if words should be truncated 
                                                                    mid-senten...', 'bela'),
            BelaKey::ABBREVIATE_MONTH_NAME              => __('Sets whether the month names will be abbreviated to three letters.', 'bela'),
            BelaKey::TAGS_PICK_STRATEGY                 => '',
            BelaKey::TAG_STRATEGY_THRESHOLD             => __('Sets depending on the selection made above the number of post per 
                                                                    tag needed to display the tag or the number of most-used tags 
                                                                    to display.', 'bela'),
            BelaKey::NAVIGATION_TABS_ORDER              => '',
            BelaKey::BY_DATE_TEXT                       => __('The text written in the chronological tab.', 'bela'),
            BelaKey::BY_CATEGORY_TEXT                   => __('The text written in the categories tab.', 'bela'),
            BelaKey::BY_TAGS_TEXT                       => __('The text written in the tags tab.', 'bela'),
            BelaKey::TEXT_BEFORE_CHILD_CATEGORY         => __('The text written before each category which is a child of another. 
                                                                    This is recursive.', 'bela'),
            BelaKey::TEXT_AFTER_CHILD_CATEGORY          => __('The text that after each category which is a child of another. 
                                                                    This is recursive.', 'bela'),
            BelaKey::TEXT_WHEN_CONTENT_LOADING          => __('The text displayed when the data are being fetched from the server 
                                                                    (basically when stuff is loading). Can contain HTML.', 'bela'),
            BelaKey::TEXT_WHEN_BLANK_CONTENT            => __('The text displayed when no data are being fetched from the server 
                                                                    (basically when stuff is not loading). Can contain HTML.', 'bela'),
            /**
             * Exclude items
             */
            BelaKey::EXCLUDE_CATEGORY_LIST              => '',
            BelaKey::EXCLUDE_PAGE                       => '',
            BelaKey::EXCLUDE_POST_TYPE_LIST             => '',
            /**
             * Pagination
             */
            BelaKey::PAGE_OPT_NUMBER_PER_PAGE           => __('The max number of posts that will be listed per page.', 'bela'),
            BelaKey::PAGE_OPT_PREVIOUS_PAGE_TEXT        => __('The text written as the link to the previous page.', 'bela'),
            BelaKey::PAGE_OPT_NEXT_PAGE_TEXT            => __('The text written as the link to the next page.', 'bela'),
        );
    }

    public function getLabel($key) {
        $labels = $this->getLabels();
        return isset($labels[$key]) ? $labels[$key] : $key;
    }

    public function getDescription($key) {
        $descriptions = $this->getDescriptions();
        return isset($descriptions[$key]) ? $descriptions[$key] : $key;
    }

    public function getNameAttr($key) {
        return __CLASS__ . '[' . $key . ']';
    }

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
        if (!isset($this->options[$key])) {
            $this->options[$key] = $this->defaultOptions[$key];
        }
        return $this->options[$key];
    }

    /**
     * Set the options
     * @param int $key
     * @param mixed $value
     */
    public function set($key, $value, $writeThrough = false) {
        $this->options[$key] = $value;
        if ($writeThrough) {
            $this->save();
        }
    }

    /**
     * Set options array to the options
     * @param array $optionsArr
     */
    public function setOptions($optionsArr) {
        $optionKeys = array_keys($this->defaultOptions);
        foreach ($optionsArr as $optKey => $optVal) {
            if (in_array($optKey, $optionKeys)) {
                $this->set($optKey, $optVal);
            }
        }
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
    const BELA_VERSION = 1001;
    //meta
    const OFFICIAL_PAGE = 1;
    const PROJECT_PAGE = 2;
    const ISSUE_TRACKER = 3;
    //stitchs
    const SHOW_LATEST_FIRST = 4;
    const SHOW_NUMBER_OF_ENTRIES = 5;
    const SHOW_NUMBER_OF_ENTRIES_PER_TAG = 6;
    const SHOW_NUMBER_OF_COMMENTS = 7;
    const EXCLUDE_TRACKBACKS = 8;
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
    const TAG_STRATEGY_THRESHOLD = 23;
    //option constants
    const TAG_STRATEGY_SHOW_ALL = 24;
    const TAG_STRATEGY_FIRST_X_MOST_USED = 25;
    const TAG_STRATEGY_TAG_AT_LEAST_X_POST = 26;
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
    const FADE_EVERYTHING = 45;
    //appearance
    const STYLE_NAME = 46;
}