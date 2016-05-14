(function ($) {
    $.fn.ajaxModalUrl = function (options) {

        // настройки по умолчанию
        var options = jQuery.extend({
            // action urls
            type: 'json',
            requestType: "GET",
            clickClass: "",
            modalWindowId: "",
            submitButton: "",
            submitButtonSelector: "",
            closeButtonName: "",
            spinnerLink: "",
            ajaxUrl: "",
            useClickHref: false,
            callback: '',
            onClickFunction: '',
            onCompleteFunction: false,
            reload: false,
            data: '',
            closeModal: true
        }, options);

        $(options.clickClass).click(function () {

            if ($(options.submitButton).length) {
                $(options.modalWindowId).append(options.submitButton);
            }
            var clickObj = this;
            $(options.modalWindowId + ' .errors').html('');
            if (typeof(options.onClickFunction) == "function") {
                var res = options.onClickFunction(this);
                if (!res) {
                    return false;
                }
            }
            if (options.ajaxUrl.length < 1) {
                options.ajaxUrl = this.href;
            }
            if (options.useClickHref == true) {
                options.ajaxUrl = this.href;
            }


            $(options.modalWindowId).addClass('reveal-modal');
            $(options.modalWindowId).foundation('reveal', 'open');

            /*       var submitClass = $.trim($(options.submitButton).attr("class"));
             submitClass = submitClass.replace(" ", ".");*/
            $(options.submitButtonSelector).unbind('click');
            $(options.submitButtonSelector).click(function () {
                $(options.modalWindowId)
                    .prepend('<img class="spinner" ' + 'src="' + options.spinnerLink + '" alt="" />');
                $.ajax({url: options.ajaxUrl, type: options.requestType, dataType: options.type, data: options.data,
                    complete: function (html, st) {

                        $('.spinner').remove();
                        if (st == "success") {
                            var ans_json = eval('(' + html.responseText + ')');
                            if (ans_json.error == 'false') {
                                if (typeof(options.callback) == "function") {
                                    options.callback(clickObj, ans_json);
                                }
                                $(options.modalWindowId + ' .errors').html('');
                                $(options.modalWindowId).prepend(notification(ans_json.message, 'success', true, 3000));
                                if (options.reload == true) {
                                    setTimeout(function () {
                                        location.reload();
                                    }, 2000);

                                }
                                if (options.closeModal == true) {
                                    setTimeout(function () {
                                        $(options.modalWindowId).foundation('reveal', 'close');
                                    }, 2000);
                                }
                            } else if (ans_json.error == 'true') {
                                $(options.modalWindowId).prepend(notification(ans_json.error_message, 'alert', true, 3000));
                            }
                            if (typeof(options.onCompleteFunction) == "function") {
                                var res = options.onCompleteFunction(ans_json, this, clickObj);
                                if (!res) {
                                    return false;
                                }
                            }
                        } else {
                            $(options.modalWindowId).prepend(notification('unknown error occurred', 'alert', true, 3000));
                        }
                    }
                });
                return false;
            });
            return false;
        });
    }

})(jQuery);