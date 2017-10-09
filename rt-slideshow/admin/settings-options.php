<?php
define('SLIDESHOW_SETTINGS', 'slideshow-settings');
class slideshow_settings_page {
    function slideshow_settings_page() {
        add_action('admin_menu', array(&$this, 'on_admin_menu'));
        add_action('admin_post_save_slideshow_settings', array(&$this, 'setting_on_save_changes'));
    }

    //extend the admin menu
    function on_admin_menu() {
        $theme_data = wp_get_theme();
       
        $this->pagehook = add_options_page( 'Slideshow Settings', 'Slideshow Settings', 'manage_options', SLIDESHOW_SETTINGS, array(&$this, 'on_show_page'));
        add_action('load-' . $this->pagehook, array(&$this, 'on_load_page'));
    }

    //will be executed if wordpress core detects this page has to be rendered
    function on_load_page() {
        //ensure, that the needed javascripts been loaded to allow drag/drop, expand/collapse and hide/show of boxes
        wp_enqueue_script('common');
        wp_enqueue_script('wp-lists');
        wp_enqueue_script('postbox');
        // scripts
        wp_enqueue_script(array(
                'jquery',
                'jquery-ui-core',
                'jquery-ui-tabs',
                'jquery-ui-sortable',
                'wp-color-picker',
                'thickbox',
                'media-upload',	
        ));


        // 3.5 media gallery
        if( function_exists('wp_enqueue_media') && !did_action( 'wp_enqueue_media' ))
        {
            wp_enqueue_media();
        }
        // styles
        wp_enqueue_style(array(
                'thickbox',
                'wp-color-picker',
        ));
        wp_enqueue_style( 'rt-admin-css', SLIDESHOW_PATH. 'admin/css/slide_admin.css' );
        wp_enqueue_script( 'rt-admin-js', SLIDESHOW_PATH. 'admin/js/slide_admin.js' );
        //add several metaboxes now, all metaboxes registered during load page can be switched off/on at "Screen Options" automatically, nothing special to do therefore
        add_meta_box('slideshow_settings', 'Slideshow Settings', array(&$this, 'slideshow_settings'), $this->pagehook, 'normal', 'core');
    }

    //executed to show the plugins complete admin page
    function on_show_page() {   
        global $screen_layout_columns;
        $data = array();
    ?>
        <div id="slideshow-settings-metaboxes" class="wrap">
            <?php screen_icon('options-general'); ?>
            <form action="admin-post.php" method="post">
                <?php wp_nonce_field('slideshow-settings-metaboxes'); ?>
                <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
                <?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>
                <input type="hidden" name="action" value="save_slideshow_settings" />
                <div id="poststuff" class="metabox-holder has-right-sidebar">
                    <div id="side-info-column" class="inner-sidebar">
                        <!-- Update -->
                        <div class="postbox">
                            <div class="inside">
                                <input type="hidden" name="HTTP_REFERER" value="<?php echo $_SERVER['HTTP_REFERER'] ?>" />
                                <input type="hidden" name="theme_options_nonce" value="<?php echo wp_create_nonce( 'input' ); ?>" />
                                <input type="submit" class="button button-primary button-large" value="Save Slider" />
                            </div>
                        </div>
                        <?php do_meta_boxes($this->pagehook, 'side', $data); ?>
                    </div>
                    <div id="post-body" class="has-sidebar">
                        <div id="post-body-content" class="has-sidebar-content">
                            <?php do_meta_boxes($this->pagehook, 'normal', $data); ?>
                            <?php do_meta_boxes($this->pagehook, 'additional', $data); ?>
                        </div>
                    </div>
                    <br class="clear"/>
                </div>
            </form>
        </div>
        <script type="text/javascript">
        //<![CDATA[
            jQuery(document).ready(function($) {
                // close postboxes that should be closed
                jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
            });
        //]]>
        </script>
        <?php
    }

    function setting_on_save_changes() {
        if (!current_user_can('manage_options'))
            wp_die(__('Cheatin&#8217; uh?'));
        //cross check the given referer
        check_admin_referer('slideshow-settings-metaboxes');
        //process here your on $_POST validation and / or option saving
        update_option('slideshow_settings',serialize($_POST['slider_image']));
        wp_redirect($_POST['_wp_http_referer']);
    }
    
    function slideshow_settings($data) {
        $slideshow_images = unserialize(get_option('slideshow_settings'));
        ?>
        <div id="rt-event_venue" class="field field_type-repeater rt-tab_group-show" data-field_name="event_venue" data-field_key="" data-field_type="repeater">
            <p class="label"><label for="rt-field-event_venue"><?php _e('Slider Images', 'rt-image-slideshow'); ?></label></p>
            <div class="repeater empty" data-min_rows="0" data-max_rows="999">
            <table class="widefat rt-input-table row_layout" data-input-name="slider_image">
            <tbody class="">
            <?php 
            $i= 1;
            if(!empty($slideshow_images)){ 
                foreach($slideshow_images as $slideshow_image){
                    if(!empty($slideshow_image)){
                    ?>
                        <tr class="row">
                            <td class="order"><?php echo $i; ?></td>
                            <td class="rt_input-wrap">
                                <table class="widefat rt_input">
                                    <tbody>
                                        <tr class="field sub_field field_type-image" data-field_type="image" data-field_key="" data-field_name="slider_image">
                                            <td class="label">
                                                <label><?php _e('Image', 'rt-image-slideshow'); ?></label>
                                                <span class="sub-field-instructions"></span>
                                            </td>
                                            <td>
                                                <div class="inner">
                                                    <div class="rt-image-uploader clearfix" data-preview_size="thumbnail" data-library="all">
                                                        <input class="rt-image-value" type="hidden" name="slider_image[]" value="<?php echo esc_url($slideshow_image); ?>">
                                                        <div class="my-image">
                                                            <div class="hover">
                                                                <ul class="bl">
                                                                    <li><a class="rt-button-delete ir" href="#"><?php _e('Remove','rt-image-slideshow') ?></a></li>
                                                                </ul>
                                                            </div>
                                                            <img class="rt-image-image" src="<?php echo $slideshow_image; ?>" alt="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td class="remove">
                                <a class="rt-button-add add-row-before" href="#" style="margin-top: -27.5px;"></a>
                                <a class="rt-button-remove" href="#"></a>
                            </td>
                        </tr>
                    <?php  $i++; } } }?>
                    <tr class="row-clone">
                        <td class="order"></td>
                        <td class="rt_input-wrap">
                            <table class="widefat rt_input">
                                <tbody>
                                    <tr class="field sub_field field_type-image" data-field_type="image" data-field_key="" data-field_name="slider_image">
                                        <td class="label">
                                            <label><?php _e('Image','rt-image-slideshow'); ?></label>
                                            <span class="sub-field-instructions"></span>
                                        </td>

                                        <td>
                                            <div class="inner">
                                                <div class="rt-image-uploader clearfix" data-preview_size="thumbnail" data-library="all">
                                                        <input class="rt-image-value" type="hidden" name="slider_image[]" value="">
                                                        <div class="has-image">
                                                            <div class="hover">
                                                                <ul class="bl">
                                                                    <li><a class="rt-button-delete ir" href="#"><?php _e('Remove','rt-image-slideshow'); ?></a></li>
                                                                </ul>
                                                            </div>
                                                            <img class="rt-image-image" src="" alt="">
                                                        </div>
                                                        <div class="no-image">
                                                            <p>
                                                                No image selected <input type="button" class="button add-image upload_image_button" value="Add Image">
                                                            </p>
                                                        </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="remove">
                            <a class="rt-button-add add-row-before" href="#" style="margin-top: -27.5px;"></a>
                            <a class="rt-button-remove" href="#"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <ul class="hl clearfix repeater-footer">
                <li class="right">
                    <a href="#" class="add-row-end button button-primary button-medium"><?php _e('Add Row','rt-image-slideshow'); ?></a>
                </li>
            </ul>
            </div>
        </div>
       <?php
    }
}

$subscribe_settings_page = new slideshow_settings_page();
$GLOBALS['slideshow_settings'] = unserialize(get_option('slideshow_settings'));

//end of test settings page