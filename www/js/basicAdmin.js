(function ($) {
    $(window).load(function () {
        $(document).foundation();
        //deep linking for tabs
//        if(window.location.hash){
//            $('dl.tabs dd a').each(function(){
//                var hash = '#' + $(this).attr('href').split('#')[1];
//                if(hash == window.location.hash){
//                    $(this).click();
//                }
//            });
//        }
    });

})(jQuery);

function notification(text, type, close, timeout) {
    if (timeout === 0) {
        timeout = 10000;
    }

    var messageHtml = $('<div id="m' + text.length + '" data-alert class="mb10 alert-box ' + type + '">');
    messageHtml.append(text);
    if (close == true) {
        messageHtml.append('<a onclick="$(this.parentNode).remove()" class="close">&times;</a>');
    }

    if (timeout > 1) {
        setTimeout(function () {
            messageHtml.fadeOut();
        }, timeout);
    }
    return messageHtml;
}

var addTinyMCE = function (elementId) {
    tinyMCE.init({
        selector: '#' + elementId,
        menubar: false,
        statusbar: false,
        plugins: [
            "lists link image charmap preview anchor",
            "fullscreen",
            "insertdatetime code"
        ],
        toolbar: " bold italic | bullist  outdent indent | link image insertdatetime | code preview fullscreen",
        max_height: 200,
        min_height: 160,
        height: 180,
        force_br_newlines : false,
        force_p_newlines : false,
        forced_root_block : ''
        //  content_css : "/design/css/main.css, /design/css/modifications.css"
    });
};

var removeTinyMCE = function (elementId) {
    tinyMCE.get(elementId).remove();
};