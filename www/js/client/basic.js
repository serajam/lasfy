var tagsDelimiters = ',;:\/#\n\t';

// @TODO Tags initialization should be placed here

(function ($) {

  $(window).load(function () {



    //$(document).foundation();
    $(document).foundation('section', {
      deep_linking: true, one_up: true,
      rtl: true
    });
    $(document).foundation({
      tooltip: {
        selector: '.has-tip',
        additional_inheritable_classes: [],
        disable_for_touch: true,
        tooltip_class: '.tooltip',
        tip_template: function (selector, content, id) {
          return '<span data-selector="' + selector + '" id="t' + selector + '" class="'
            + Foundation.libs.tooltip.settings.tooltip_class.substring(1)
            + '" role="tooltip">' + content + '<span class="nub"></span></span>';
        }
      }
    });
    // $(document).foundation('reveal');
    $(document).foundation({dropdown: {is_hover: true}});

    $('.vacanciesContainer, .resumesContainer, .vacanciesJoobleContainer').on('click', 'div.vacancyHead:not(.mail)',
      function () {
      idArray = $(this).attr('id').split('_');
      id = idArray[3];
      prefix = idArray[0];
      if ($('#' + prefix + id).is(':visible')) {
        $('#' + prefix + id).css('overflow', 'hidden');
        $('#' + prefix + id).hide();
        $('#' + prefix + '_plus_Id_' + id).removeClass('hideElem');
        $('#' + prefix + '_plus_Id_' + id).addClass('showElem');
        $('#' + prefix + '_minus_Id_' + id).removeClass('showElem');
        $('#' + prefix + '_minus_Id_' + id).addClass('hideElem');
      }
      else {
        $('#' + prefix + id).css('overflow', 'inherit');
        $('#' + prefix + id).show();
        $('#' + prefix + '_minus_Id_' + id).removeClass('hideElem');
        $('#' + prefix + '_minus_Id_' + id).addClass('showElem');
        $('#' + prefix + '_plus_Id_' + id).removeClass('showElem');
        $('#' + prefix + '_plus_Id_' + id).addClass('hideElem');
      }
      return false;
    });

    $('.langSelect').on('change', function () {
      val = $(this).val();
      hostname = window.location.hostname;
      pathname = window.location.pathname;
      pathname = pathname.split('/');
      pathname[1] = val;
      protocol = window.location.protocol;
      url = protocol + '//' + hostname + pathname.join('/');
      window.location.href = url;
    });

    $().opft(); // open previous tabs

  });

})(jQuery);

function notification(text, type, close, timeout) {
  if (timeout === 0) {
    timeout = 10000;
  }

  var messageHtml = $('<div id="m' + text.length + '" data-alert class="mb10 w95p alert-box ' + type + '">');
  messageHtml.append(text);
  if (close == true) {
    messageHtml.append('<a onclick="$(this.parentNode).remove()" class="close">&times;</a>');
  }

  if (timeout > 1) {
    setTimeout(function () {
      messageHtml.remove();
      //messageHtml.fadeOut();
    }, timeout);
  }
  return messageHtml;
}