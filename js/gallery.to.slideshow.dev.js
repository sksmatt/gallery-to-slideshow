/**
 * Gallery To Slideshow JS
 *
 * @package  Gallery To Slideshow
 * @author   Matt Varone | mattvarone.com
 */
(function ($) {
	"use strict";

	var Gallery = function (gallery) {
		this.gallery = gallery;
		this.init();
	};

	Gallery.prototype = (function () {
		var init = function () {
			var galleryObj = this.gallery;
			this.gallery
				.append("<ul class=\"pager\"></ul>")
				.find(".slides li").each(function (index, item) {
					$(item).clone().removeAttr("style").addClass("thumb").find("p").remove().end().not(":has(a)").find("img").wrap("<a href=\"#\"/>").end().end().find("a").addClass("gallery-to-slideshow-link").end().appendTo(galleryObj.find(".pager")).click(function (e) {
						e.preventDefault();
					});
				}).end().flexslider({
					animation: 'fade',
					slideDirection: 'horizontal',
					slideshowSpeed: 7000,
					controlNav: false,
					keyboardNav: false,
					directionNav: false,
					manualControls: '.pager li a img',
					start: function (slider) {
						galleryObj.find('.pager li').eq(slider.currentSlide).addClass('active').end().find('a').on('click', function (e) {
							e.preventDefault();
							slider.flexAnimate($(this).parent().index());
						});
					},
					after: function (slider) {
						galleryObj.find('.pager li').removeClass('active').eq(slider.currentSlide).addClass('active');
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
}(jQuery));