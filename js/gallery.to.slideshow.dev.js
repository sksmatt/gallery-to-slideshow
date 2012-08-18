/**
 * Gallery To Slideshow JS
 *
 * @package  Gallery To Slideshow
 * @author   Matt Varone | mattvarone.com
 */
var mv_gallery_to_slideshow_js_params;
(function ($, params) {
    "use strict";

    var Gallery = function (gallery) {
        this.gallery = gallery;
        this.init();
    };

    Gallery.prototype = (function () {
        var init = function () {
            var galleryObj = this.gallery;
            
            if ( params.thumbnails === "1" ) {
                this.gallery
                    .append("<ul class=\"pager\"></ul>")
                    .find(".slides li").each(function (index, item) {
                        $(item)
                            .clone()
                            .removeAttr("style")
                            .addClass("thumb")
                            .find("p")
                            .remove()
                        .end()
                            .not(":has(a)")
                                .find("img")
                                .wrap("<a href=\"#\"/>")
                            .end()
                        .end()
                            .find("a")
                            .addClass("gallery-to-slideshow-link")
                        .end()
                            .appendTo(galleryObj.find(".pager")).click(function (e) {
                            e.preventDefault();
                        });
                    }).end();
            }
            this.gallery.flexslider({
                    slideshow: (params.slideshow === "1") ? true : false,
                    animation: 'fade',
                    slideDirection: 'horizontal',
                    slideshowSpeed: ( params.slideshowSpeed) ? parseInt(params.slideshowSpeed,10) : 7000,
                    animationDuration: ( params.animationDuration) ? parseInt(params.animationDuration,10) : 600,
                    mousewheel: (params.mousewheel === "1") ? true : false,
                    controlNav: (params.controlNav === "1") ? true : false,
                    keyboardNav: (params.keyboardNav === "1") ? true : false,
                    directionNav: (params.directionNav === "1") ? true : false,
                    manualControls: (params.manualControls) ? params.manualControls : '.pager li a img',
                    prevText: params.prevText,
                    nextText: params.nextText,
                    pausePlay: (params.pausePlay === "1") ? true : false,
                    pauseText: params.pauseText,
                    randomize: (params. randomize === "1") ? true : false,
                    slideToStart: (params.slideToStart) ?  parseInt(params.slideToStart,10) : 0,
                    animationLoop: (params.animationLoop === "1") ? true : true,
                    pauseOnAction: (params.pauseOnAction === "1") ? true : true,
                    pauseOnHover: (params.pauseOnHover === "1") ? true : false,
                    controlsContainer: (params.controlsContainer) ? params.controlsContainer : '',
                    start: function (slider) {
                        if ( params.thumbnails === "1" ) {
                            galleryObj
                                .find('.pager li')
                                .eq(slider.currentSlide)
                                .addClass('active')
                            .end()
                                .find('a')
                                .on('click', function (e) {
                                    e.preventDefault();
                                    slider.flexAnimate($(this).parent().index());
                                });
                        }
                    },
                    after: function (slider) {
                        if ( params.thumbnails === "1" ) {
                                galleryObj.
                                    find('.pager li')
                                    .removeClass('active')
                                    .eq(slider.currentSlide)
                                    .addClass('active');
                        }
                    }
                });
        };
        return {
            init: init
        };
    }());

    if ($.isFunction($.fn.flexslider)) {
        $("div.gallery-to-slideshow").each(function (index, item) {
            var gallery = new Gallery($(item));
        });
    }
}(jQuery, mv_gallery_to_slideshow_js_params));