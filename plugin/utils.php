<?php

/**
 * @package LazyLoad Post Gallery
 * @version 0.1
 */

defined('ABSPATH') or exit();

class slwsu_lazyload_post_gallery_utils {
    
    /**
     * 
     * @param type $str
     * @param type $sep
     * @param type $charset
     * @return type
     */
    public static function str_to_id($str, $sep = null, $charset = 'utf-8') {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

        if (null !== $sep):
            $str = preg_replace('# #', $sep, $str); // On remplace les espaces
        endif;


        $str = strtolower($str);

        return $str;
    }

}
