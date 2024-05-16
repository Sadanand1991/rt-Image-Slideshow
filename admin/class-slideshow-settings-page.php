<?php
/**
 * SLIDESHOW_SETTINGS
 *
 * @package slideshow-settings
 */

define( 'SLIDESHOW_SETTINGS', 'slideshow-settings' );

/**
 * Slideshow settings page class.
 */
class Slideshow_Settings_Page {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'on_admin_menu' ) );
		add_action( 'admin_post_save_slideshow_settings', array( $this, 'setting_on_save_changes' ) );
	}

	/**
	 * Extend the admin menu.
	 */
	public function on_admin_menu() {
		$this->pagehook = add_options_page( 'Slideshow Settings', 'Slideshow Settings', 'manage_options', SLIDESHOW_SETTINGS, array( $this, 'on_show_page' ) );
		add_action( 'load-' . $this->pagehook, array( $this, 'on_load_page' ) );
	}

	/**
	 * Load admin page.
	 */
	public function on_load_page() {
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );

		wp_enqueue_script(
			array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-tabs',
				'jquery-ui-sortable',
				'wp-color-picker',
				'thickbox',
				'media-upload',
			)
		);

		if ( function_exists( 'wp_enqueue_media' ) && ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}

		wp_enqueue_style(
			array(
				'thickbox',
				'wp-color-picker',
			)
		);
		wp_enqueue_style( 'rt-admin-css', SLIDESHOW_PATH . 'admin/css/slide_admin.css', array(), '1.0.0' );
		wp_enqueue_script( 'rt-admin-js', SLIDESHOW_PATH . 'admin/js/slide_admin.js', array(), '1.0.0', true );

		add_meta_box( 'slideshow_settings', 'Slideshow Settings', array( $this, 'slideshow_settings' ), $this->pagehook, 'normal', 'core' );
	}

	/**
	 * Show admin page.
	 */
	public function on_show_page() {
		$data = array();
		?>
		<div id="slideshow-settings-metaboxes" class="wrap">
			<?php echo esc_html( get_admin_page_title() ); ?>
			<form action="admin-post.php" method="post">
				<?php wp_nonce_field( 'slideshow-settings-metaboxes' ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
				<input type="hidden" name="action" value="save_slideshow_settings" />
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div id="side-info-column" class="inner-sidebar">
						<div class="postbox">
							<div class="inside">
								<input type="hidden" name="HTTP_REFERER" value="<?php echo esc_url( wp_get_referer() ); ?>" />
								<input type="hidden" name="theme_options_nonce" value="<?php echo esc_attr( wp_create_nonce( 'input' ) ); ?>" />
								<input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Save Slider', 'rt-image-slideshow' ); ?>" />
							</div>
						</div>
						<?php do_meta_boxes( $this->pagehook, 'side', $data ); ?>
					</div>
					<div id="post-body" class="has-sidebar">
						<div id="post-body-content" class="has-sidebar-content">
							<?php do_meta_boxes( $this->pagehook, 'normal', $data ); ?>
							<?php do_meta_boxes( $this->pagehook, 'additional', $data ); ?>
						</div>
					</div>
					<br class="clear"/>
				</div>
			</form>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				postboxes.add_postbox_toggles('<?php echo esc_js( $this->pagehook ); ?>');
			});
		</script>
		<?php
	}

	/**
	 * Save settings.
	 */
	public function setting_on_save_changes() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Cheatin&#8217; uh?', 'rt-image-slideshow' ) );
		}

		check_admin_referer( 'slideshow-settings-metaboxes' );
		// First, check if the $_POST['slider_image'] index exists.
		if ( isset( $_POST['slider_image'] ) ) {
			$slider_image_data = isset( $_POST['slider_image'] ) ? wp_unslash( $_POST['slider_image'] ) : array();
			update_option( 'slideshow_settings', wp_json_encode( $slider_image_data ) );
		}
		wp_safe_redirect( esc_url_raw( wp_get_referer() ) );
		exit;
	}

	/**
	 * Display slideshow settings.
	 */
	public function slideshow_settings() {
		$slideshow_images = json_decode( get_option( 'slideshow_settings' ), true );
		?>
		<div id="rt-event_venue" class="field field_type-repeater rt-tab_group-show" data-field_name="event_venue" data-field_key="" data-field_type="repeater">
			<p class="label"><label for="rt-field-event_venue"><?php esc_html_e( 'Slider Images', 'rt-image-slideshow' ); ?></label></p>
			<div class="repeater empty" data-min_rows="0" data-max_rows="999">
				<table class="widefat rt-input-table row_layout" data-input-name="slider_image">
					<tbody class="">
					<?php
					$i = 1;
					if ( ! empty( $slideshow_images ) ) {
						foreach ( $slideshow_images as $slideshow_image ) {
							if ( ! empty( $slideshow_image ) ) {
								?>
								<tr class="row">
									<td class="order"><?php echo esc_html( $i ); ?></td>
									<td class="rt_input-wrap">
										<table class="widefat rt_input">
											<tbody>
												<tr class="field sub_field field_type-image" data-field_type="image" data-field_key="" data-field_name="slider_image">
													<td class="label">
														<label><?php esc_html_e( 'Image', 'rt-image-slideshow' ); ?></label>
														<span class="sub-field-instructions"></span>
													</td>
													<td>
														<div class="inner">
															<div class="rt-image-uploader clearfix" data-preview_size="thumbnail" data-library="all">
																<input class="rt-image-value" type="hidden" name="slider_image[]" value="<?php echo esc_url( $slideshow_image ); ?>">
																<div class="my-image">
																	<div class="hover">
																		<ul class="bl">
																			<li><a class="rt-button-delete ir" href="#"><?php esc_html_e( 'Remove', 'rt-image-slideshow' ); ?></a></li>
																		</ul>
																	</div>
																	<img class="rt-image-image" src="<?php echo esc_url( $slideshow_image ); ?>" alt="">
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
								<?php
								++$i;
							}
						}
					}
					?>
					<tr class="row-clone">
						<td class="order"></td>
						<td class="rt_input-wrap">
							<table class="widefat rt_input">
								<tbody>
									<tr class="field sub_field field_type-image" data-field_type="image" data-field_key="" data-field_name="slider_image">
										<td class="label">
											<label><?php esc_html_e( 'Image', 'rt-image-slideshow' ); ?></label>
											<span class="sub-field-instructions"></span>
										</td>

										<td>
											<div class="inner">
												<div class="rt-image-uploader clearfix" data-preview_size="thumbnail" data-library="all">
														<input class="rt-image-value" type="hidden" name="slider_image[]" value="">
														<div class="has-image">
															<div class="hover">
																<ul class="bl">
																	<li><a class="rt-button-delete ir" href="#"><?php esc_html_e( 'Remove', 'rt-image-slideshow' ); ?></a></li>
																</ul>
															</div>
															<img class="rt-image-image" src="" alt="">
														</div>
														<div class="no-image">
															<p>
																<?php esc_html_e( 'No image selected', 'rt-image-slideshow' ); ?> <input type="button" class="button add-image upload_image_button" value="<?php esc_attr_e( 'Add Image', 'rt-image-slideshow' ); ?>">
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
					<a href="#" class="add-row-end button button-primary button-medium"><?php esc_html_e( 'Add Row', 'rt-image-slideshow' ); ?></a>
				</li>
			</ul>
			</div>
		</div>
		<?php
	}
}

$subscribe_settings_page       = new Slideshow_Settings_Page();
$GLOBALS['slideshow_settings'] = json_decode( get_option( 'slideshow_settings' ) );
