'use strict';

/**
 * Global front-end scripts.
 *
 * Global Scripts to run on the front-end.
 */

(function ($) {
	/*
  * Lightbox options
  */
	lightbox.option({
		'disableScrolling': false, // If true, prevent the page from scrolling while Lightbox is open. This works by settings overflow hidden on the body.
		'fadeDuration': 600, // The time it takes for the Lightbox container and overlay to fade in and out, in milliseconds.
		'fitImagesInViewport': true, // If true, resize images that would extend outside of the viewport so they fit neatly inside of it. This saves the user from having to scroll to see the entire image.
		'imageFadeDuration': 600, // The time it takes for the image to fade in once loaded, in milliseconds.
		'resizeDuration': 700 // The time it takes for the Lightbox container to animate its width and height when transition between different size images, in milliseconds.
	});
})(jQuery);