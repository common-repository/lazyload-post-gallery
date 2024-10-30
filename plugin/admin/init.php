<?php

/**
 * @package LazyLoad Post Gallery
 * @version 0.1
 */

defined('ABSPATH') or exit();

class slwsu_lazyload_post_gallery_admin_init {

    /**
     * 
     */
    public function __construct() {
        $this->admin_page();
        $this->assets();
    }

    /**
     * 
     */
    public function admin_page() {
        include_once plugin_dir_path(__FILE__) . 'panel.php';
        new slwsu_lazyload_post_gallery_admin_panel();

        if (isset($_GET['page']) && 'lazyload-post-gallery' === $_GET['page']):
            add_filter('admin_footer_text', array($this, 'remove_footer_admin'));
        endif;
    }

    /**
     * 
     */
    public function remove_footer_admin() {
        $html = '';

        $html .= __('Developed by', 'lary') . ' <a href="ttps://web-startup.fr/" target="_blank" />Web-StartUp</a>';
        $html .= ' ';
        $html .= __('for', 'lary') . ' <a href="http://www.wordpress.org" target="_blank">WordPress</a>';
        $html .= ' - ';
        $html .= '<a href="https://web-startup.fr/lazyload-post-gallery/" target="_blank" />' . __('More information', 'lary') . '</a>';

        echo $html;
    }

    /**
     * 
     */
    public function assets() {
        include_once plugin_dir_path(__FILE__) . 'assets.php';
        new slwsu_lazyload_post_gallery_admin_assets();
    }

}
