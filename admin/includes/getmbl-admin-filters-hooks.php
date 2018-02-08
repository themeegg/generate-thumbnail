<?php

if (!function_exists('getmbl_ajax_thumbnail_generate_ajax')) {

    function getmbl_ajax_thumbnail_generate_ajax() {

        global $wpdb;
        $action = $_POST["do"];
        $thumbnails = isset($_POST['thumbnails']) ? $_POST['thumbnails'] : NULL;
        $onlyfeatured = isset($_POST['onlyfeatured']) ? $_POST['onlyfeatured'] : 0;

        if ($action == "getlist") {
            
            $res = array();

            if ($onlyfeatured) {
                /* Get all featured images */
                $featured_images = $wpdb->get_results("SELECT meta_value, {$wpdb->posts}.post_title AS title FROM {$wpdb->postmeta}, {$wpdb->posts} WHERE meta_key = '_thumbnail_id' AND {$wpdb->postmeta}.post_id={$wpdb->posts}.ID ORDER BY post_date DESC");

                foreach ($featured_images as $image) {
                    $res[] = array(
                        'id' => $image->meta_value,
                        'title' => $image->title
                    );
                }
            } else {
                $attachments = get_children(array(
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'numberposts' => -1,
                    'post_status' => null,
                    'post_parent' => null, // any parent
                    'output' => 'object',
                    'orderby' => 'post_date',
                    'order' => 'desc'
                ));

                foreach ($attachments as $attachment) {
                    $res[] = array(
                        'id' => $attachment->ID,
                        'title' => $attachment->post_title
                    );
                }
            }

            die(json_encode($res));
        } else if ($action == "regen") {
            $id = $_POST["id"];

            $fullsizepath = get_attached_file($id);

            if (FALSE !== $fullsizepath && @file_exists($fullsizepath)) {
                set_time_limit(30);
                wp_update_attachment_metadata($id, getmbl_generate_attachment_metadata_custom($id, $fullsizepath, $thumbnails));

                die(wp_get_attachment_thumb_url($id));
            }

            die('-1');
        }
    }

}

add_action('wp_ajax_ajax_thumbnail_generate', 'getmbl_ajax_thumbnail_generate_ajax');

add_action('plugins_loaded', create_function('', 'global $ThemeEgg_Ajax_Thumbnail_Generate; $ThemeEgg_Ajax_Thumbnail_Generate = new ThemeEgg_Ajax_Thumbnail_Generate();'));
