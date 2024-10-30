<?php

/**
 * @package LazyLoad Post Gallery
 * @version 0.1
 */
defined('ABSPATH') or exit();

class slwsu_lazyload_post_gallery_admin_assets {

    public function __construct() {
        if (isset($_GET['page']) && 'lazyload-post-gallery' === $_GET['page']):
            add_action('admin_enqueue_scripts', array($this, 'register_css'));
            add_action('admin_enqueue_scripts', array($this, 'register_js'));
        endif;
    }

    public function register_css() {
        wp_register_style('lary_admin', plugins_url('assets/admin.css', __FILE__));
        wp_enqueue_style('lary_admin');
    }

    public function register_js() {
        wp_register_script('lary_admin', plugins_url('assets/admin.js', __FILE__), null, '', true);
        wp_enqueue_script('lary_admin');
    }

}
