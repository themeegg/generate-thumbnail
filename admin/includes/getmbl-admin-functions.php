<?php

if (!function_exists('getmbl_ajax_thumbnail_generate_get_sizes')) {

    function getmbl_ajax_thumbnail_generate_get_sizes() {

        global $_wp_additional_image_sizes;

        foreach (get_intermediate_image_sizes() as $s) {

            $sizes[$s] = array(
                'name' => '',
                'width' => '',
                'height' => '',
                'crop' => FALSE
            );

            /* Read theme added sizes or fall back to default sizes set in options... */

            $sizes[$s]['name'] = $s;

            if (isset($_wp_additional_image_sizes[$s]['width'])) {
                $sizes[$s]['width'] = intval($_wp_additional_image_sizes[$s]['width']);
            } else {
                $sizes[$s]['width'] = get_option("{$s}_size_w");
            }

            if (isset($_wp_additional_image_sizes[$s]['height'])) {
                $sizes[$s]['height'] = intval($_wp_additional_image_sizes[$s]['height']);
            } else {
                $sizes[$s]['height'] = get_option("{$s}_size_h");
            }

            if (isset($_wp_additional_image_sizes[$s]['crop'])) {
                if (!is_array($sizes[$s]['crop'])) {
                    $sizes[$s]['crop'] = intval($_wp_additional_image_sizes[$s]['crop']);
                } else {
                    $sizes[$s]['crop'] = $_wp_additional_image_sizes[$s]['crop'];
                }
            } else {
                $sizes[$s]['crop'] = get_option("{$s}_crop");
            }
        }

        $sizes = apply_filters('intermediate_image_sizes_advanced', $sizes);

        return $sizes;
    }

}

if (!function_exists('getmbl_generate_attachment_metadata_custom')) {

    /**
     * Generate post thumbnail attachment meta data.
     *
     * @param int $attachment_id Attachment Id to process.
     * @param string $file Filepath of the Attached image.
     * @return mixed Metadata for attachment.
     */
    function getmbl_generate_attachment_metadata_custom($attachment_id, $file, $thumbnails = NULL) {
        $attachment = get_post($attachment_id);

        $metadata = array();
        if (preg_match('!^image/!', get_post_mime_type($attachment)) && file_is_displayable_image($file)) {
            $imagesize = getimagesize($file);
            $metadata['width'] = $imagesize[0];
            $metadata['height'] = $imagesize[1];
            list($uwidth, $uheight) = wp_constrain_dimensions($metadata['width'], $metadata['height'], 128, 96);
            $metadata['hwstring_small'] = sprintf("height='%s' width='%s'", $uheight, $uwidth);

            // Make the file path relative to the upload dir
            $metadata['file'] = _wp_relative_upload_path($file);

            $sizes = getmbl_ajax_thumbnail_generate_get_sizes();

            foreach ($sizes as $size => $size_data) {
                if (isset($thumbnails) && !in_array($size, $thumbnails)) {
                    $intermediate_size = image_get_intermediate_size($attachment_id, $size_data['name']);
                } else {
                    $intermediate_size = image_make_intermediate_size($file, $size_data['width'], $size_data['height'], $size_data['crop']);
                }

                if ($intermediate_size) {
                    $metadata['sizes'][$size] = $intermediate_size;
                }
            }

            // fetch additional metadata from exif/iptc
            $image_meta = wp_read_image_metadata($file);

            if ($image_meta) {
                $metadata['image_meta'] = $image_meta;
            }
        }

        return apply_filters('wp_generate_attachment_metadata', $metadata, $attachment_id);
    }

}