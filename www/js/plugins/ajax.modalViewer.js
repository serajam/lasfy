(function ($) {
    $.fn.ajaxModalViewer = function (options) {

        // настройки по умолчанию
        var options = jQuery.extend({
            // action urls
            type: 'html',
            clickClass: "",
            modalWindowId: "",
            closeButtonName: "",
            spinnerLink: "",
            ajaxUrl: "",
            useClickHref: false,
            height: 350,
            width: 500,
            callback: '',
            container: false,
            closeOnEscape: true
        }, options);
        var clickObj = '';

        $(options.clickClass).click(function () {

            clickObj = this;
            if (options.container == false) {
                $(options.modalWindowId).html('');
            } else {
                $(options.container).html('');
            }
            var clickObj = this;
            $(options.modalWindowId + ' .errors').html('');

            if (options.ajaxUrl.length < 1) {
                options.ajaxUrl = this.href;
            }
            if (options.useClickHref == true) {
                options.ajaxUrl = this.href;
            }
            $(options.modalWindowId).foundation('reveal', 'open');
            perfomRequest(clickObj);
            return false;
        });
        return false

        function perfomRequest(clickObj) {
            $(options.modalWindowId)
                .html('<img class="spinner" ' + 'src="' + options.spinnerLink + '" alt="" />');
            $.ajax({url: options.ajaxUrl, type: "GET", dataType: options.type,
                complete: function (html, st) {
                    if (st == "success") {
                        if (options.container == false) {
                            $(options.modalWindowId).html(html.responseText);
                        } else {
                            $(options.container).html(html.responseText);
                        }
                        $(options.modalWindowId + ' .spinner').remove();
                        if (typeof(options.callback) == "function") {
                            options.callback(options.clickClass, clickObj);
                        }
                    }
                }
            });
        }
    }
})(jQuery);