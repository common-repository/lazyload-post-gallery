/**
 * Du javascript ici !!!
 */
var LAZYLOAD_POST_GALLERY = {};

LAZYLOAD_POST_GALLERY.LAZY = {
    _imgs: [],
    init: function (className) {
        // On met les images class="lazy" dans un array
        this._imgs = $_.cn(className); // = .getElementsByClassName();
        // On lance la fonction lazyLoad()
        this._imgsLoad();
        // console.log(this._gallery);
    },
    _imgsLoad: function () {
        // On parcour les images
        for (var i = 0; i < this._imgs.length; i++) {
            // On test si l'img est partielement visivle
            if (this._imgs[i].attr('data-src')) {
                if (true === this._inViewport(this._imgs[i])) {
                    // data-src to src
                    this._imgs[i].src = this._imgs[i].attr('data-src');
                    this._imgs[i].removeAttribute('data-src');
                    // data-srcset to srcset
                    this._imgs[i].srcset = this._imgs[i].attr('data-srcset');
                    this._imgs[i].removeAttribute('data-srcset');
                }
            }
        }
    },
    _inViewport: function (el) {
        var top = el.offsetTop;
        var left = el.offsetLeft;
        var width = el.offsetWidth;
        var height = el.offsetHeight;

        while (el.offsetParent) {
            el = el.offsetParent;
            top += el.offsetTop;
            left += el.offsetLeft;
        }

        return (
                top < (window.pageYOffset + window.innerHeight) &&
                left < (window.pageXOffset + window.innerWidth) &&
                (top + height) > window.pageYOffset &&
                (left + width) > window.pageXOffset
                );
    }
};

/**
 * Support des lightbox
 */
LAZYLOAD_POST_GALLERY.LIGHTBOX = {
    init: function (options) {
        var lightbox = new Lightbox();
        lightbox.load(options);
    }
};
