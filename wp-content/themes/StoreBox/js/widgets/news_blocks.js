(function () {
    "use strict";
    jQuery(document).ready(function () {
        jQuery(document).find('.gk-news-blocks').each(function (i, widget) {
            widget = jQuery(widget);

            if (!widget.hasClass('active')) {
                widget.addClass('active');
                gkNewsBlocksInit(widget);
            }
        });
    });

    var gkNewsBlocksInit = function (widget) {
        widget = jQuery(widget);
        // add the basic events
        widget.find('figure').each(function (i, figure) {
            figure = jQuery(figure);
            var overlay = jQuery('<div class="gk-img-overlay"></div>');
            jQuery(figure.find('figcaption').first()).before(overlay);
            overlay.click(function () {
                window.location.href = jQuery(figure.find('a').first()).attr('href');
            });
        });
    };
})();
// EOF