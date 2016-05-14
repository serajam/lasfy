(function ($) {
  $.fn.ajaxRequester = function (options) {

    // default options
    var options = jQuery.extend({
      // action urls
      url: "",
      postFunc: null,
      notice: true,
      noticeEl: false,
      successCallback: '',
      modal: false,
      reload: false,
      dataType: 'json',
      data: false
    }, options);

    $.ajax({
      url: options.url, type: "GET", dataType: options.dataType, data: options.data,
      complete: function (html, st) {
        if (st == "success") {
          if (options.dataType == 'json') {
            var ans_json = eval('(' + html.responseText + ')');
          }
          else {
            var ans_json = html.responseText;
          }
          if (typeof(options.successCallback) == "function") {
            options.successCallback(this, ans_json);
          }
          if (ans_json.error == 'false') {

            if (options.notice) {
              $().modalMesssage({
                closeButtonName: 'Ok',
                modalWindowId: '#success-message-window',
                message: ans_json.message,
                type: 'success',
                modal: options.modal,
                showCloseButton: false,
              });
            }

            if (options.noticeEl) {
              $(options.noticeEl).prepend(notification(ans_json.message, 'success'));
            }

            if (options.reload == true) {
              setTimeout(function () {
                location.reload();
              }, 2000);
            }
          }
          else if (ans_json.error == 'true') {
            if (options.notice) {
              $().modalMesssage({
                closeButtonName: 'Ok',
                modalWindowId: '#error-message-window',
                message: ans_json.error_message,
                type: 'alert',
                modal: options.modal,
                closeAfter: 5000,
                showCloseButton: false,
              });
            }
            if (options.noticeEl) {
              $(options.noticeEl).prepend(notification(ans_json.error_message, 'alert'));
            }
          }

          if (typeof(options.postFunc) == "function") {
            options.postFunc(this, ans_json);
          }
        }
      }
    });
    return false
  }
})(jQuery);