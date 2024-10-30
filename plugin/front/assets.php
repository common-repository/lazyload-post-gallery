<?php

/**
 * @package LazyLoad Post Gallery
 * version 0.1
 */

defined('ABSPATH') or exit();

class slwsu_lazyload_post_gallery_front_assets {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'register_css'));
        add_action('wp_enqueue_scripts', array($this, 'register_js'));
        add_action('wp_footer', array($this, 'footer_script'), 100);
    }

    public function register_css() {
        global $post;
        if (has_shortcode($post->post_content, 'gallery')) :
            wp_register_style('lary_front', plugins_url('assets/front.css', __FILE__), false);
            wp_enqueue_style('lary_front');
        endif;
    }

    public function register_js() {
        global $post;
        if (has_shortcode($post->post_content, 'gallery')) :
            wp_register_script('lary_lightbox', plugins_url('assets/lightbox.js', __FILE__), null, '', true);
            wp_enqueue_script('lary_lightbox');
            wp_register_script('lary_front', plugins_url('assets/front.js', __FILE__), null, '', true);
            wp_enqueue_script('lary_front');
        endif;
    }

    function footer_script() {
        global $post;
        if (has_shortcode($post->post_content, 'gallery')) :
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    LAZYLOAD_POST_GALLERY.LAZY.init('lazyload-post-gallery-lazy');
                    document.onscroll = function () {
                        setTimeout(function () {
                            LAZYLOAD_POST_GALLERY.LAZY.init('lazyload-post-gallery-lazy');
                        }, 1000);
                    };

                    /* https://github.com/felixhagspiel/jsOnlyLightbox */
                    var options = {
                        boxId: false,
                        dimensions: true,
                        captions: true,
                        prevImg: true,
                        nextImg: true,
                        hideCloseBtn: false,
                        closeOnClick: true,
                        loadingAnimation: 200,
                        animElCount: 4,
                        preload: true,
                        carousel: true,
                        animation: 400,
                        nextOnClick: true,
                        responsive: true,
                        maxImgSize: 0.8,
                        keyControl: true,
                        // callbacks
                        onopen: function () {
                            // ...
                        },
                        onclose: function () {
                            // ...
                        },
                        onload: function (event) {
                            // ...
                        },
                        onresize: function () {
                            // ...
                        },
                        onloaderror: function (event) {
                            // ...
                        }
                    };
                    LAZYLOAD_POST_GALLERY.LIGHTBOX.init(options);
                });
            </script>
            <?php

        endif;
    }

}
