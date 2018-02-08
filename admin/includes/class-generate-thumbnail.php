<?php
if (!class_exists('ThemeEgg_Ajax_Thumbnail_Generate')) {

    class ThemeEgg_Ajax_Thumbnail_Generate {

        function __construct() {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_filter('attachment_fields_to_edit', array($this, 'attachment_fields_edit'), 10, 2);
        }

        function add_admin_menu() {
            add_management_page(esc_html__('Generate all Thumbnails', 'generate-thumbnail'), esc_html__('Generate Thumbnails', 'generate-thumbnail'), 'manage_options', 'themeegg-generate-thumbnail', array($this, 'add_management_related_page'));
        }

        /**
         * Add generate thumbnails button to the media page
         *
         * @param array $fields
         * @param object $post
         * @return array
         */
        function attachment_fields_edit($fields, $post) {

            $thumbnails = array();

            foreach (getmbl_ajax_thumbnail_generate_get_sizes() as $s) {
                $thumbnails[] = 'thumbnails[]=' . $s['name'];
            }

            $thumbnails = '&' . implode('&', $thumbnails);

            ob_start();
            ?>
            <script type="text/javascript">
                function teg_regtmbl_set_message(msg) {
                    jQuery("#atr-message").html(msg);
                    jQuery("#atr-message").show();
                }

                function regenerate() {
                    jQuery("#ajax_thumbnail_generate").prop("disabled", true);
                    teg_regtmbl_set_message("<?php esc_html_e('Reading attachments...', 'generate-thumbnail') ?>");
                    thumbnails = '<?php echo $thumbnails ?>';
                    jQuery.ajax({
                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                        type: "POST",
                        data: "action=ajax_thumbnail_generate&do=regen&id=<?php echo $post->ID ?>" + thumbnails,
                        success: function (result) {
                            if (result != '-1') {
                                teg_regtmbl_set_message("<?php esc_html_e('Done.', 'generate-thumbnail') ?>");
                            }
                        },
                        error: function (request, status, error) {
                            teg_regtmbl_set_message("<?php esc_html_e('Error', 'generate-thumbnail') ?>" + request.status);
                        },
                        complete: function () {
                            jQuery("#ajax_thumbnail_generate").prop("disabled", false);
                        }
                    });
                }
            </script>
            <input type='button' onclick='javascript:regenerate();' class='button' name='ajax_thumbnail_generate' id='ajax_thumbnail_generate' value='Generate Thumbnails'>
            <span id="atr-message" class="updated fade" style="clear:both;display:none;line-height:28px;padding-left:10px;"></span>
            <?php
            $html = ob_get_clean();

            $fields['generate-thumbnail'] = array(
                'label' => esc_html__('Ajax Thumbnail Generate', 'generate-thumbnail'),
                'input' => 'html',
                'html' => $html
            );

            return $fields;
        }

        function add_management_related_page() {
            ?>
            <div id="message" class="updated fade" style="display:none"></div>
            <script type="text/javascript">
                // <![CDATA[
                function teg_regtmbl_set_message(msg) {
                    jQuery("#message").html(msg);
                    jQuery("#message").show();
                }

                function regenerate() {
                    jQuery("#ajax_thumbnail_generate").prop("disabled", true);
                    teg_regtmbl_set_message("<p><?php esc_html_e('Reading attachments...', 'generate-thumbnail') ?></p>");

                    inputs = jQuery('input:checked');
                    var thumbnails = '';
                    if (inputs.length != jQuery('input[type=checkbox]').length) {
                        inputs.each(function () {
                            thumbnails += '&thumbnails[]=' + jQuery(this).val();
                        });
                    }

                    var onlyfeatured = jQuery("#onlyfeatured").prop('checked') ? 1 : 0;

                    jQuery.ajax({
                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                        type: "POST",
                        data: "action=ajax_thumbnail_generate&do=getlist&onlyfeatured=" + onlyfeatured,
                        success: function (result) {
                            var list = eval(result);
                            var curr = 0;

                            if (!list) {
                                teg_regtmbl_set_message("<?php esc_html_e('No attachments found.', 'generate-thumbnail') ?>");
                                jQuery("#ajax_thumbnail_generate").prop("disabled", false);
                                return;
                            }

                            function regenItem() {
                                if (curr >= list.length) {
                                    jQuery("#ajax_thumbnail_generate").prop("disabled", false);
                                    teg_regtmbl_set_message("<?php esc_html_e('Done.', 'generate-thumbnail') ?>");
                                    return;
                                }

                                teg_regtmbl_set_message('<?php printf(__('Generateing %s of %s (%s)...', 'generate-thumbnail'), "' + (curr + 1) + '", "' + list.length + '", "' + list[curr].title + '"); ?>');

                                jQuery.ajax({
                                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                    type: "POST",
                                    data: "action=ajax_thumbnail_generate&do=regen&id=" + list[curr].id + thumbnails,
                                    success: function (result) {
                                        curr = curr + 1;
                                        if (result != '-1') {
                                            jQuery("#thumb").show();
                                            jQuery("#thumb-img").attr("src", result);
                                        }
                                        regenItem();
                                    }
                                });
                            }

                            regenItem();
                        },
                        error: function (request, status, error) {
                            teg_regtmbl_set_message("<?php esc_html_e('Error', 'generate-thumbnail') ?>" + request.status);
                        }
                    });
                }

                jQuery(document).ready(function () {
                    jQuery('#size-toggle').click(function () {
                        jQuery("#sizeselect").find("input[type=checkbox]").each(function () {
                            jQuery(this).prop("checked", !jQuery(this).prop("checked"));
                        });
                    });
                });

                // ]]>
            </script>

            <form method="post" action="" style="display:inline; float:left; padding-right:30px;">
                <h4><?php esc_html_e('Select which thumbnails you want to generate', 'generate-thumbnail'); ?>:</h4>
                <a href="javascript:void(0);" id="size-toggle"><?php esc_html_e('Toggle all', 'generate-thumbnail'); ?></a>

                <ul id="sizeselect">

                    <?php foreach (getmbl_ajax_thumbnail_generate_get_sizes() as $image_size) : ?>
                        <li>
                            <label>
                                <input type="checkbox" name="thumbnails[]" id="sizeselect" checked="checked" value="<?php echo $image_size['name'] ?>" />
                                <?php
                                $crop_setting = '';

                                if ($image_size['crop']) {
                                    if (is_array($image_size['crop'])) {
                                        $crop_setting = sprintf('%s, %s', $image_size['crop'][0], $image_size['crop']['1']);
                                    } else {
                                        $crop_setting = ' ' . __('cropped', 'generate-thumbnail');
                                    }
                                }

                                printf('<em>%s</em> (%sx%s%s)', $image_size['name'], $image_size['width'], $image_size['height'], $crop_setting);
                                ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p>
                    <label>
                        <input type="checkbox" id="onlyfeatured" name="onlyfeatured" />
                        <?php esc_html_e('Only generate featured images', 'generate-thumbnail'); ?>
                    </label>
                </p>
                <p><?php esc_html_e('Note: If you\'ve changed the dimensions of your thumbnails, existing thumbnail images will not be deleted.', 'generate-thumbnail');
                        ?></p>
                <input type="button" onClick="javascript:regenerate();" class="button" name="ajax_thumbnail_generate" id="ajax_thumbnail_generate" value="<?php esc_html_e('Generate All Thumbnails', 'generate-thumbnail') ?>" />
                <br />
            </form>
            <div id="thumb" style="display:none;"><h4><?php esc_html_e('Thumbnail of', 'generate-thumbnail'); ?>:</h4><img id="thumb-img" /></div>
            <p style="clear:both; padding-top:2em;">
                <?php printf(__('If you find this plugin useful, I\'d be happy to read your comments on the %splugin homepage%s. If you experience any problems, feel free to leave a comment too.', 'generate-thumbnail'), '<a href="https://wordpress.org/plugins/generate-thumbnail" target="_blank">', '</a>'); ?>
            </p>
            <?php
        }

    }

}