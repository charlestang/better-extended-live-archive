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
        ajaxRequest: function(params) {
            var that = this, request = $.extend({action: belaAjaxAction}, params);
            that.loading();
            $.getJSON(belaAjaxUrl, request, function(response) {
                if (response.ret === 0) {
                    that.archiveContent.html(response.data);
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
            var menu_id = $('li:first', that.menuBar).addClass('bela-menu-active').attr('data');
            that.ajaxRequest({menu: menu_id});
        },
        loading: function() {
            this.loadingBar.html(belaLoadingTip);
            this.loadingBar.show();
        },
        loadFinish: function() {
            this.loadingBar.html(belaIdleTip);
            this.loadingBar.hide();
        }

    };

    BELA.init();
});