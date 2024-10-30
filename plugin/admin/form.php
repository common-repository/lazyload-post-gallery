<?php

/**
 * @package LazyLoad Post Gallery
 * @version 0.1
 */

defined('ABSPATH') or exit();

class slwsu_lazyload_post_gallery_admin_form {

    /**
     * 
     */
    public static function validation() {
        if (isset($_GET['settings-updated'])) {
            delete_transient('slwsu_lazyload_post_gallery_options');
            ?>
            <div id="message" class="updated">
                <p><strong><?php echo __('Settings saved', 'lary') ?></strong></p>
            </div>
            <?php
        }
    }

    /**
     * 
     */
    public static function action() {
        ?>
        <a class="lazyload-post-gallery-modal-link" style="text-decoration:none; font-weight:bold;" href="#openModal"><?php echo __('About', 'lary'); ?> <span class="dashicons dashicons-info"></span></a>
        <?php
    }

    /**
     * 
     * @global type $current_user
     * @param type $post
     */
    public static function message($post) {
        ?>
        <div id="openModal" class="lazyload-post-gallery-modal">
            <div>
                <a href="#lazyload-post-gallery-modal-close" title="Close" class="lazyload-post-gallery-modal-close"><span class="dashicons dashicons-dismiss"></span></a>
                <h2><?php echo __('About', 'lary'); ?></h2>
                <p><span class="dashicons dashicons-admin-users"></span> <?php echo __('By', 'lary'); ?> <?php echo 'Steeve Lefebvre - slWsu'; ?> - <a style="text-decoration:none;" href="<?php echo 'https://www.facebook.com/groups/285005131924566/'; ?>" target="_blank"><span class="dashicons dashicons-facebook"></span></a></p>
                <p><span class="dashicons dashicons-admin-site"></span> <?php echo __('More information', 'lary'); ?> : <a href="<?php echo 'https://web-startup.fr/lazyload-post-gallery/'; ?>" target="_blank"><?php _e('plugin page', 'lary'); ?></a></p>
                <p><span class="dashicons dashicons-admin-tools"></span> <?php echo __('Development for the web', 'lary'); ?> : HTML, PHP, JS, WordPress</p>
                <h2><?php echo __('Support', 'lary'); ?></h2>
                <p><span class="dashicons dashicons-email-alt"></span> <?php echo __('Ask your question', 'lary'); ?></p>
                <?php
                if (isset($post['submit'])) {
                    global $current_user; $to = 'steeve.lfbvr@gmail.com'; $subject = "Support Grouper !!!";
                    $roles = implode(", ", $current_user->roles);
                    $message = "From: " . get_bloginfo('name') . " - " . get_bloginfo('home') . " - " . get_bloginfo('admin_email') . "\n";
                    $message .= "By : " . strip_tags($post['nom']) . " - " . $post['email'] . " - " . $roles . "\n";
                    $message .= strip_tags($post['message']) . "\n";
                    if (wp_mail($to, $subject, $message)):
                        echo '<p class="lazyload-post-gallery-contact-valide"><strong>' . __('Your message has been sent !', 'lary') . '</strong></p>';
                    else:
                        echo '<p class="lazyload-post-gallery-contact-error">' . __('Something went wrong, go back and try again !', 'lary') . '</p>';
                    endif;
                }
                ?>
                <form id="lazyload-post-gallery-contact" action="" method="post">
                    <fieldset>
                        <input id="nom" name="nom" type="text" placeholder="<?php echo __('Your name', 'lary'); ?>" required="required">
                    </fieldset>
                    <fieldset>
                        <input id="email" name="email" type="email" placeholder="<?php echo __('Your Email Address', 'lary'); ?>" required="required">
                    </fieldset>
                    <fieldset>
                        <textarea id="message" name="message" placeholder="<?php echo __('Formulate your support request or feature proposal here...', 'lary'); ?>" required="required"></textarea>
                    </fieldset>
                    <fieldset>
                        <input id="submit" name="submit" type="submit" value="<?php echo __('Send', 'lary'); ?>" class="button button-primary" type="submit" id="lazyload-post-gallery-contact-submit" data-submit="...Sending" />
                    </fieldset>
                </form>
            </div>
        </div>
        <?php
    }

}
