(function ($) {
    $.fn.ajaxModalFormSubmitter = function (options) {
        // настройки по умолчанию
        var options = jQuery.extend({
            // action urls
            subForm: false,
            clickObj: "",
            modalWindowId: "",
            closeButtonName: "",
            spinnerLink: "",
            useClickUrl: false,
            formId: "",
            width: 500,
            height: 500,
            redirectUrl: false,
            reload: true,
            clearBeforeStart: true,
            editor: false,
            formPath: '',
            lthis: false,
            showEffect: 'fade',
            simpleSubmit: false,
            resetForm: true,
            beforeSubmitFunc: '',
            onClickFunction: '',
            onCloseFunction: '',
            onEditorLoadFunc: '',
            modal: true,
            clearModal: true,
            editorElementId: false,
            isHTMLEditor: false,
            editorLanguage: 'en',
            afterSubmitFunc: false,
            messagesTimeOut: null,
            messagesClose: true
        }, options);

        $(options.clickObj).click(function (event) {
            event.preventDefault();
            options.lthis = this;
            options.formPath = options.modalWindowId + ' ' + options.formId;
            if (options.resetForm == true) {
                $(options.formId).resetForm();
                $(options.formId).clearForm(1);
                $(options.formId + ' input[type="hidden"]').val('');
            }

            $(options.modalWindowId + ' .errors, .ajax-errors').html('');
            if (options.useClickUrl == true) {
                $(options.modalWindowId + ' ' + options.formId).attr('action', this.href);
            }
            $(options.modalWindowId + ' .alert-box').remove();
            $(options.modalWindowId + ' .spinner').remove();
            $(options.modalWindowId + ' .error').removeClass('error');
            $(options.modalWindowId + ' .errors').removeClass('errors');
            $(options.modalWindowId + ' .errorLight').removeClass('errorLight');
            //    $(options.modalWindowId + ' #').trigger('change', true);

            $(options.modalWindowId).addClass('reveal-modal');
            $(options.modalWindowId).foundation('reveal', 'open');
            if (options.isHTMLEditor) {
              addTinyMCE(options.editorElementId, options.editorLanguage);
            }
            if (typeof(options.onClickFunction) == "function") {
                options.onClickFunction(this);
            }

            if (options.simpleSubmit == true) {
                addLoader();
                $(options.modalWindowId + ' ' + options.formId).ajaxForm({
                    dataType: 'json',
                    success: function (responseText, st) {
                        var ans_json = responseText;
                        //   var ans_json = eval('(' + responseText.responseText + ')');
                        $(options.modalWindowId + ' .spinner').remove();
                        $(options.modalWindowId + ' .error').removeClass('error');
                        $(options.modalWindowId + ' .errors').removeClass('errors');
                        $(options.modalWindowId + ' .errorLight').removeClass('errorLight');

                        if (ans_json.error == 'true') {
                            $('.ui-dialog-buttonset button.ui-button:nth-child(1)', modWin).removeAttr('disabled');
                            var messageId = 'm' + ans_json.error_message.length;
                            if (!$(options.modalWindowId + ' #' + messageId).length) {
                                $(options.modalWindowId).prepend(
                                    notification(ans_json.error_message, 'alert', options.messagesClose, options.messagesTimeOut));
                            }
                        }

                        if (ans_json.formMessages) {
                            $(options.modalWindowId + ' .ajax-errors').remove();
                            $.each(ans_json.formMessages, function (i, item) {
                                if (options.subForm == true) {
                                    $.each(item, function (subI, subFormElement) {
                                        addErrors(subI, subFormElement, i);
                                    });
                                } else {
                                    addErrors(i, item);
                                }
                            });
                        }
                        if (ans_json.error == 'false') {
                            $(options.formPath).submit();
                            return false;
                        }
                        return false;
                    },
                    error: function () {
                        $(options.modalWindowId + ' .spinner').remove();
                        $(options.modalWindowId).prepend(
                            notification('unknown error occurred', 'alert', options.messagesClose, options.messagesTimeOut));
                    }
                });
            } else {
                addLoader();
                var modWin = $(options.modalWindowId).parent();
                $('.ui-dialog-buttonset button.ui-button:nth-child(1)', modWin).attr('disabled', 'disabled');
                $(options.modalWindowId + ' ' + options.formId).ajaxForm({
                    dataType: 'json',
                    beforeSerialize: function () {
                        if (typeof(options.beforeSubmitFunc) == "function") {
                            options.beforeSubmitFunc(this);
                        }
                    },
                    beforeSubmit: function () {
                        $(options.modalWindowId)
                            .prepend('<img class="spinner" ' + 'src="' + options.spinnerLink + '" alt="" />');
                    },
                    success: function (responseText) {
                        var ans_json = responseText;
                        //   var ans_json = eval('(' + responseText.responseText + ')');
                        $(options.modalWindowId + ' .spinner').remove();
                        $(options.modalWindowId + ' .error').removeClass('error');
                        $(options.modalWindowId + ' .errors').removeClass('errors');
                        $(options.modalWindowId + ' .errorLight').removeClass('errorLight');

                        if (typeof(options.afterSubmitFunc) == "function") {
                            options.afterSubmitFunc(options, ans_json);
                        }

                        if (ans_json.error == 'true') {
                            $('.ui-dialog-buttonset button.ui-button:nth-child(1)', modWin).removeAttr('disabled');
                            var messageId = 'm' + ans_json.error_message.length;
                            if (!$(options.modalWindowId + ' #' + messageId).length) {
                                $(options.modalWindowId).prepend(notification(ans_json.error_message, 'alert', options.messagesClose, options.messagesTimeOut));
                            }
                        }

                        if (ans_json.formMessages) {
                            $(options.modalWindowId + ' .ajax-errors').remove();
                            $.each(ans_json.formMessages, function (i, item) {
                                if (options.subForm == true) {
                                    $.each(item, function (subI, subFormElement) {
                                        addErrors(subI, subFormElement, i);
                                    });
                                } else {
                                    addErrors(i, item);
                                }
                            });
                        }

                        if (ans_json.message) {
                            $(options.modalWindowId).prepend(notification(ans_json.message, 'success', options.messagesClose, options.messagesTimeOut));
                        }

                        if (ans_json.error == 'false') {
                            if (!ans_json.message.length && ans_json.error == 'false') {
                                $(options.modalWindowId + ' .spinner').remove();
                                $(options.modalWindowId).prepend(notification('No data', 'alert', options.messagesClose, options.messagesTimeOut));
                                return false;
                            }
                            if (ans_json['data'] && ans_json['data']['redirect']) {
                                location.href = ans_json['data']['redirect'];
                            }
                            if (options.redirectUrl == false && options.reload == true) {
                                setTimeout(function () {
                                    location.reload();
                                }, 1200);

                            } else if (options.reload == false) {
                                setTimeout(function () {
                                        $(options.modalWindowId).foundation('reveal', 'close');
                                    }, 1200
                                );
                            } else {
                                setTimeout(function () {
                                    location.href = options.redirectUrl;
                                }, 1200);
                            }
                            if (options.clearModal == true) {
                            }

                        }
                        return false;
                    }
                });
                return false;
            }
            return false;
        });

        var addLoader = function () {
            if (options.editor == true) {
                $(options.modalWindowId)
                    .prepend('<img class="spinner" ' + 'src="' + options.spinnerLink + '" alt="" />');
                $(options.formPath).css('display', 'none');
                editor();
            } else {
                $(options.formPath).css('display', 'block');
            }
        };

        var addErrors = function (id, item, subForm) {
            var err = '<ul class="ajax-errors errors">';
            $(options.modalWindowId + ' #' + id).addClass('errorLight');
            if (id === 'tags') {
                $(options.modalWindowId + ' .tag-editor').addClass('errorLight');
            }
            $.each(item, function (erri, errText) {
                err += '<li>' + errText + '</li>';
            });
            err += '</ul>';

            if ("undefined" != typeof subForm) {
                $(options.formPath + ' #' + subForm + '-' + id).parent().append(err);
            } else {
                $(options.formPath + ' #' + id).parent().append(err);
            }
        };

        var addValue = function (formInput, item) {
            if (formInput.length < 1) {
                return true;
            }
            if (formInput.is('select')) {
                if ($.isArray(item)) {
                    $.each(item, function (i, opt) {
                        $(formInput.selector + ' option[value="' + opt + '"]').prop('selected', 'selected');
                    });
                } else {
                    $(formInput.selector + ' option[value="' + item + '"]').prop('selected', 'selected');
                }
                $(formInput.selector).trigger('change', true);
            } else if (formInput.is('input')) {
                if (formInput.attr("type") == 'checkbox') {
                    if (item == '0') {
                        formInput.prop('checked', false);
                    } else {
                        formInput.prop('checked', true);
                    }
                } else if (formInput.attr("type") == 'password') {

                } else {
                    (formInput).val(item);
                }
            } else {
                formInput.val(item);
                if (formInput.is('textarea') && options.isHTMLEditor) {
                    if($.isArray(options.editorElementId)) {
                        key = $.inArray(formInput.attr('id'), options.editorElementId);
                        tinyMCE.get(options.editorElementId[key]).setContent($('#' + options.editorElementId[key]).val());
                    } else {
                        tinyMCE.get(options.editorElementId).setContent($('#' + options.editorElementId).val());
                    }
                }

                /* var id = $(formInput).prop('id');
                 if (formInput.is('textarea')){
                 tinyMCE.get(id).setContent($('#' + id).val());
                 }*/
            }
        };

        var editor = function () {
            $(options.modalWindowId + ' .errors')
                .html('<img class="spinner" ' + 'src="' + options.spinnerLink + '">');
            $.ajax({url: options.lthis.href, type: "GET", dataType: "json",
                success: function (json, st) {
                    var ansJson = json;
                    $(options.formPath + ' .errors').html('');
                    $(options.modalWindowId + ' .spinner').remove();
                    if (ansJson.error == 'true') {
                        $().modalMesssage({
                            closeButtonName: 'Ok',
                            modalWindowId: '#error-message-window',
                            message: ansJson.error_message,
                            type: 'error'
                        });
                        return false;
                    }
                    if (typeof(ansJson.message) == 'undefined' && ansJson.error == 'false') {
                        $(options.modalWindowId).prepend(notification('No data', 'alert', options.messagesClose, options.messagesTimeOut));
                        return false;
                    }

                    if (typeof(ansJson.data) == 'undefined') {
                        $(options.modalWindowId).prepend(notification('No data', 'alert', options.messagesClose, options.messagesTimeOut));
                        return false;
                    }

                    $.each(ansJson.data, function (i, item) {
                        if (options.subForm) {
                            $.each(item, function (itemId, subItemValue) {
                                var formInput = $(options.formPath + ' #' + i + '-' + itemId);
                                addValue(formInput, subItemValue);
                            });

                        } else {
                            var formInput = $(options.formPath + ' #' + i);
                            addValue(formInput, item);
                        }
                    });
                    if (typeof(options.onEditorLoadFunc) == "function") {
                        options.onEditorLoadFunc(this, ansJson.data);
                    }
                    $(options.formPath).css('display', 'block');

                }
            });
        }
    }
})(jQuery);