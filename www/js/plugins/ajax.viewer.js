(function ($) {
    $.fn.ajaxViewer = function (options) {

        // настройки по умолчанию
        var options = jQuery.extend({
            // action urls
            type: 'html',
            windowId: "",
            spinnerLink: "",
            ajaxUrl: "",
            callback: ''
        }, options);

        perfomRequest();
        function perfomRequest() {
            $(options.windowId)
                .html('<img class="spinner" ' + 'src="' + options.spinnerLink + '" alt="" />');
            $.ajax({url: options.ajaxUrl, type: "GET", dataType: options.type,
                complete: function (html, st) {
                    var ct = html.getResponseHeader("content-type") || "";
                    if (ct.indexOf('html') > -1) {
                        $(options.windowId).html(html.responseText);
                    }
                    else if (ct.indexOf('json') > -1) {
                        var ans_json = eval('(' + html.responseText + ')');
                        $(options.windowId).html(ans_json.error_message);
                    }
                }
            });
        }
    }
})(jQuery);