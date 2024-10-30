<?php

/**
 * @package LazyLoad Post Gallery
 * @version 0.1
 */
defined('ABSPATH') or exit();

add_filter('post_gallery', 'slwsu_gallery', 10, 2);

function slwsu_gallery($output, $attr) {
    global $post, $wp_locale;

    // Gallery instance counter
    static $instance = 0;
    $instance++;

    // Validate the author's orderby attribute
    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby'])
            unset($attr['orderby']);
    }
    // Get attributes from shortcode
    extract(shortcode_atts(array(
        'order' => 'ASC',
        'orderby' => 'menu_order ID',
        'id' => $post->ID,
        'itemtag' => 'dl',
        'icontag' => 'dt',
        'captiontag' => 'dd',
        'columns' => isset($attr['columns']) ? $attr['columns'] : 4,
        'size' => isset($attr['size']) ? $attr['size'] : 'thumbnail',
        'include' => '',
        'exclude' => ''
                    ), $attr));

    // Initialize
    $id = intval($id);
    $attachments = array();
    if ($order == 'RAND')
        $orderby = 'none';

    if (!empty($include)) {

        // Include attribute is present
        $include = preg_replace('/[^0-9,]+/', '', $include);
        $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

        // Setup attachments array
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } else if (!empty($exclude)) {

        // Exclude attribute is present 
        $exclude = preg_replace('/[^0-9,]+/', '', $exclude);

        // Setup attachments array
        $attachments = get_children(array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    } else {
        // Setup attachments array
        $attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    }

    if (empty($attachments))
        return '';

    // Filter gallery differently for feeds
    if (is_feed()) {
        $output = "\n";
        foreach ($attachments as $att_id => $attachment)
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    // Filter tags and attributes
    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $columns = intval($columns);

    // $itemwidth = $columns > 0 ? floor(100 / $columns) : 100;
    $itemwidth = $columns > 0 ? (100 / $columns) : 100;

    $float = is_rtl() ? 'right' : 'left';
    $selector = "gallery-{$instance}";

    $inlineCss = get_option('slwsu_lazyload_post_gallery_styles', '');
    // Filter gallery CSS
    $output = apply_filters('gallery_style', "
		<style type='text/css'>
			#{$selector} {
                            width: 100%;
                            margin: 0 auto;
			}
			#{$selector} .gallery-item {
                            margin: 0;
                            padding: 0;
                            float: {$float};
                            text-align: center;
                            width: {$itemwidth}%;
			}
			#{$selector} img {
                            width: 100%;
                            padding: 5px 10px 5px 0;
                            margin: 0;
			}
			#{$selector} .gallery-caption {
                            margin-right: 10px;
                            margin-top: -5px;
			}
            {$inlineCss}
		</style>
		<!-- see gallery_shortcode() in wp-includes/media.php -->
		<div id='$selector' class='gallery lazyload-post-gallery galleryid-{$id}'>"
    );

    // Iterate through the attachments in this gallery instance
    $i = 0;
    foreach ($attachments as $id => $attachment) {
        // Attachment link
        /*
          $dataAtr = array(
          'class' => 'attachment-' . $size . ' size-' . $size . ' lazyload-post-gallery-lazy jslghtbx-thmb',
          'src' => plugins_url('assets/loader.gif', __FILE__),
          'data-src' => $img_src,
          'srcset' => plugins_url('assets/loader.gif', __FILE__),
          'data-srcset' => $img_srcset,
          'data-jslghtbx' => plugins_url('assets/loader.gif', __FILE__),
          'data-jslghtbx-caption' => 'Titre caption',
          'data-jslghtbx-group' => $selector
          );
          $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false, false, $dataAtr) : wp_get_attachment_link($id, $size, true, false, false, $dataAtr);
         */

        $img_src = wp_get_attachment_image_url($id, $size);
        $img_srcset = wp_get_attachment_image_srcset($id, $size);
        $attachment = get_post($id);
        $title = ('true' === get_option('slwsu_lazyload_post_gallery_add_carousel_title', 'false')) ? $attachment->post_title . '<br />' : '';
        $caption = ('true' === get_option('slwsu_lazyload_post_gallery_add_carousel_caption', 'false')) ? $attachment->post_excerpt . '<br />' : '';
        $description = ('true' === get_option('slwsu_lazyload_post_gallery_add_carousel_description', 'false')) ? $attachment->post_content : '';
        $full = wp_get_attachment_image_url($id, 'full');
        $loadingGif = plugins_url('assets/loader.gif', __FILE__);

        $img = ''
                . '<img '
                . 'class="attachment-' . $size . ' size-' . $size . ' lazyload-post-gallery-lazy jslghtbx-thmb" '
                . 'src="' . $loadingGif . '"'
                . 'data-src="' . $img_src . '" '
                . 'srcset="' . $loadingGif . '"'
                . 'data-srcset="' . $img_srcset . '"'
                . 'data-jslghtbx="' . $full . '" '
                . 'data-jslghtbx-caption="' . $title . $caption . $description . '" '
                . 'data-jslghtbx-group="' . $selector . '"'
                . '/>';

        // Start itemtag
        $output .= "<{$itemtag} class='gallery-item'>";

        // icontag
        $output .= "
		<{$icontag} class='gallery-icon effect-lily'>
			$img
		</{$icontag}>";

        if ('true' === get_option('slwsu_lazyload_post_gallery_add_gallery_title', 'false')):
            if ($captiontag && trim($attachment->post_title)) {
                // if ($captiontag && trim($attachment->post_excerpt)) {
                // captiontag
                $output .= "
                        <{$captiontag} class='gallery-caption'>
                                " . wptexturize($attachment->post_title) . "
                        </{$captiontag}>";
            }

        endif;

        // End itemtag
        $output .= "</{$itemtag}>";

        // Line breaks by columns set
        if ($columns > 0 && ++$i % $columns == 0)
            $output .= '<br style="clear: both">';
    }

    // End gallery output
    $output .= "
		<br style='clear: both;'>
	</div>\n";

    return $output;
}
