(function ($) {
    $.fn.ajaxFormSubmitter = function (options) {

        // настройки по умолчанию
        var options = jQuery.extend({
            // action urls
            subForm: false,
            beforeSubmitFunc: '',
            afterSubmitFunc: '',
            messageWindow: "",
            spinnerLink: "",
            useClickUrl: false,
            formId: "",
            redirectUrl: false,
            reload: true,
            response: '',
            messageAppend: true,
            modalMessage: false
        }, options);

        var messageWindow = options.messageWindow;
        var formId = options.formId;

        $(formId).ajaxForm({
            beforeSubmit: function (q, $form) {
                if (options.messageWindow == 'form') {
                    messageWindow = '#' + $($form).prop('id');
                    formId = '#' + $($form).prop('id');
                }
                $(messageWindow)
                    .prepend('<img class="spinner" ' + 'src="' + options.spinnerLink + '" alt="" />');
            },
            beforeSerialize: function () {
                if (typeof(options.beforeSubmitFunc) == "function") {
                    options.beforeSubmitFunc(this);
                }
            },
            dataType: 'json',
            success: function (responseText) {

                var ans_json = responseText;
                options.response = ans_json;
                //   var ans_json = eval('(' + responseText.responseText + ')');
                $('.spinner').remove();
                $('.ajax-errors, .message').html('');

                if (ans_json.formMessages) {
                    var numerator = 0;
                    $.each(ans_json.formMessages, function (i, item) {
                        var err = '<ul class="ajax-errors">';
                        if (numerator == 0) {
                            if (i == 'files') {
                                $(formId + ' .' + i).focus();
                            } else {
                                $(formId + ' #' + i).focus();
                            }
                        }
                        numerator = numerator + 1;
                        $.each(item, function (erri, errText) {
                            err += '<li>' + errText + '</li>';
                        });
                        err += '</ul>';
                        if (i == 'files') {
                            $(formId + ' .' + i).parent().append(err);
                        } else {
                            if (options.subForm == true) {
                                $.each(item, function (subI, subFormElement) {
                                    addErrors(subI, subFormElement, i);
                                });
                            } else {
                                addErrors(i, item);
                            }
                        }

                    });
                }

                if (ans_json.message) {
                    message(ans_json.message, 'success');
                }

                if (ans_json.error == 'true' && ans_json.error_message.length > 2) {
                    var messageId = 'm' + ans_json.error_message.length;
                    if (!$(messageWindow + ' #' + messageId).length) {
                        message(ans_json.error_message, 'alert');
                    }
                }
                if (typeof(options.afterSubmitFunc) == "function") {
                    options.afterSubmitFunc(this, options, ans_json);
                }
                if (ans_json.error == 'false') {
                    if (ans_json['data'] && ans_json['data']['redirect']) {
                        location.href = ans_json['data']['redirect'];
                        return false;
                    }

                    if (options.redirectUrl) {
                        location.href = options.redirectUrl;
                    }
                    else if (options.redirectUrl == false && options.reload == true) {
                        setTimeout(function () {
                            location.reload();
                        }, 2000);

                    }
                }
                return false;
            }
        });

        var message = function (message, type) {
            if (!message) {
                return false;
            }

            if (!options.modalMessage) {
                if (options.messageAppend == true) {
                    $(messageWindow).prepend(notification(message, type, false, 3000));
                } else {
                    $(messageWindow).prepend(notification(message, type, true, 3000));
                }
            }
            if (options.modalMessage) {
                closeAfter = 2000;
                if (type == 'alert') {
                    closeAfter = 15000
                }
                $().modalMesssage({
                    closeButtonName: 'Ok',
                    modalWindowId: '#success-message-window',
                    message: message,
                    type: type,
                    closeAfter: closeAfter
                });
            }
        }

        var addErrors = function (id, item, subForm) {
            var err = '<ul class="ajax-errors">';
            $(formId + ' #' + id).addClass('error');

            if (typeof item === 'string') {
                err += '<li>' + item + '</li>';
            }
            else {
                $.each(item, function (erri, errText) {
                    err += '<li>' + errText + '</li>';
                });
            }
            err += '</ul>';

            if ("undefined" != typeof subForm) {
                $(formId + ' #' + subForm + '-' + id).parent().append(err);
            } else {
                $(formId + ' #' + id).parent().append(err);
            }
        }

        return false;
    }
})(jQuery);