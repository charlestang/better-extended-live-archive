/**
 * The wrapper of the bela js code
 * @param {jQuery} $
 * @returns {undefined}
 */
jQuery(function($) {
    var BELA = {
        /**
         * the bela wrapper
         */
        container: $('#bela-container'),
        /**
         * the menu bar 
         */
        menuBar: $('#bela-navi-menu'),
        /**
         * the live archive content area
         */
        archiveContent: $('#bela-container div.bela-indices'),
        /**
         * loading tip
         */
        loadingBar: $('#bela-container div.bela-loading'),
        /**
         * Send the ajax request and change the content
         * @param {Object} params
         * @returns {void}
         */
        ajaxRequest: function(params, callback) {
            var that = this, request = $.extend({action: belaAjaxAction}, params);
            that.loading();
            $.getJSON(belaAjaxUrl, request, function(response) {
                if (response.ret === 0) {
                    that.archiveContent.html(response.data);
                    if (callback) {
                        callback();
                    }
                } else {
                    that.archiveContent.html(response.msg);
                }
                that.loadFinish();
            });
        },
        /**
         * Initialize the plugin
         * @returns {undefined}
         */
        init: function() {
            var that = this;
            var menu_id = $('li:first', that.menuBar).addClass('bela-menu-active active').attr('data');
            that.ajaxRequest({menu: menu_id}, function() {
                that.eventBinding();
            });

            that.naviClick();
        },
        eventBinding: function() {
            var that = this;
            that.yearClick();
            that.monthClick();
            that.categoryClick();
            that.tagClick();
            that.preClick();
            that.nextClick();
        },
        yearClick: function() {
            var that = this;
            $('li.year-entry', that.archiveContent).click(function() {
                var entry = $(this),
                        menu_id = entry.attr('menu'),
                        year_id = entry.attr('year');
                that.ajaxRequest({menu: menu_id, year: year_id}, function() {
                    that.eventBinding();
                });
            });
        },
        monthClick: function() {
            var that = this;
            $('li.month-entry', that.archiveContent).click(function() {
                var entry = $(this),
                        menu_id = entry.attr('menu'),
                        year_id = entry.attr('year'),
                        month_id = entry.attr('month');
                that.ajaxRequest({menu: menu_id, year: year_id, month: month_id}, function() {
                    that.eventBinding();
                });
            });
        },
        categoryClick: function() {
            var that = this;
            $('li.category-entry', that.archiveContent).click(function() {
                var entry = $(this),
                        menu_id = entry.attr('menu'),
                        cat_id = entry.attr('cat');
                that.ajaxRequest({menu: menu_id, cat: cat_id}, function() {
                    that.eventBinding();
                });
            });
        },
        tagClick: function() {
            var that = this;
            $('li.tag-entry', that.archiveContent).click(function() {
                var entry = $(this),
                        menu_id = entry.attr('menu'),
                        tag_id = entry.attr('tag');
                that.ajaxRequest({menu: menu_id, tag: tag_id}, function() {
                    that.eventBinding();
                });
            });

        },
        pageNaviEventHandler: function() {
            var $parent = $(this).parent(),
                    page_num = $(this).attr('page'),
                    menu_id = $parent.attr('menu'),
                    year_id = $parent.attr('year'),
                    month_id = $parent.attr('month'),
                    cat_id = $parent.attr('cat'),
                    tag_id = $parent.attr('tag');
            BELA.ajaxRequest({menu: menu_id, year: year_id, month: month_id, cat: cat_id, tag: tag_id, page: page_num}, function() {
                BELA.eventBinding();
            });

        },
        preClick: function() {
            var that = this;
            $('div.bela-pre-page', that.archiveContent).click(that.pageNaviEventHandler);
        },
        nextClick: function() {
            var that = this;
            $('div.bela-next-page', that.archiveContent).click(that.pageNaviEventHandler);
        },
        /**
         * bind the click event to navi tab click
         * @returns {undefined}
         */
        naviClick: function() {
            var that = this;
            $('li', that.menuBar).click(function() {
                var curr = $(this);
                var menu_id = curr.attr('data');
                that.ajaxRequest({menu: menu_id}, function() {
                    $('li', that.menuBar).removeClass('active');
                    curr.addClass('active');
                    that.eventBinding();
                });
            });
        },
        /**
         * Show the loading tips when ajax request
         * @returns {undefined}
         */
        loading: function() {
            this.loadingBar.html(belaLoadingTip);
            this.loadingBar.show();
        },
        /**
         * Hide the loading tips after ajax reqeust
         * @returns {undefined}
         */
        loadFinish: function() {
            this.loadingBar.html(belaIdleTip);
            this.loadingBar.hide();
        }

    };

    BELA.init();
});
