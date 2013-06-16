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
            that.ajaxRequest({menu: menu_id});

            that.naviClick();
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
