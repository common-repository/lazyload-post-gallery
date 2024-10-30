<?php

/**
 * @package LazyLoad Post Gallery
 * @version 0.1
 */

defined('ABSPATH') or exit();

class slwsu_lazyload_post_gallery_front_init {

    /**
     * 
     */
    public function __construct() {
        $this->_init();
    }

    /**
     * 
     */
    private function _init() {
        $this->gallery();
    }

    /**
     * 
     */
    public function gallery() {
        include_once plugin_dir_path(__FILE__) . 'gallery.php';
        $this->assets();
    }

    /**
     * 
     */
    public function assets() {
        include_once plugin_dir_path(__FILE__) . 'assets.php';
        new slwsu_lazyload_post_gallery_front_assets();
    }

}
