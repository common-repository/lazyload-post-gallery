<?php

/**
 * @package LazyLoad Post Gallery
 * @version 0.1
 */

defined('ABSPATH') or exit();

class slwsu_lazyload_post_gallery_options {
    
    /**
     * ...
     */
    public static function options() {
        $return = [
            // New
            'carousel_title' => 'false',
            'carousel_caption' => 'false',
            'carousel_description' => 'false',
            // Old
            'gallery_title' => 'false',
            'styles' => '',
            // Options plugin
            'delete_options' => 'false',
            'grouper' => 'Grouper'
        ];
        return $return;
    }
    
    /**
     * ...
     */
    public static function get_options() {
        $return = [];
        foreach (self::options() as $k => $v):
            $return['slwsu_lazyload_post_gallery_' . $k] = get_option('slwsu_lazyload_post_gallery_' . $k, $v);
        endforeach;
        unset($k, $v);

        return $return;
    }
    
    /**
     * ...
     */
    public static function get_transient() {
        $return = get_transient('slwsu_lazyload_post_gallery_options');
        return $return;
    }
    
    /**
     * ...
     */
    public static function set_transient($aOptions) {
        set_transient('slwsu_lazyload_post_gallery_options', $aOptions, '');
    }

}
