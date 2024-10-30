<?php
/**
 * @package LazyLoad Post Gallery
 * @version 0.1
 */

defined('ABSPATH') or exit();

include_once plugin_dir_path(__FILE__) . 'form.php';

class slwsu_lazyload_post_gallery_admin_panel {

    /**
     * ...
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'admin_settings'));
    }

    /**
     * ...
     */
    public function admin_menu() {
        global $GROUPER_LAZYLOAD_POST_GALLERY;
        if (is_object($GROUPER_LAZYLOAD_POST_GALLERY)):
            // Grouper
            $GROUPER_LAZYLOAD_POST_GALLERY->add_admin_menu();
            add_submenu_page($GROUPER_LAZYLOAD_POST_GALLERY->grp_id, 'LazyLoad Post Gallery', 'LazyLoad Post Gallery', 'manage_options', 'lazyload-post-gallery', array($this, 'admin_page'));
        else:
            add_menu_page('LazyLoad Post Gallery', 'LazyLoad Post Gallery', 'activate_plugins', 'lazyload-post-gallery', array($this, 'admin_page'));
        endif;
    }

    /**
     * ...
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <?php
            slwsu_lazyload_post_gallery_admin_form::action();
            echo '<h1>LazyLoad Post Gallery</h1>';
            slwsu_lazyload_post_gallery_admin_form::validation();
            slwsu_lazyload_post_gallery_admin_form::message($_POST);
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'options';
            ?>
            <h2 class = "nav-tab-wrapper">
                <a href="?page=lazyload-post-gallery&tab=options" class="nav-tab<?php echo ('options' === $active_tab) ? ' nav-tab-active' : ''; ?>"><?php _e('Options', 'lary'); ?></a>
                <a href="?page=lazyload-post-gallery&tab=styles" class="nav-tab<?php echo ('styles' === $active_tab) ? ' nav-tab-active' : ''; ?>"><?php _e('Styles', 'lary'); ?></a>
                <a href="?page=lazyload-post-gallery&tab=grouper" class="nav-tab<?php echo ('grouper' === $active_tab) ? ' nav-tab-active' : ''; ?>">Grouper</a>
            </h2>

            <form method="post" action="options.php">
                <?php
                if ($active_tab == 'options'):
                    do_settings_sections('slwsu_lazyload_post_gallery_options');
                    settings_fields('slwsu_lazyload_post_gallery_options');
                elseif ($active_tab == 'styles') :
                    do_settings_sections('slwsu_lazyload_post_gallery_styles');
                    settings_fields('slwsu_lazyload_post_gallery_styles');
                elseif ($active_tab == 'grouper') :
                    do_settings_sections('slwsu_lazyload_post_gallery_grouper');
                    settings_fields('slwsu_lazyload_post_gallery_grouper');
                else:
                    echo '<br /> Erreur !';
                endif;

                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     *
     */
    public function admin_settings() {
        // Section plugin
        add_settings_section(
                'slwsu_lazyload_post_gallery_section_plugin', __('Options', 'lary'), array($this, 'section_plugin'), 'slwsu_lazyload_post_gallery_options'
        );

        // ...
        add_settings_field(
                'slwsu_lazyload_post_gallery_add_gallery_title', __('Gallery title', 'lary'), array($this, 'add_gallery_title'), 'slwsu_lazyload_post_gallery_options', 'slwsu_lazyload_post_gallery_section_plugin'
        );
        register_setting(
                'slwsu_lazyload_post_gallery_options', 'slwsu_lazyload_post_gallery_add_gallery_title'
        );

        // ...
        add_settings_field(
                'slwsu_lazyload_post_gallery_add_carousel_title', __('Carousel title', 'lary'), array($this, 'add_carousel_title'), 'slwsu_lazyload_post_gallery_options', 'slwsu_lazyload_post_gallery_section_plugin'
        );
        register_setting(
                'slwsu_lazyload_post_gallery_options', 'slwsu_lazyload_post_gallery_add_carousel_title'
        );

        // ...
        add_settings_field(
                'slwsu_lazyload_post_gallery_add_carousel_caption', __('Carousel caption', 'lary'), array($this, 'add_carousel_caption'), 'slwsu_lazyload_post_gallery_options', 'slwsu_lazyload_post_gallery_section_plugin'
        );
        register_setting(
                'slwsu_lazyload_post_gallery_options', 'slwsu_lazyload_post_gallery_add_carousel_caption'
        );

        // ...
        add_settings_field(
                'slwsu_lazyload_post_gallery_add_carousel_description', __('Carousel description', 'lary'), array($this, 'add_carousel_description'), 'slwsu_lazyload_post_gallery_options', 'slwsu_lazyload_post_gallery_section_plugin'
        );
        register_setting(
                'slwsu_lazyload_post_gallery_options', 'slwsu_lazyload_post_gallery_add_carousel_description'
        );


        
        


        // Section options
        add_settings_section(
                'slwsu_lazyload_post_gallery_section_options', __('Deactivation', 'lary'), array($this, 'section_options'), 'slwsu_lazyload_post_gallery_options'
        );

        // ...
        add_settings_field(
                'slwsu_lazyload_post_gallery_delete_options', __('Delete options', 'lary'), array($this, 'delete_options'), 'slwsu_lazyload_post_gallery_options', 'slwsu_lazyload_post_gallery_section_options'
        );
        register_setting(
                'slwsu_lazyload_post_gallery_options', 'slwsu_lazyload_post_gallery_delete_options'
        );

        // Section styles
        add_settings_section(
                'slwsu_lazyload_post_gallery_section_styles', __('Styles CSS', 'lary'), array($this, 'section_styles'), 'slwsu_lazyload_post_gallery_styles'
        );

        // ...
        add_settings_field(
                'slwsu_lazyload_post_gallery_styles', __('Your styles', 'lary'), array($this, 'add_styles'), 'slwsu_lazyload_post_gallery_styles', 'slwsu_lazyload_post_gallery_section_styles'
        );
        register_setting(
                'slwsu_lazyload_post_gallery_styles', 'slwsu_lazyload_post_gallery_styles'
        );

        /**
         * Support GRP
         */
        if ('true' === get_option('slwsu_is_active_grouper', 'false')):
            // Section grouper
            add_settings_section(
                    'slwsu_lazyload_post_gallery_section_grouper', __('Group', 'lary'), array($this, 'section_grouper'), 'slwsu_lazyload_post_gallery_grouper'
            );
            // ...
            add_settings_field(
                    'slwsu_lazyload_post_gallery_grouper', __('Plugin Group', 'lary'), array($this, 'grouper_nom'), 'slwsu_lazyload_post_gallery_grouper', 'slwsu_lazyload_post_gallery_section_grouper'
            );
            register_setting(
                    'slwsu_lazyload_post_gallery_grouper', 'slwsu_lazyload_post_gallery_grouper'
            );
        else:
            // Section NO grouper
            add_settings_section(
                    'slwsu_lazyload_post_gallery_section_grouper', __('Grouper', 'ptro'), array($this, 'section_grouper_no'), 'slwsu_lazyload_post_gallery_grouper'
            );
        endif;
    }

    /**
     * Plugin
     */
    public function section_plugin() {
        echo __('This section concerns the configuration of the plugin', 'lary') . '&nbsp;<strong><i>LazyLoad Post Gallery</i></strong>';
    }

    public function add_gallery_title() {
        $input = get_option('slwsu_lazyload_post_gallery_add_gallery_title', 'false');
        ?>
        <input name="slwsu_lazyload_post_gallery_add_gallery_title" type="radio" value="true" <?php if ('true' == $input) echo 'checked="checked"'; ?> />
        <span class="description">On</span>
        &nbsp;
        <input name="slwsu_lazyload_post_gallery_add_gallery_title" type="radio" value="false" <?php if ('false' == $input) echo 'checked="checked"'; ?> />
        <span class="description">Off</span>
        &nbsp;-&nbsp;
        <span class="description"><?php echo __('Display title with gallery images.', 'lary'); ?> </span>
        <?php
    }

    public function add_carousel_title() {
        $input = get_option('slwsu_lazyload_post_gallery_add_carousel_title', 'false');
        ?>
        <input name="slwsu_lazyload_post_gallery_add_carousel_title" type="radio" value="true" <?php if ('true' == $input) echo 'checked="checked"'; ?> />
        <span class="description">On</span>
        &nbsp;
        <input name="slwsu_lazyload_post_gallery_add_carousel_title" type="radio" value="false" <?php if ('false' == $input) echo 'checked="checked"'; ?> />
        <span class="description">Off</span>
        &nbsp;-&nbsp;
        <span class="description"><?php echo __('Display title in carousel images.', 'lary'); ?> </span>
        <?php
    }

    public function add_carousel_caption() {
        $input = get_option('slwsu_lazyload_post_gallery_add_carousel_caption', 'false');
        ?>
        <input name="slwsu_lazyload_post_gallery_add_carousel_caption" type="radio" value="true" <?php if ('true' == $input) echo 'checked="checked"'; ?> />
        <span class="description">On</span>
        &nbsp;
        <input name="slwsu_lazyload_post_gallery_add_carousel_caption" type="radio" value="false" <?php if ('false' == $input) echo 'checked="checked"'; ?> />
        <span class="description">Off</span>
        &nbsp;-&nbsp;
        <span class="description"><?php echo __('Display caption in carousel images.', 'lary'); ?> </span>
        <?php
    }

    public function add_carousel_description() {
        $input = get_option('slwsu_lazyload_post_gallery_add_carousel_description', 'false');
        ?>
        <input name="slwsu_lazyload_post_gallery_add_carousel_description" type="radio" value="true" <?php if ('true' == $input) echo 'checked="checked"'; ?> />
        <span class="description">On</span>
        &nbsp;
        <input name="slwsu_lazyload_post_gallery_add_carousel_description" type="radio" value="false" <?php if ('false' == $input) echo 'checked="checked"'; ?> />
        <span class="description">Off</span>
        &nbsp;-&nbsp;
        <span class="description"><?php echo __('Display description in carousel images.', 'lary'); ?> </span>
        <?php
    }

    /**
     * Options
     */
    public function section_options() {
        echo __('This section is about saving plugin options of', 'lary') . '&nbsp;<strong><i>LazyLoad Post Gallery</i></strong>.';
    }

    public function delete_options() {
        $input = get_option('slwsu_lazyload_post_gallery_delete_options');
        ?>
        <input name="slwsu_lazyload_post_gallery_delete_options" type="radio" value="true" <?php if ('true' == $input) echo 'checked="checked"'; ?> />
        <span class="description">On</span>
        &nbsp;
        <input name="slwsu_lazyload_post_gallery_delete_options" type="radio" value="false" <?php if ('false' == $input) echo 'checked="checked"'; ?> />
        <span class="description">Off</span>
        &nbsp;-&nbsp;
        <span class="description"><?php echo __('Delete plugin options when disabling.', 'lary'); ?> </span>
        <?php
    }

    /**
     * Section CSS
     */
    public function section_styles() {
        echo __('This section concerns the CSS styles of', 'lary') . '&nbsp;<strong><i>LazyLoad Post Gallery</i></strong>.';
    }

    public function add_styles() {
        $input = get_option('slwsu_lazyload_post_gallery_styles', '');
        echo '<textarea id="slwsu_lazyload_post_gallery_styles" name="slwsu_lazyload_post_gallery_styles" style="width:100%; height:250px;">' . $input . '</textarea>';
        echo '<p class="description">' . __('Specify here the CSS styles for your\'s galleries.', 'lary');
    }

    /**
     * Support GRP
     */
    public function section_grouper() {
        echo __('This section concerns the Grouper plugin group of', 'lary') . '&nbsp;<strong><i>LazyLoad Post Gallery</i></strong>.';
    }

    public function grouper_nom() {
        $input = get_option('slwsu_lazyload_post_gallery_grouper', 'Grouper');
        echo '<input id="slwsu_lazyload_post_gallery_grouper" name="slwsu_lazyload_post_gallery_grouper" value="' . $input . '" type="text" class="regular-text" />';
        echo '<p class="description">' . __('Specify here the Grouper group to attach', 'lary') . '&nbsp;<strong><i>LazyLoad Post Gallery</i></strong>.</p>';
        echo '<p>' . __('WARNING :: changing the value of this field amounts to modifying the name of the parent link in the WordPress admin menu !', 'lary') . '</p>';
        echo '<p>' . __('You can use this option to isolate this plugin or to add this plugin to an existing Grouper group.', 'lary') . '</p>';
    }

    public function section_grouper_no() {
        echo '<strong><i>LazyLoad Post Gallery</i></strong> ' . __('is compatible with Grouper', 'lary');
        if (file_exists(WP_PLUGIN_DIR . '/grouper')):
            echo '.<br />Grouper ' . __('is installed but does not appear to be enabled', 'lary') . ' : ';
            echo '<a href="plugins.php">' . __('you can activate', 'lary') . ' Grouper</a>';
        else:
            echo ' : <a href="https://web-startup.fr/grouper/" target="_blank">' . __('more information here', 'lary') . '</a>.';
        endif;
    }

}
