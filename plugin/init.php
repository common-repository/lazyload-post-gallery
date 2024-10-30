<?php

/**
 * @package LazyLoad Post Gallery
 * @version 0.1
 */

defined('ABSPATH') or exit();


class slwsu_lazyload_post_gallery_plugin_init {

    /**
     * ...
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'register_qs_lib'));
        
        $this->_init();
    }

    /**
     * ...
     */
    private function _init() {
        if (is_admin()):
            $this->admin_init();
        else:
            $this->front_init();
        endif;
    }

    /**
     * ...
     */
    public function register_qs_lib() {
        wp_register_script('qs_lib', plugins_url('libs/QS.lib.js', __FILE__),  array(), null, true);
        wp_enqueue_script('qs_lib');
    }

    /**
     * ...
     */
    private function admin_init() {
        include_once plugin_dir_path(__FILE__) . 'admin/init.php';
        new slwsu_lazyload_post_gallery_admin_init();
    }

    /**
     * ...
     */
    private function front_init() {
        include_once plugin_dir_path(__FILE__) . 'front/init.php';
        new slwsu_lazyload_post_gallery_front_init();
    }

}
