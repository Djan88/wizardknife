/**
 * jQuery Cookie plugin
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
(function () {
    "use strict";
    jQuery.cookie = function (key, value, options) {

        // key and at least value given, set cookie...
        if (arguments.length > 1 && String(value) !== "[object Object]") {
            options = jQuery.extend({}, options);

            if (value === null || value === undefined) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var days = options.expires,
                    t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            value = String(value);

            return (document.cookie = [
                encodeURIComponent(key), '=',
                options.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join(''));
        }

        // key and possibly options given, get cookie...
        options = value || {};
        var result, decode = options.raw ? function (s) {
                return s;
            } : decodeURIComponent;
        return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
    };

    // Array filter
    if (!Array.prototype.filter) {
        Array.prototype.filter = function (fun /*, thisp */ ) {
            if (this === null) {
                throw new TypeError();
            }

            var t = Object(this);
            var len = t.length >>> 0;
            if (typeof fun !== "function") {
                throw new TypeError();
            }

            var res = [];
            var thisp = arguments[1];

            for (var i = 0; i < len; i++) {
                if (i in t) {
                    var val = t[i]; // in case fun mutates this
                    if (fun.call(thisp, val, i, t))
                        res.push(val);
                }
            }

            return res;
        };
    }

    /**
     *
     * Template scripts
     *
     **/

    // onDOMLoadedContent event
    jQuery(document).ready(function () {
        // Thickbox use
        jQuery(document).ready(function () {
            if (typeof tb_init !== "undefined") {
                tb_init('div.wp-caption a'); //pass where to apply thickbox
            }
        });
        // style area
        if (jQuery('#gk-style-area')) {
            jQuery('#gk-style-area div').each(function () {
                jQuery(this).find('a').each(function () {
                    jQuery(this).click(function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        changeStyle(jQuery(this).attr('href').replace('#', ''));
                    });
                });
            });
        }
        // Function to change styles

        function changeStyle(style) {
            var file = $GK_TMPL_URL + '/css/' + style;
            jQuery('head').append('<link rel="stylesheet" href="' + file + '" type="text/css" />');
            jQuery.cookie($GK_TMPL_NAME + '_style', style, {
                expires: 365,
                path: '/'
            });
        }

        // Responsive tables
        jQuery('article section table').each(function (i, table) {
            table = jQuery(table);
            var heads = table.find('thead th');
            var cells = table.find('tbody td');
            var heads_amount = heads.length;
            // if there are the thead cells
            if (heads_amount) {
                var cells_len = cells.length;
                for (var j = 0; j < cells_len; j++) {
                    var head_content = jQuery(heads.get(j % heads_amount)).text();
                    jQuery(cells.get(j)).html('<span class="gk-table-label">' + head_content + '</span>' + jQuery(cells.get(j)).html());
                }
            }
        });

        // login popup
        if (jQuery('#gk-popup-login') || jQuery('#btn-cart')) {
            var popup_overlay = jQuery('#gk-popup-overlay');
            popup_overlay.css({
                'opacity': '0',
                'display': 'block'
            });
            popup_overlay.fadeOut();

            var opened_popup = null;

            if (jQuery('#gk-popup-login')) {
                var popup_login = null;
                var popup_login_h = null;
                var popup_cart = null;
                var popup_cart_h = null;

                popup_login = jQuery('#gk-popup-login');
                popup_login.css({
                    'opacity': 0,
                    'display': 'block'
                });
                popup_login.animate({
                    'opacity': 0,
                    'height': 0
                }, 200);

                jQuery('.gklogin').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    popup_overlay.fadeTo(200, 0.45);
                    popup_login_h = popup_login.find('.gk-popup-wrap').outerHeight();
                    popup_login.animate({
                        'opacity': 1,
                        'height': popup_login_h
                    }, 200);
                    opened_popup = 'login';
                });
            }

            if (jQuery('#btn-cart')) {
                var btn = jQuery('#btn-cart');

                jQuery(window).scroll(function () {
                    var scroll = jQuery(window).scrollTop();
                    var max = jQuery('.gk-page-wrapper').height();
                    var final = 0;
                    if (scroll > 70) {
                        if (scroll < max - 122) {
                            final = scroll - 50;
                        } else {
                            final = max - 172;
                        }
                    } else {
                        final = 20;
                    }
                    btn.css('top', final + "px");
                });

                popup_cart = jQuery('#gk-popup-cart');
                popup_cart.css({
                    'opacity': 0,
                    'display': 'block'
                });
                popup_cart.animate({
                    'opacity': 0,
                    'height': 0
                }, 200);

                jQuery('#btn-cart').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    popup_overlay.fadeTo(200, 0.45);
                    popup_cart_h = popup_cart.find('.gk-popup-wrap').outerHeight();
                    popup_cart.animate({
                        'opacity': 1,
                        'height': popup_cart_h
                    }, 200);
                    opened_popup = 'cart';
                    jQuery('#btn-cart').addClass('loading');
                    setTimeout(function () {
                        jQuery('#btn-cart').removeClass('loading');
                    }, 1000);
                });
            }

            popup_overlay.click(function () {
                if (opened_popup === 'login') {
                    popup_overlay.fadeOut();
                    popup_login.animate({
                        'opacity': 0,
                        'height': 0
                    }, 200);
                } else if (opened_popup === 'cart') {
                    popup_overlay.fadeOut();
                    popup_cart.animate({
                        'opacity': 0,
                        'height': 0
                    }, 200);
                }
            });
        }

        //product zoom
        jQuery(document).find('.woocommerce-main-image.zoom').each(function (i, el) {
            el = jQuery(el);
            el.find('img').each(function (i, img) {
                img = jQuery(img);

                if (img.width() > 80) {
                    var zoomicon = jQuery('<div class="gk-wc-zoom"><span></span></div>');
                    var parent = img.parent();
                    var text = jQuery('.images.wc-image-zoom').attr('data-zoomtext');
                    var span = jQuery('<span class="zoom-text"></span>');
                    span.text(text);

                    parent.append(zoomicon);
                    zoomicon.find('span').append(span);

                    parent.mouseenter(function () {
                        if (img.height() > 80) {
                            zoomicon.attr('class', 'gk-wc-zoom active');
                        }
                    });
                    parent.mouseleave(function () {
                        zoomicon.attr('class', 'gk-wc-zoom');
                    });
                }
            });
        });

        jQuery(document).find('.post-type-archive-product .wc-product-overlay, .tax-product_cat .wc-product-overlay, .tax-product_tag .wc-product-overlay').each(function (i, el) {
            el = jQuery(el);
            el.find('img').each(function (i, img) {
                img = jQuery(img);

                if (img.width() > 80) {
                    var zoomicon = jQuery('<div class="gk-wc-view"><span></span></div>');
                    var parent = img.parent();
                    var text = jQuery('.wc-product-overlay').attr('data-viewtext');
                    var span = jQuery('<span></span>').text(text);

                    parent.append(zoomicon);
                    zoomicon.find('span').append(span);

                    parent.mouseenter(function () {
                        if (img.height() > 80) {
                            zoomicon.attr('class', 'gk-wc-view active');
                            el.addClass('active');
                        }
                    });
                    parent.mouseleave(function () {
                        zoomicon.attr('class', 'gk-wc-view');
                        el.removeClass('active');
                    });
                }
            });
        });
    });

    // Image Show
    jQuery(window).load(function () {
        setTimeout(function () {
            jQuery(".gk-is-wrapper-gk-storebox").each(function (i, el) {
                var wrapper = jQuery(el);
                var $G = [];
                var slides = [];
                var links = [];
                var imagesToLoad = [];
                var loadedImages = 0;
                var swipe_min_move = 30;
                var swipe_max_time = 500;
                // animation variables
                $G.animation_timer = false;
                $G.anim_speed = wrapper.attr('data-speed');
                $G.anim_interval = wrapper.attr('data-interval');
                $G.autoanim = wrapper.attr('data-autoanim');
                // blank flag
                $G.blank = false;
                // load the images
                wrapper.find('.gk-is-slide').each(function (i, el) {
                    el = jQuery(el);
                    var newImg = jQuery('<img title="' + el.text() + '" class="' + el.attr('class') + '" style="' + el.attr('data-style') + '" src="' + el.attr('data-url') + '" data-num="' + i + '" />');
                    links[i] = el.attr('data-link');
                    imagesToLoad.push(newImg);
                    el.after(newImg);
                    el.remove();
                });

                var time = setInterval(function () {
                    var process = 0;

                    jQuery(imagesToLoad).each(function (i, img) {
                        if (img[0].complete) {
                            process++;
                        }
                    });

                    if (process === imagesToLoad.length) {
                        clearInterval(time);
                        loadedImages = process;
                        setTimeout(function () {
                            wrapper.find('.gk-is-preloader').fadeOut();
                        }, 400);
                    }
                }, 200);

                var time_main = setInterval(function () {
                    if (loadedImages) {
                        clearInterval(time_main);
                        $G.actual_slide = 0;

                        wrapper.animate({
                                'height': wrapper.find('figure').height()
                            },
                            350,
                            function () {
                                var firstSlide = wrapper.find('figure').first();
                                firstSlide.addClass('active');
                                firstSlide.animate({
                                    'opacity': 1
                                }, 350);
                                wrapper.css('height', 'auto');
                            }
                        );

                        wrapper.addClass('loaded');

                        wrapper.find(".gk-is-slide").each(function (i, elmt) {
                            slides[i] = jQuery(elmt);
                        });

                        setTimeout(function () {
                            var initfig = slides[0].parent().find('figcaption');
                            if (initfig) {
                                initfig.animate({
                                    'margin-top': 0,
                                    'opacity': 1
                                }, 250);
                            }
                        }, 250);

                        wrapper.find('.gk-is-overlay').click(function () {
                            window.location = links[$G.actual_slide];
                        });
                        wrapper.find('.gkIsOverlay').css('cursor', 'pointer');

                        // auto-animation
                        if ($G.autoanim === 'on') {
                            $G.animation_timer = setTimeout(function () {
                                gk_storebox_autoanimate($G, wrapper, 'next', null);
                            }, $G.anim_interval);
                        }

                        // prev / next
                        if (wrapper.find('.gk-is-prev').length) {
                            wrapper.find('.gk-is-prev').click(function (e) {
                                e.preventDefault();
                                e.stopPropagation();
                                $G.blank = true;
                                gk_storebox_autoanimate($G, wrapper, 'prev', null);
                            });

                            wrapper.find('.gk-is-next').click(function (e) {
                                e.preventDefault();
                                e.stopPropagation();
                                $G.blank = true;
                                gk_storebox_autoanimate($G, wrapper, 'next', null);
                            });

                            var slide_pos_start_x = 0;
                            var slide_pos_start_y = 0;
                            var slide_time_start = 0;
                            var slide_swipe = false;

                            wrapper.bind('touchstart', function (e) {
                                slide_swipe = true;
                                var touches = e.originalEvent.changedTouches || e.originalEvent.touches;

                                if (touches.length > 0) {
                                    slide_pos_start_x = touches[0].pageX;
                                    slide_pos_start_y = touches[0].pageY;
                                    slide_time_start = new Date().getTime();
                                }
                            });

                            wrapper.bind('touchmove', function (e) {
                                var touches = e.originalEvent.changedTouches || e.originalEvent.touches;

                                if (touches.length > 0 && slide_swipe) {
                                    if (
                                        Math.abs(touches[0].pageX - slide_pos_start_x) > Math.abs(touches[0].pageY - slide_pos_start_y)
                                    ) {
                                        e.preventDefault();
                                    } else {
                                        slide_swipe = false;
                                    }
                                }
                            });

                            wrapper.bind('touchend', function (e) {
                                var touches = e.originalEvent.changedTouches || e.originalEvent.touches;

                                if (touches.length > 0 && slide_swipe) {
                                    if (
                                        Math.abs(touches[0].pageX - slide_pos_start_x) >= swipe_min_move &&
                                        new Date().getTime() - slide_time_start <= swipe_max_time
                                    ) {
                                        if (touches[0].pageX - slide_pos_start_x > 0) {
                                            $G.blank = true;
                                            gk_storebox_autoanimate($G, wrapper, 'prev', null);
                                        } else {
                                            $G.blank = true;
                                            gk_storebox_autoanimate($G, wrapper, 'next', null);
                                        }
                                    }
                                }
                            });
                        }

                        // events
                        wrapper.mouseenter(function () {
                            wrapper.addClass('hover');
                        });

                        wrapper.mouseleave(function () {
                            wrapper.removeClass('hover');
                        });
                    }
                }, 250);
            });
        }, 2000);
    });

    var gk_storebox_animate = function ($G, wrapper, imgPrev, imgNext) {
        imgPrev = jQuery(imgPrev);
        imgNext = jQuery(imgNext);
        var prevfig = imgPrev.find('figcaption');
        //
        if (prevfig) {
            prevfig.animate({
                'margin-top': 50,
                'opacity': 0
            }, 150);
        }
        //
        imgNext.attr('class', 'animated');
        imgPrev.animate({
                'opacity': 0,
            },
            $G.anim_speed,
            function () {
                imgPrev.attr('class', '');
            }
        );
        //
        imgNext.animate({
                'opacity': 1
            },
            $G.anim_speed,
            function () {
                imgNext.attr('class', 'active');
                var nextfig = imgNext.find('figcaption');
                if (nextfig) {
                    nextfig.animate({
                        'margin-top': 0,
                        'opacity': 1
                    }, 150);
                }
                if ($G.autoanim === 'on') {
                    clearTimeout($G.animation_timer);

                    $G.animation_timer = setTimeout(function () {
                        if ($G.blank) {
                            $G.blank = false;
                            clearTimeout($G.animation_timer);

                            $G.animation_timer = setTimeout(function () {
                                gk_storebox_autoanimate($G, wrapper, 'next', null);
                            }, $G.anim_interval);
                        } else {
                            gk_storebox_autoanimate($G, wrapper, 'next', null);
                        }
                    }, $G.anim_interval);
                }
            }
        );
    };

    var gk_storebox_autoanimate = function ($G, wrapper, dir, next) {
        var i = $G.actual_slide;
        var imgs = wrapper.find('figure');

        if (next === null) {
            next = (dir === 'next') ? ((i < imgs.length - 1) ? i + 1 : 0) : ((i === 0) ? imgs.length - 1 : i - 1); // dir: next|prev
        }

        gk_storebox_animate($G, wrapper, imgs[i], imgs[next]);
        $G.actual_slide = next;
    };
})();
// EOF