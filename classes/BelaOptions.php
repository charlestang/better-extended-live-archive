<?php

/**
 * This class used to manage the options of the plugin
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaOptions {

    const OPT_KEY = 'better-extended-live-archive-options';

    public static $defaultOptions = array(
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
        BelaKey::MAX_ENTRY_TITLE_LENGTH => 0,  // 0 means no truncate
        BelaKey::MAX_CATEGORY_NAME_LENGTH => 0,
        BelaKey::TRUNCATED_TEXT => '...',
        BelaKey::TRUNCATE_BREAK_WORD => false,
        BelaKey::ABBREVIATE_MONTH_NAME => false,
        BelaKey::TAGS_PICK_STRATEGY => BelaKey::STRATEGY_SHOW_ALL,
        BelaKey::STRATEGY_THRESHOLD => 5,
        /**
         * Navigate options.
         */
        
        
    );

}


/**
 * Constants define
 */
class BelaKey {
    const OFFICIAL_PAGE = 1;
    const PROJECT_PAGE  = 2;
    const ISSUE_TRACKER = 3;

    const SHOW_NEWEST_FIRST = 4;
    const SHOW_NUMBER_OF_ENTRIES = 5;
    const SHOW_NUMBER_OF_ENTRIES_PER_TAG = 6;
    const SHOW_NUMBER_OF_COMMENTS = 7;
    const INCLUDE_TRACKBACKS = 8;
    const PAGINATE_THE_LIST = 9;

    const SELECTED_SIGN = 10;
    const SELECTED_CLASS = 11;
    const TEMPLATE_NUMBER_OF_ENTRIES = 12;
    const TEMPLATE_NUMBER_OF_ENTRIES_PER_TAG = 13;
    const TEMPLATE_NUMBER_OF_COMMENTS = 14;
    const COMMENTS_CLOSED_SIGN = 15;
    const POST_DATE_FORMAT_STRING = 16;
    
    const MAX_ENTRY_TITLE_LENGTH = 17;
    const MAX_CATEGORY_NAME_LENGTH = 18;
    const TRUNCATED_TEXT = 19;
    const TRUNCATE_BREAK_WORD = 20;
    const ABBREVIATE_MONTH_NAME = 21;
    const TAGS_PICK_STRATEGY = 22;
    const STRATEGY_THRESHOLD = 23;

    const STRATEGY_SHOW_ALL = 24;
    const STRATEGY_SHOW_MOST_USED = 25;
    const STRATEGY_SHOW_MOST_POST = 26;

    

    

}