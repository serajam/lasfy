/**
 * This plugin stores in cookies the id (sign) of opened tab
 * and than it opens previous tab when user returns to the page.
 * First we use it with Foundation tabs.
 * Set the id for your tabs container, f.e.
 * <ul class="tabs" data-tab="'" id="setTabs">
 *
 * @author Alexey Kagarlykskiy
 * @copyright   Copyright (c) 2013-2015 Studio 105 (http://105.in.ua)
 */
(function ($) {
    $.fn.opft = function (options) {
        var options = jQuery.extend({
            tabsClass: '.tabs',
            clickElem: 'a',
            clickElemAttrib: 'href',
            clickElemSign: '#panel',
            comparison: '^=',
            cookiesExdays: 30
        }, options);

        tab = options.clickElem + '[' + options.clickElemAttrib + options.comparison + '"' +
        options.clickElemSign + '"]';

        $(options.tabsClass).find(tab).click(function () {
            id = $(this).parent().parent().attr('id');
            setCookie(id + '-tab', $(this).attr('href'), options.cookiesExdays);
        });

        var setCookie = function (cName, cVal, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            document.cookie = cName + "=" + cVal + "; " + expires;
        };

        var getCookie = function (cName) {
            var name = cName + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1);
                if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
            }
            return "";
        };

        cookName = $(options.tabsClass).find(tab).parent().parent().attr('id');
        openedTab = getCookie(cookName + '-tab');
        if($(options.tabsClass).is(':visible')){
            $(options.tabsClass).find(options.clickElem + '[' + options.clickElemAttrib +
            options.comparison + '"' + openedTab + '"]').click();
        }

    };
})(jQuery);