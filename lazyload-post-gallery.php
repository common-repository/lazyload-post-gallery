<?php
/*
  Plugin Name: LazyLoad Post Gallery
  Plugin URI: http://web-startup.fr/
  Description: This plugin adds a lightbox to the native galleries of WordPress articles.
  Version: 0.1
  Author: Steeve Lefebvre (slWsu)
  Author URI: web-startup.fr/
  License: GPLv2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: lary
  Contributors: Benoti
  ------------------------------------------------------------------------------
  Note pour les anglophones : quand un code commenté en anglais me plait
  et qu'aucune traduction n'est disponible, je dois me démerder.
  Merci de bien vouloir me rendre la pareille :-þ
 */

//
// http://epicadesign.fr/css3-des-effets-avances-de-survol-dimage/
// https://wpfr.net/support/sujet/resolu-comment-afficher-le-titre-et-non-la-legende-sous-les-miniatures/page/3/
// http://wp-snppts.com/image-gallery-without-plugin
// https://github.com/tevko/wp-tevko-responsive-images/blob/master/wp-tevko-responsive-images.php
// https://css-tricks.com/text-blocks-over-image/

defined('ABSPATH') or exit();

__('LazyLoad Post Gallery', 'lary');
__('This plugin adds a lightbox to the native galleries of WordPress articles.', 'lary');

/**
 * Grouper support
 */
if (is_admin() && 'true' === get_option('slwsu_is_active_grouper', 'false')):
    if (!class_exists('slwsu_grouper_init')):
        require_once WP_PLUGIN_DIR . '/grouper/init.php';
    endif;
    $GROUPER_LAZYLOAD_POST_GALLERY = new slwsu_grouper_init(get_option('slwsu_lazyload_post_gallery_grouper'));
else:
    $GROUPER_LAZYLOAD_POST_GALLERY = false;
endif;

/**
 * Entrée du plugin
 */
class slwsu_lazyload_post_gallery {

    public static $wpVersion = '4';
    public static $phpVersion = '5.5';

    public function __construct() {
        // Hook
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        $wpV = slwsu_lazyload_post_gallery::version_check('wp', self::$wpVersion);
        $phpV = slwsu_lazyload_post_gallery::version_check('php', self::$phpVersion);

        if (false === $wpV or false === $phpV):
            add_action('admin_notices', array($this, 'admin_notice'));
            add_action('admin_init', array($this, 'deactivate_auto'));
        else:
            add_action('plugins_loaded', array($this, 'text_domain'));
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'setting_links'));
            $this->plugin();
        endif;
    }

    /**
     * Plugin
     */
    private function plugin() {
        include_once plugin_dir_path(__FILE__) . 'plugin/init.php';
        new slwsu_lazyload_post_gallery_plugin_init();
    }

    /**
     * 
     */
    public static function version_check($type, $val) {
        if ('wp' === $type):
            $wp_status = slwsu_lazyload_post_gallery::check($GLOBALS['wp_version'], $val);
            return (true === $wp_status) ? true : false;
        elseif ('php' === $type):
            $php_status = slwsu_lazyload_post_gallery::check(phpversion(), $val);
            return (true === $php_status) ? true : false;
        endif;
    }

    /**
     * 
     */
    public static function check($curent, $min) {
        return version_compare($curent, $min) < 0 ? false : true;
    }

    /**
     * Languages
     */
    public static function text_domain() {
        load_plugin_textdomain('lary', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Liens
     */
    public static function setting_links($aLinks) {
        $links[] = '<a href="https://web-startup.fr/lazyload-post-gallery/">' . __('Page', 'lary') . '</a>';
        $links[] = '<a href="' . admin_url('admin.php?page=lazyload-post-gallery') . '">' . __('Settings', 'lary') . '</a>';
        return array_merge($links, $aLinks);
    }

    /**
     * Activation
     */
    public static function activate() {
        $option = slwsu_lazyload_post_gallery::options();
        foreach ($option as $k => $v):
            add_option($k, $v);
        endforeach;
        unset($k, $v);
    }

    /**
     * Désactivation
     */
    public static function deactivate() {
        if ('true' === get_option('slwsu_lazyload_post_gallery_delete_options', 'false')):
            $option = slwsu_lazyload_post_gallery::options();
            foreach ($option as $k => $v):
                delete_option($k);
            endforeach;
            unset($k, $v);
        endif;
    }

    /**
     * Options
     */
    public static function options() {
        include_once plugin_dir_path(__FILE__) . 'plugin/options.php';
        return slwsu_lazyload_post_gallery_options::get_options();
    }

    /**
     * 
     */
    public static function admin_notice() {
        unset($_GET['activate']);
        ?>
        <div id="message" class="error">
            <p>
                <?php
                if (false === slwsu_lazyload_post_gallery::version_check('wp', self::$wpVersion)):
                    printf(__('This Plugin requires at least version of WORDPRESS', 'grp') . ' ' . self::$wpVersion . '+, ' . __('You use version', 'grp') . ' %s.', $GLOBALS['wp_version']);
                endif;
                if (false === slwsu_lazyload_post_gallery::version_check('wp', self::$phpVersion)):
                    printf(__('This Plugin requires at least version of PHP', 'grp') . ' ' . self::$phpVersion . '+, ' . __('You use version', 'grp') . ' %s.', phpversion());
                endif;
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * Désactivation automatique
     */
    public static function deactivate_auto() {
        // On désactive le plugin
        deactivate_plugins(plugin_basename(__FILE__));
    }

}

/**
 *
 */
new slwsu_lazyload_post_gallery();
