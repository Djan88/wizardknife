(function () {
    "use strict";
    jQuery(document).ready(function () {
        jQuery(document).find('.gk-product-gallery').each(function (i, widget) {
            widget = jQuery(widget);

            if (!widget.hasClass('active')) {
                widget.addClass('active');
                gkProductGalleryInit(widget);
            }
        });
    });

    var gkProductGalleryInit = function (widget) {
        widget = jQuery(widget);
        // set the basic widget variables
        widget.attr('data-current', 1);
        widget.attr('data-blank', 0);
        widget.attr('data-stop', 0);
        widget.attr('data-pages', Math.ceil(widget.find('.gk-image').length / widget.attr('data-cols')));

        // check if buttons exists
        if (widget.find('.gk-prev-btn')) {
            widget.find('.gk-prev-btn').click(function (e) {
                e.preventDefault();
                widget.attr('data-blank', 1);
                gkProductGalleryAnim(widget, 'prev');
            });

            widget.find('.gk-next-btn').click(function (e) {
                e.preventDefault();
                widget.attr('data-blank', 1);
                gkProductGalleryAnim(widget, 'next');
            });

            var arts_pos_start_x = 0;
            var arts_pos_start_y = 0;
            var arts_time_start = 0;
            var arts_swipe = false;

            widget.bind('touchstart', function (e) {
                arts_swipe = true;
                var touches = e.originalEvent.changedTouches || e.originalEvent.touches;

                if (touches.length > 0) {
                    arts_pos_start_x = touches[0].pageX;
                    arts_pos_start_y = touches[0].pageY;
                    arts_time_start = new Date().getTime();
                }
            });

            widget.bind('touchmove', function (e) {
                var touches = e.originalEvent.changedTouches || e.originalEvent.touches;

                if (touches.length > 0 && arts_swipe) {
                    if (
                        Math.abs(touches[0].pageX - arts_pos_start_x) > Math.abs(touches[0].pageY - arts_pos_start_y)
                    ) {
                        e.preventDefault();
                    } else {
                        arts_swipe = false;
                    }
                }
            });

            widget.bind('touchend', function (e) {
                var touches = e.originalEvent.changedTouches || e.originalEvent.touches;

                if (touches.length > 0 && arts_swipe) {
                    if (
                        Math.abs(touches[0].pageX - arts_pos_start_x) >= 30 &&
                        new Date().getTime() - arts_time_start <= 500
                    ) {
                        if (touches[0].pageX - arts_pos_start_x > 0) {
                            widget.attr('data-blank', 1);
                            gkProductGalleryAnim(widget, 'prev');
                        } else {
                            widget.attr('data-blank', 1);
                            gkProductGalleryAnim(widget, 'next');
                        }
                    }
                }
            });
        }

        // check if autoanimation is enabled
        if (widget.hasClass('gk-auto-anim')) {
            setTimeout(function () {
                gkProductGalleryAutoAnim(widget);
            }, widget.attr('data-autoanim-time'));
        }

        // add stop event
        widget.find('.gk-image').each(function (i, img) {
            img = jQuery(img);

            img.mouseenter(function () {
                widget.attr('data-stop', 1);
                img.addClass('hover');
            });

            img.mouseleave(function () {
                widget.attr('data-stop', 0);
                img.removeClass('hover');
            });
        });

        widget.find('.gk-img-overlay').each(function (i, img) {
            img = jQuery(img);

            img.click(function () {
                window.location.href = jQuery(img.parent().find('h4 a').first()).attr('href');
            });
        });
    };

    var gkProductGalleryAutoAnim = function (widget) {
        if (widget.attr('data-blank') === 1 || widget.attr('data-stop') === 1) {
            setTimeout(function () {
                widget.attr('data-blank', 0);
                gkProductGalleryAutoAnim(widget);
            }, widget.attr('data-autoanim-time'));
        } else {
            gkProductGalleryAnim(widget, 'next');

            setTimeout(function () {
                gkProductGalleryAutoAnim(widget);
            }, widget.attr('data-autoanim-time'));
        }
    };

    var gkProductGalleryAnim = function (widget, dir) {
        // amount of news per page
        var perPage = widget.attr('data-cols');
        var current = widget.attr('data-current') * 1.0;
        var allPages = widget.attr('data-pages');
        var next = 0;
        // select next page
        if (dir === 'next') {
            if (current == allPages) {
                next = 1;
            } else {
                next = current + 1;
            }
        } else if (dir === 'prev') {
            if (current == 1) {
                next = allPages;
            } else {
                next = current - 1;
            }
        }
        // set the current page
        widget.attr('data-current', next);
        // hide current elements
        widget.find('.gk-image').each(function (i, img) {
            img = jQuery(img);

            if (img.hasClass('active')) {
                gkProductGalleryImgClass(img, 'active', false, 0);
                gkProductGalleryImgClass(img, '', true, 300);
            }
        });
        // show next elements	
        setTimeout(function () {
            widget.find('.gk-image').each(function (i, img) {
                img = jQuery(img);

                if (i >= (next - 1) * perPage && i < (next * perPage)) {
                    gkProductGalleryImgClass(img, 'active', false, 0);
                    gkProductGalleryImgClass(img, 'active show', true, 300);
                }
            });
        }, 300);
    };

    var gkProductGalleryImgClass = function (img, className, delay, time) {
        if (!delay) {
            img.attr('class', 'gk-image ' + className);
        } else {
            setTimeout(function () {
                img.attr('class', 'gk-image ' + className);
            }, time);
        }
    };
})();