/**
 * Created by alexius on 21.11.14.
 */
(function($){
    $.fn.tagsMaker = function(options){
        var options = JQuery.extend({
            tagsContainerClass: 'tagsContainer',
            eventSource: true
        }, options);

        return this;
    };
})(jQuery);