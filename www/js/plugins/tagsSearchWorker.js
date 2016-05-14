/**
 * This plugin fills the search field in the form and makes
 * the ajax's request to get the ads by the tags.
 *
 * @author Alexey Kagarlykskiy
 * @copyright   Copyright (c) 2013-2015 Studio 105 (http://105.in.ua)
 */
(function ($) {
    $.fn.tagsSearchWorker = function (options) {
        var options = jQuery.extend({
            tagObj: '',
            tagsContainer: '',
            requestAtOnce: false,
            searchButton: '',
            searchFormId: '',
            searchUrlRequest: false,
            baseHost: '',
            applyTemplateFunction: '',
            spinnerLink: false,
            closeLink: false
        }, options);

        $(options.tagObj).css("cursor", "pointer");
        $(options.tagObj).click(function () {
            tag = $(this).text();
            $(options.tagsContainer).tagEditor('addTag', tag, true);
            if (options.requestAtOnce == true) {
                if (options.searchUrlRequest != false) {
                    $(options.searchFormId).attr('action', options.searchUrlRequest);
                }
                if (options.spinnerLink != false) {
                    $(options.tagsContainer).parent()
                        .append('<img class="spinnerRound" ' + 'src="' + options.spinnerLink + '" alt="" />');
                    $('img#closeIcon').remove();
                }
                $(options.searchFormId).ajaxSubmit({
                    dataType: 'json',
                    success: function (responseText) {

                        if (typeof(options.applyTemplateFunction) == "function") {
                            options.applyTemplateFunction(responseText);
                        }
                        $('img.spinnerRound').remove();
                        if (!$('img#closeIcon').length && options.closeLink != false && $(options.tagsContainer).tagEditor('getTags')[0].tags.length) {
                            $(options.tagsContainer).parent()
                                .append('<img id="closeIcon" ' + 'src="' + options.closeLink + '" alt="" />');
                        }

                        return false;
                    },
                    complete: function (responseText) {
                        if (responseText.error == 'true') {
                            // @todo show pretty error
                            alert('<?php echo __("access_error") ?>');
                            $('img.spinnerRound').remove();
                            $(options.searchFormId).attr('action', mainHost + 'default/search/get-ads');
                            if (!$('img#closeIcon').length && options.closeLink != false && $(options.tagsContainer).tagEditor('getTags')[0].tags.length) {
                                $('#searchTags').parent()
                                    .append('<img id="closeIcon" ' + 'src="' + options.closeLink + '" alt="" />');
                            }
                        }

                        return false;
                    }
                });
            }
            return false;
        });
        return this;
    };
})(jQuery);