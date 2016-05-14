(function ($) {
  $.fn.modalMesssage = function (options) {

    // настройки по умолчанию
    var options = jQuery.extend({
      // action urls
      closeButtonName: 'Ok',
      modalWindowId: '',
      message: "",
      height: 250,
      width: 350,
      show: null,
      modal: false,
      showCloseButton: true,
      type: 'success'
    }, options);

    $(options.modalWindowId + ' ' + '.message').html(notification(options.message, options.type,
      options.showCloseButton));
    $(options.modalWindowId).foundation('reveal', {
      animation: 'FadeAndPop',
      animationSpeed: 50,
      css: {
        open: {
          'opacity': 1,
          'z-index': 10001
        }
      }
    });
    $(options.modalWindowId).foundation('reveal', 'open');
    return false;
  }
})(jQuery);